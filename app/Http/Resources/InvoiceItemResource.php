<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InvoiceItemResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'item_type' => $this->item_type,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'amount' => (float) $this->amount,
        ];
    }
}
