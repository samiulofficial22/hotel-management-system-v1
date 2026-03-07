<?php

namespace App\Services;

use App\Models\SpaBooking;
use App\Repositories\SpaBookingRepository;
use Illuminate\Support\Facades\DB;

class SpaBookingService
{
    public function __construct(
        protected SpaBookingRepository $repository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService
    ) {}

    public function createBooking(array $data)
    {
        $booking = $this->repository->create($data);
        
        if ($booking->status === 'confirmed' || $booking->payment_status === 'paid') {
            $this->postToLedger($booking);
        }

        return $booking;
    }

    public function updateStatus(SpaBooking $booking, string $status)
    {
        $oldStatus = $booking->status;
        $booking->update(['status' => $status]);

        if ($oldStatus !== 'confirmed' && $status === 'confirmed') {
            $this->postToLedger($booking);
        }

        return $booking;
    }

    public function postToLedger(SpaBooking $booking)
    {
        $amount = (float) $booking->amount;
        if ($amount <= 0) return;

        $revAccount = $this->chartService->findByCode('SPA_REV');
        $arAccount = $this->chartService->findByCode('AR');
        $cashAccount = $this->chartService->findByCode(strtoupper($booking->payment_method ?? 'CASH') === 'CASH' ? 'CASH' : 'BANK');

        if ($booking->payment_status === 'paid') {
            if ($cashAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $cashAccount->id, $revAccount->id, $amount, now()->toDateString(),
                    'Spa Revenue - Booking #' . $booking->id,
                    'spa_booking', $booking->id, auth()->id()
                );
            }
        } else {
            if ($arAccount && $revAccount) {
                $this->ledgerService->createDoubleEntry(
                    $arAccount->id, $revAccount->id, $amount, now()->toDateString(),
                    'Spa Revenue (AR) - Booking #' . $booking->id,
                    'spa_booking', $booking->id, auth()->id()
                );
            }
        }
    }
}
