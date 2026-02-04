<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    public function __construct(
        protected Invoice $model
    ) {}

    public function find(int $id): ?Invoice
    {
        return $this->model->with(['booking', 'guest', 'items', 'payments'])->find($id);
    }

    public function findByNumber(string $invoiceNumber): ?Invoice
    {
        return $this->model->where('invoice_number', $invoiceNumber)->first();
    }

    public function getByBooking(int $bookingId): Collection
    {
        return $this->model->where('booking_id', $bookingId)->with('items')->orderByDesc('created_at')->get();
    }

    public function getByGuest(int $guestId): Collection
    {
        return $this->model->where('guest_id', $guestId)->with('booking')->orderByDesc('invoice_date')->get();
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['guest', 'booking'])->orderByDesc('invoice_date');
        if ($status !== null) {
            $q->where('status', $status);
        }
        return $q->paginate($perPage);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->fresh();
    }

    public function delete(Invoice $invoice): bool
    {
        return $invoice->delete();
    }
}
