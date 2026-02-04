<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinibarConsumption extends Model
{
    protected $fillable = ['booking_id', 'minibar_item_id', 'quantity', 'unit_price', 'total', 'consumed_date', 'notes', 'recorded_by'];

    protected $casts = ['unit_price' => 'decimal:2', 'total' => 'decimal:2', 'consumed_date' => 'date'];

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** @return BelongsTo<MinibarItem, $this> */
    public function minibarItem(): BelongsTo
    {
        return $this->belongsTo(MinibarItem::class);
    }
}
