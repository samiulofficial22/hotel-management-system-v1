<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InvoiceResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'booking_id' => $this->booking_id,
            'guest_id' => $this->guest_id,
            'guest' => $this->whenLoaded('guest', fn () => new GuestResource($this->guest)),
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'balance_due' => (float) $this->balance_due,
            'status' => $this->status,
            'invoice_date' => $this->invoice_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', fn () => InvoiceItemResource::collection($this->items)),
        ];
    }
}
