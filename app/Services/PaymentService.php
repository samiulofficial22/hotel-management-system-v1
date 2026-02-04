<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $repository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,completed,failed,refunded,cancelled'],
            'payment_date' => ['required', 'date'],
            'received_by' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function find(int $id): ?Payment
    {
        return $this->repository->find($id);
    }

    public function getByInvoice(int $invoiceId): Collection
    {
        return $this->repository->getByInvoice($invoiceId);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Record payment and update invoice paid_amount & balance_due. Partial payment supported. */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            $amount = (float) $data['amount'];
            if ($amount > (float) $invoice->balance_due) {
                throw ValidationException::withMessages(['amount' => ['Amount exceeds balance due.']]);
            }
            $data['payment_number'] = $this->generatePaymentNumber();
            $data['status'] = $data['status'] ?? Payment::STATUS_COMPLETED;
            $data['booking_id'] = $data['booking_id'] ?? $invoice->booking_id;
            $payment = $this->repository->create($data);

            $newPaid = (float) $invoice->paid_amount + $amount;
            $newBalance = max(0, (float) $invoice->total_amount - $newPaid);
            $status = $newBalance <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIALLY_PAID;
            $invoice->update([
                'paid_amount' => $newPaid,
                'balance_due' => $newBalance,
                'status' => $status,
            ]);

            return $payment->fresh();
        });
    }

    /** Refund: create negative payment or mark existing as refunded; adjust invoice. */
    public function refund(Payment $payment, string $notes = ''): Payment
    {
        return DB::transaction(function () use ($payment, $notes) {
            $payment->update(['status' => Payment::STATUS_REFUNDED, 'notes' => ($payment->notes ?? '') . "\nRefund: " . $notes]);
            $invoice = $payment->invoice;
            $newPaid = max(0, (float) $invoice->paid_amount - (float) $payment->amount);
            $newBalance = (float) $invoice->total_amount - $newPaid;
            $invoice->update([
                'paid_amount' => $newPaid,
                'balance_due' => $newBalance,
                'status' => $newBalance <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIALLY_PAID,
            ]);
            return $payment->fresh();
        });
    }

    public function delete(Payment $payment): bool
    {
        return $this->repository->delete($payment);
    }

    protected function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $last = Payment::withTrashed()->whereDate('created_at', today())->orderByDesc('id')->first();
        $seq = $last ? ((int) substr(preg_replace('/\D/', '', $last->payment_number), -4)) + 1 : 1;
        return $prefix . $date . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
