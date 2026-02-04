<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'invoice_id' => $this->invoice_id,
            'booking_id' => $this->booking_id,
            'amount' => (float) $this->amount,
            'method' => $this->method,
            'reference' => $this->reference,
            'status' => $this->status,
            'payment_date' => $this->payment_date?->toIso8601String(),
            'notes' => $this->notes,
        ];
    }
}
