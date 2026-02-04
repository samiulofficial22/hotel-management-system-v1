<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepository $repository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'guest_id' => ['required', 'exists:guests,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function find(int $id): ?Invoice
    {
        return $this->repository->find($id);
    }

    public function getByBooking(int $bookingId): Collection
    {
        return $this->repository->getByBooking($bookingId);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $status);
    }

    /** Create invoice from booking: room charge + optional late checkout. Auto-calc tax and balance. */
    public function createFromBooking(Booking $booking, float $discountAmount = 0, float $taxRate = 0): Invoice
    {
        return DB::transaction(function () use ($booking, $discountAmount, $taxRate) {
            $subtotal = (float) $booking->room_rate + (float) $booking->late_checkout_fee;
            $taxAmount = $taxRate > 0 ? round($subtotal * ($taxRate / 100), 2) : 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount;
            $invoice = $this->repository->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'booking_id' => $booking->id,
                'guest_id' => $booking->guest_id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_due' => $totalAmount,
                'status' => Invoice::STATUS_ISSUED,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
            ]);

            $this->addRoomChargeItem($invoice, $booking);
            if ((float) $booking->late_checkout_fee > 0) {
                $this->addItem($invoice, 'Late checkout fee', 'late_checkout', 1, (float) $booking->late_checkout_fee);
            }
            return $invoice->fresh();
        });
    }

    public function addRoomChargeItem(Invoice $invoice, Booking $booking): InvoiceItem
    {
        $amount = (float) $booking->room_rate;
        return InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Room charge - ' . $booking->room->number . ' (' . $booking->check_in_date->format('M d') . ' - ' . $booking->check_out_date->format('M d') . ')',
            'item_type' => 'room',
            'quantity' => 1,
            'unit_price' => $amount,
            'amount' => $amount,
            'bookable_id' => $booking->id,
            'bookable_type' => Booking::class,
        ]);
    }

    public function addItem(Invoice $invoice, string $description, string $itemType, float $quantity, float $unitPrice): InvoiceItem
    {
        $amount = round($quantity * $unitPrice, 2);
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $description,
            'item_type' => $itemType,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'amount' => $amount,
        ]);
        $this->recalculateInvoice($invoice);
        return $item;
    }

    protected function recalculateInvoice(Invoice $invoice): void
    {
        $subtotal = $invoice->items()->sum('amount');
        $discount = (float) $invoice->discount_amount;
        $taxRate = (float) $invoice->tax_rate;
        $taxAmount = $taxRate > 0 ? round(($subtotal - $discount) * ($taxRate / 100), 2) : 0;
        $total = $subtotal - $discount + $taxAmount;
        $balanceDue = $total - (float) $invoice->paid_amount;
        $status = $balanceDue <= 0 ? Invoice::STATUS_PAID : ($invoice->paid_amount > 0 ? Invoice::STATUS_PARTIALLY_PAID : Invoice::STATUS_ISSUED);
        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'balance_due' => max(0, $balanceDue),
            'status' => $status,
        ]);
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $last = Invoice::withTrashed()->whereDate('created_at', today())->orderByDesc('id')->first();
        $seq = $last ? ((int) substr(preg_replace('/\D/', '', $last->invoice_number), -4)) + 1 : 1;
        return $prefix . $date . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
