<?php

namespace App\Repositories;

use App\Models\PosOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PosOrderRepository
{
    public function __construct(protected PosOrder $model) {}

    public function find(int $id): ?PosOrder
    {
        return $this->model->with(['items.menuItem', 'posTable', 'outlet'])->find($id);
    }

    public function openByOutlet(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['items.menuItem', 'posTable'])
            ->where('outlet_id', $outletId)
            ->whereIn('status', [PosOrder::STATUS_OPEN, PosOrder::STATUS_SENT_TO_KITCHEN, PosOrder::STATUS_PREPARING, PosOrder::STATUS_READY])
            ->orderByDesc('created_at')
            ->get();
    }

    public function kitchenPending(): Collection
    {
        return $this->model->newQuery()
            ->with(['items' => fn ($q) => $q->whereIn('status', ['pending', 'sent_to_kitchen', 'preparing']), 'items.menuItem', 'outlet', 'posTable'])
            ->whereIn('status', [PosOrder::STATUS_OPEN, PosOrder::STATUS_SENT_TO_KITCHEN, PosOrder::STATUS_PREPARING, PosOrder::STATUS_READY])
            ->orderBy('created_at')
            ->get();
    }

    public function paginateByOutlet(int $outletId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['posTable', 'servedBy'])
            ->where('outlet_id', $outletId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): PosOrder
    {
        return $this->model->create($data);
    }

    public function update(PosOrder $order, array $data): PosOrder
    {
        $order->update($data);
        return $order->fresh();
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'POS-' . date('Ymd');
        $last = $this->model->where('order_number', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq = $last ? (int) substr($last->order_number, -4) + 1 : 1;
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /** Completed orders for date range (by completed_at), optional outlet filter. */
    public function getCompletedOrdersForReport(Carbon $from, Carbon $to, ?int $outletId = null): Collection
    {
        $q = $this->model->newQuery()
            ->with(['items.menuItem', 'outlet', 'posTable'])
            ->where('status', PosOrder::STATUS_COMPLETED)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderByDesc('completed_at');
        if ($outletId !== null) {
            $q->where('outlet_id', $outletId);
        }
        return $q->get();
    }
}
