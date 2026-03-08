<?php namespace App\Services;

use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\Booking;
use App\Repositories\LaundryOrderRepository;
use App\Services\InvoiceService;
use App\Services\LedgerEntryService;
use App\Services\ChartOfAccountService;
use Illuminate\Support\Facades\DB;

class LaundryService
{
    public function __construct(
        protected LaundryOrderRepository $repository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService,
        protected InvoiceService $invoiceService
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

            if ($order->payment_method === 'post_to_room' && $order->room_id) {
                $roomBooking = Booking::where('room_id', $order->room_id)
                    ->whereIn('status', [Booking::STATUS_CHECKED_IN])
                    ->first();
                if ($roomBooking) {
                    $invoice = $this->invoiceService->getOrCreateOpenInvoiceForBooking($roomBooking);
                    // Add each item individually to the invoice for better detail
                    foreach ($order->items()->with('item')->get() as $orderItem) {
                        $this->invoiceService->addItem(
                            $invoice, 
                            $orderItem->item->name . ' (' . ucfirst($orderItem->service_type) . ')', 
                            'laundry', 
                            $orderItem->quantity, 
                            $orderItem->unit_price
                        );
                    }
                    $order->update(['payment_status' => 'paid', 'notes' => ($order->notes ? $order->notes . ' ' : '') . '[Posted to Room Invoice #' . $invoice->invoice_number . ']']);
                }
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

        if ($oldStatus !== 'delivered' && $status === 'delivered' && $order->payment_status !== 'paid') {
            $this->postToLedger($order);
        }

        return $order;
    }

    public function updatePaymentStatus(LaundryOrder $order, string $paymentStatus)
    {
        $oldPaymentStatus = $order->payment_status;
        $order->update(['payment_status' => $paymentStatus]);

        if ($oldPaymentStatus !== 'paid' && $paymentStatus === 'paid') {
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
            if ($cashAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $cashAccount->id, $revAccount->id, $amount, now()->toDateString(),
                    'Laundry Revenue - Order #' . $order->id,
                    'laundry_order', $order->id, auth()->id()
                );
            }
        } else {
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