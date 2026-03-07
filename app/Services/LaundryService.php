<?php

namespace App\Services;

use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Repositories\LaundryOrderRepository;
use Illuminate\Support\Facades\DB;

class LaundryService
{
    public function __construct(
        protected LaundryOrderRepository $repository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService
    ) {}

    public function createOrder(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            $order = $this->repository->create($data);
            foreach ($items as $item) {
                LaundryOrderItem::create([
                    'laundry_order_id' => $order->id,
                    'laundry_item_id' => $item['laundry_item_id'],
                    'service_type' => $item['service_type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price']
                ]);
            }
            
            if ($order->status === 'delivered' || $order->payment_status === 'paid') {
                $this->postToLedger($order);
            }

            return $order;
        });
    }

    public function updateOrderStatus(LaundryOrder $order, string $status)
    {
        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        if ($oldStatus !== 'delivered' && $status === 'delivered') {
            $this->postToLedger($order);
        }

        return $order;
    }

    public function postToLedger(LaundryOrder $order)
    {
        $amount = (float) $order->total_amount;
        if ($amount <= 0) return;

        $revAccount = $this->chartService->findByCode('LAUNDRY_REV');
        $arAccount = $this->chartService->findByCode('AR');
        $cashAccount = $this->chartService->findByCode(strtoupper($order->payment_method ?? 'CASH') === 'CASH' ? 'CASH' : 'BANK');

        if ($order->payment_status === 'paid') {
            // Directly to cash/bank
            if ($cashAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $cashAccount->id, $revAccount->id, $amount, now()->toDateString(),
                    'Laundry Revenue - Order #' . $order->id,
                    'laundry_order', $order->id, auth()->id()
                );
            }
        } else {
            // To AR
            if ($arAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $arAccount->id, $revAccount->id, $amount, now()->toDateString(),
                    'Laundry Revenue (AR) - Order #' . $order->id,
                    'laundry_order', $order->id, auth()->id()
                );
            }
        }
    }
}
