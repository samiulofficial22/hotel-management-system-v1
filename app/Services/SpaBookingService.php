<?php namespace App\Services;

use App\Models\SpaBooking;
use App\Models\Booking;
use App\Repositories\SpaBookingRepository;
use App\Services\InvoiceService;
use App\Services\LedgerEntryService;
use App\Services\ChartOfAccountService;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB;

class SpaBookingService
{
    public function __construct(
        protected SpaBookingRepository $repository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService,
        protected InvoiceService $invoiceService,
        protected BookingService $bookingService
    ) {}

    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $booking = $this->repository->create($data);

            if ($booking->payment_method === 'post_to_room' && $booking->room_id) {
                $roomBooking = Booking::where('room_id', $booking->room_id)
                    ->whereIn('status', [Booking::STATUS_CHECKED_IN])
                    ->first();
                if ($roomBooking) {
                    $invoice = $this->invoiceService->getOrCreateOpenInvoiceForBooking($roomBooking);
                    $this->invoiceService->addItem($invoice, 'Spa Service: ' . $booking->service->name, 'spa', 1, $booking->amount);
                    $booking->update(['payment_status' => 'paid', 'notes' => ($booking->notes ? $booking->notes . ' ' : '') . '[Posted to Room Invoice #' . $invoice->invoice_number . ']']);
                }
            }
            
            if ($booking->status === 'confirmed' || $booking->payment_status === 'paid') {
                $this->postToLedger($booking);
            }

            return $booking;
        });
    }

    public function updateStatus(SpaBooking $booking, string $status)
    {
        $oldStatus = $booking->status;
        $booking->update(['status' => $status]);

        if ($oldStatus !== 'confirmed' && $status === 'confirmed' && $booking->payment_status !== 'paid') {
            $this->postToLedger($booking);
        }

        return $booking;
    }

    public function updatePaymentStatus(SpaBooking $booking, string $paymentStatus)
    {
        $oldPaymentStatus = $booking->payment_status;
        $booking->update(['payment_status' => $paymentStatus]);

        if ($oldPaymentStatus !== 'paid' && $paymentStatus === 'paid') {
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