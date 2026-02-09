<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\PaymentLedgerService;

class PaymentObserver
{
    public function __construct(protected PaymentLedgerService $ledgerService) {}

    public function created(Payment $payment): void
    {
        try {
            $this->ledgerService->postPayment($payment);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
