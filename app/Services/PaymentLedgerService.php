<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Payment;

/**
 * Posts payment to ledger (Cash/Bank Dr, Revenue or A/R Cr).
 * Called when a payment is created so accounting stays in sync.
 */
class PaymentLedgerService
{
    public function __construct(
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService
    ) {}

    public function postPayment(Payment $payment): void
    {
        $amount = (float) $payment->amount;
        if ($amount <= 0) {
            return;
        }
        $debitAccount = $this->getPaymentAccount($payment);
        if (! $debitAccount) {
            return;
        }
        $creditAccount = null;
        $description = 'Payment ' . $payment->payment_number;
        if ($payment->pos_order_id) {
            $creditAccount = $this->chartService->findByCode('POS_REV');
            $description = 'POS payment ' . $payment->payment_number;
        } elseif ($payment->invoice_id) {
            $creditAccount = $this->chartService->findByCode('AR');
            $description = 'Invoice payment ' . $payment->payment_number;
        }
        if (! $creditAccount) {
            $creditAccount = $this->chartService->findByCode('OTHER_REV');
        }
        if (! $creditAccount) {
            return;
        }
        $this->ledgerService->createDoubleEntry(
            $debitAccount->id,
            $creditAccount->id,
            $amount,
            $payment->payment_date->toDateString(),
            $description,
            'payment',
            $payment->id,
            $payment->received_by
        );
    }

    protected function getPaymentAccount(Payment $payment): ?ChartOfAccount
    {
        $code = strtolower($payment->method ?? 'cash') === 'cash' ? 'CASH' : 'BANK';
        return $this->chartService->findByCode($code);
    }
}
