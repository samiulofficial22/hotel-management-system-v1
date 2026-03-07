<?php

namespace App\Services;

use App\Models\RoomRefreshmentItem;
use App\Models\RoomRefreshmentTransaction;
use App\Models\Booking;
use App\Repositories\RoomRefreshmentRepository;
use Illuminate\Support\Facades\DB;

class RoomRefreshmentService
{
    public function __construct(protected
        RoomRefreshmentRepository $repository, protected
        LedgerEntryService $ledgerService, protected
        ChartOfAccountService $chartService, protected
        InvoiceService $invoiceService
        )
    {
    }

    public function recordConsumption(int $bookingId, int $itemId, int $quantity, int $recordedBy)
    {
        return DB::transaction(function () use ($bookingId, $itemId, $quantity, $recordedBy) {
            $item = RoomRefreshmentItem::findOrFail($itemId);
            $booking = Booking::findOrFail($bookingId);

            $totalPrice = $item->price * $quantity;

            // 1. Create Transaction
            $transaction = $this->repository->createTransaction([
                'booking_id' => $bookingId,
                'room_id' => $booking->room_id,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'unit_price' => $item->price,
                'total_price' => $totalPrice,
                'recorded_by' => $recordedBy
            ]);

            // 2. Reduce Stock
            $item->decrement('stock_quantity', $quantity);

            // 3. Add to Invoice (Guest Folio)
            $invoice = $this->invoiceService->getOrCreateOpenInvoiceForBooking($booking);
            $this->invoiceService->addItem($invoice, "Refreshment: {$item->name} x {$quantity}", 'refreshment', $quantity, $item->price);

            // 4. Ledger Entry: Debit AR, Credit Refreshment Revenue
            $arAccount = $this->chartService->findByCode('AR');
            $revAccount = $this->chartService->findByCode('REFRESHMENT_REV');

            if ($arAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $arAccount->id,
                    $revAccount->id,
                    $totalPrice,
                    now()->toDateString(),
                    "Room Refreshment - Booking #{$bookingId} - {$item->name}",
                    'refreshment_transaction',
                    $transaction->id,
                    $recordedBy
                );
            }

            return $transaction;
        });
    }
}
