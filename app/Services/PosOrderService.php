<?php namespace App\Services;

use App\Models\Payment;
use App\Models\PosActionLog;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Repositories\PosOrderRepository;
use App\Repositories\MenuItemRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosOrderService
{
    public function __construct(
        protected PosOrderRepository $repository,
        protected MenuItemRepository $menuItemRepository,
        protected InvoiceService $invoiceService
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

    /** Completed orders for report (date range, optional outlet). Returns [orders, revenue, cost, profit]. Revenue = paid + posted_to_room only. */
    public function getCompletedOrdersReport(Carbon $from, Carbon $to, ?int $outletId = null): array
    {
        $orders = $this->repository->getCompletedOrdersForReport($from, $to, $outletId);
        $revenue = $orders->whereIn('payment_status', [PosOrder::PAYMENT_STATUS_PAID, PosOrder::PAYMENT_STATUS_POSTED_TO_ROOM])->sum('total');
        // Backward compatibility: orders with null payment_status (legacy) count as paid
        $revenue += $orders->whereNull('payment_status')->sum('total');
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

    public function create(int $outletId, ?int $posTableId, int $userId, ?int $guestId = null, ?int $bookingId = null, string $posType = PosOrder::POS_TYPE_RESTAURANT): PosOrder
    {
        return DB::transaction(function () use ($outletId, $posTableId, $userId, $guestId, $bookingId, $posType) {
            $order = $this->repository->create([
                'outlet_id' => $outletId,
                'pos_table_id' => $posTableId,
                'order_number' => $this->repository->generateOrderNumber(),
                'status' => PosOrder::STATUS_OPEN,
                'payment_status' => PosOrder::PAYMENT_STATUS_PENDING,
                'pos_type' => $posType,
                'guest_id' => $guestId,
                'booking_id' => $bookingId,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
                'served_by' => $userId,
            ]);
            $this->logAction($order->id, PosActionLog::ACTION_CREATE, ['guest_id' => $guestId, 'booking_id' => $bookingId, 'pos_type' => $posType]);
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
        $this->logAction($order->id, PosActionLog::ACTION_ADD_ITEM, ['menu_item_id' => $menuItemId, 'quantity' => $quantity]);
        return $orderItem->fresh();
    }

    public function removeItem(PosOrder $order, int $orderItemId): void
    {
        $order->items()->where('id', $orderItemId)->delete();
        $this->recalculateOrderTotals($order);
        $this->logAction($order->id, PosActionLog::ACTION_REMOVE_ITEM, ['order_item_id' => $orderItemId]);
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
        if (in_array($order->payment_status, [PosOrder::PAYMENT_STATUS_PAID, PosOrder::PAYMENT_STATUS_POSTED_TO_ROOM, PosOrder::PAYMENT_STATUS_VOIDED], true)) {
            return $order->fresh();
        }
        $order->update(['status' => PosOrder::STATUS_COMPLETED, 'completed_at' => now()]);
        if ($order->pos_table_id) {
            $order->posTable?->update(['status' => \App\Models\PosTable::STATUS_AVAILABLE]);
        }
        $this->logAction($order->id, PosActionLog::ACTION_COMPLETE, []);
        return $order->fresh();
    }

    public function payOrder(PosOrder $order, array $payments): PosOrder
    {
        if ($order->payment_status !== PosOrder::PAYMENT_STATUS_PENDING) {
            throw ValidationException::withMessages(['order' => __('Order is already paid or posted to room.')]);
        }
        $totalPaid = array_sum(array_column($payments, 'amount'));
        if (abs((float) $totalPaid - (float) $order->total) > 0.01) {
            throw ValidationException::withMessages(['order' => __('Total payment must match order total.')]);
        }
        return DB::transaction(function () use ($order, $payments) {
            $seq = 1;
            foreach ($payments as $p) {
                $paymentNumber = 'POS-' . $order->order_number . '-' . $seq;
                Payment::create([
                    'payment_number' => $paymentNumber,
                    'invoice_id' => null,
                    'booking_id' => $order->booking_id,
                    'pos_order_id' => $order->id,
                    'amount' => (float) $p['amount'],
                    'method' => $p['method'] ?? Payment::METHOD_CASH,
                    'reference' => $p['reference'] ?? null,
                    'status' => Payment::STATUS_COMPLETED,
                    'payment_date' => now(),
                    'received_by' => auth()->id(),
                ]);
                $seq++;
            }
            $order->update(['payment_status' => PosOrder::PAYMENT_STATUS_PAID]);
            $this->logAction($order->id, PosActionLog::ACTION_PAY, ['payments' => $payments]);
            return $order->fresh();
        });
    }

    public function postOrderToRoom(PosOrder $order): PosOrder
    {
        if ($order->payment_status !== PosOrder::PAYMENT_STATUS_PENDING) {
            throw ValidationException::withMessages(['order' => __('Order is already paid or posted to room.')]);
        }
        if (!$order->booking_id) {
            throw ValidationException::withMessages(['order' => __('Select a booking to post to room.')]);
        }
        $booking = $order->booking;
        if (!$booking || !in_array($booking->status, [\App\Models\Booking::STATUS_CHECKED_IN, \App\Models\Booking::STATUS_CONFIRMED], true)) {
            throw ValidationException::withMessages(['order' => __('Booking must be checked in to post to room.')]);
        }
        return DB::transaction(function () use ($order) {
            $invoice = $this->invoiceService->getOrCreateOpenInvoiceForBooking($order->booking);
            
            // Add each item individually to the invoice for better detail
            foreach ($order->items()->with('menuItem')->get() as $item) {
                $itemDesc = $item->menuItem->name . ($item->quantity > 1 ? ' (x' . $item->quantity . ')' : '');
                $this->invoiceService->addPosCharge($invoice, $itemDesc, (float) $item->total);
            }

            $order->update([
                'payment_status' => PosOrder::PAYMENT_STATUS_POSTED_TO_ROOM,
                'invoice_id' => $invoice->id,
            ]);
            $this->logAction($order->id, PosActionLog::ACTION_POST_TO_ROOM, ['invoice_id' => $invoice->id]);
            return $order->fresh();
        });
    }

    public function voidOrder(PosOrder $order): PosOrder
    {
        if (!$order->canVoid()) {
            throw ValidationException::withMessages(['order' => __('Order is already voided.')]);
        }
        if ($order->isPaid()) {
            throw ValidationException::withMessages(['order' => __('Cannot void a paid order. Refund via payments if needed.')]);
        }
        $order->update([
            'payment_status' => PosOrder::PAYMENT_STATUS_VOIDED,
            'status' => PosOrder::STATUS_CANCELLED,
        ]);
        if ($order->pos_table_id) {
            $order->posTable?->update(['status' => \App\Models\PosTable::STATUS_AVAILABLE]);
        }
        $this->logAction($order->id, PosActionLog::ACTION_VOID, []);
        return $order->fresh();
    }

    protected function logAction(?int $posOrderId, string $action, array $details): void
    {
        PosActionLog::create([
            'pos_order_id' => $posOrderId,
            'user_id' => auth()->id(),
            'action' => $action,
            'details' => $details,
        ]);
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