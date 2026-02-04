<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    public function __construct(
        protected Payment $model
    ) {}

    public function find(int $id): ?Payment
    {
        return $this->model->with(['invoice', 'booking', 'receivedBy'])->find($id);
    }

    public function getByInvoice(int $invoiceId): Collection
    {
        return $this->model->where('invoice_id', $invoiceId)->orderByDesc('payment_date')->get();
    }

    public function getByBooking(int $bookingId): Collection
    {
        return $this->model->where('booking_id', $bookingId)->with('invoice')->orderByDesc('payment_date')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['invoice', 'booking'])
            ->orderByDesc('payment_date')
            ->paginate($perPage);
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);
        return $payment->fresh();
    }

    public function delete(Payment $payment): bool
    {
        return $payment->delete();
    }
}
