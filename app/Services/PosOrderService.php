<?php

namespace App\Services;

use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Repositories\PosOrderRepository;
use App\Repositories\MenuItemRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PosOrderService
{
    public function __construct(
        protected PosOrderRepository $repository,
        protected MenuItemRepository $menuItemRepository
    ) {}

    public function find(int $id): ?PosOrder
    {
        return $this->repository->find($id);
    }

    public function openByOutlet(int $outletId): Collection
    {
        return $this->repository->openByOutlet($outletId);
    }

    public function kitchenPending(): Collection
    {
        return $this->repository->kitchenPending();
    }

    public function paginateByOutlet(int $outletId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginateByOutlet($outletId, $perPage);
    }

    /** Completed orders for report (date range, optional outlet). Returns [orders, revenue, cost, profit]. */
    public function getCompletedOrdersReport(Carbon $from, Carbon $to, ?int $outletId = null): array
    {
        $orders = $this->repository->getCompletedOrdersForReport($from, $to, $outletId);
        $revenue = $orders->sum('total');
        $cost = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $cost += ($item->unit_cost ?? 0) * $item->quantity;
            }
        }
        $profit = $revenue - $cost;
        return [
            'orders' => $orders,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
        ];
    }

    public function create(int $outletId, ?int $posTableId, int $userId): PosOrder
    {
        return DB::transaction(function () use ($outletId, $posTableId, $userId) {
            $order = $this->repository->create([
                'outlet_id' => $outletId,
                'pos_table_id' => $posTableId,
                'order_number' => $this->repository->generateOrderNumber(),
                'status' => PosOrder::STATUS_OPEN,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
                'served_by' => $userId,
            ]);
            return $order->fresh();
        });
    }

    public function addItem(PosOrder $order, int $menuItemId, int $quantity = 1, ?string $notes = null): PosOrderItem
    {
        $item = $this->menuItemRepository->find($menuItemId);
        if (!$item) {
            throw new \InvalidArgumentException('Menu item not found.');
        }
        $unitPrice = $item->price;
        $unitCost = $item->cost ?? 0;
        $total = $unitPrice * $quantity;

        $orderItem = $order->items()->create([
            'menu_item_id' => $menuItemId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
            'total' => $total,
            'status' => PosOrderItem::STATUS_PENDING,
            'notes' => $notes,
        ]);

        $this->recalculateOrderTotals($order);
        return $orderItem->fresh();
    }

    public function removeItem(PosOrder $order, int $orderItemId): void
    {
        $order->items()->where('id', $orderItemId)->delete();
        $this->recalculateOrderTotals($order);
    }

    public function sendToKitchen(PosOrder $order): PosOrder
    {
        $order->items()->where('status', PosOrderItem::STATUS_PENDING)->update(['status' => PosOrderItem::STATUS_SENT_TO_KITCHEN]);
        $order->update(['status' => PosOrder::STATUS_SENT_TO_KITCHEN]);
        return $order->fresh();
    }

    public function markItemReady(PosOrderItem $item): PosOrderItem
    {
        $item->update(['status' => PosOrderItem::STATUS_READY]);
        $order = $item->posOrder;
        if ($order->items()->whereNotIn('status', [PosOrderItem::STATUS_READY])->count() === 0) {
            $order->update(['status' => PosOrder::STATUS_READY]);
        }
        return $item->fresh();
    }

    public function completeOrder(PosOrder $order): PosOrder
    {
        $order->update(['status' => PosOrder::STATUS_COMPLETED, 'completed_at' => now()]);
        if ($order->pos_table_id) {
            $order->posTable?->update(['status' => \App\Models\PosTable::STATUS_AVAILABLE]);
        }
        return $order->fresh();
    }

    public function cancelOrder(PosOrder $order): PosOrder
    {
        $order->update(['status' => PosOrder::STATUS_CANCELLED]);
        if ($order->pos_table_id) {
            $order->posTable?->update(['status' => \App\Models\PosTable::STATUS_AVAILABLE]);
        }
        return $order->fresh();
    }

    protected function recalculateOrderTotals(PosOrder $order): void
    {
        $subtotal = $order->items()->sum('total');
        $tax = 0;
        $order->update(['subtotal' => $subtotal, 'tax_amount' => $tax, 'total' => $subtotal + $tax]);
    }
}
