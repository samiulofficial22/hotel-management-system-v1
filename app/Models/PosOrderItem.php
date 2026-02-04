<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderItem extends Model
{
    protected $fillable = ['pos_order_id', 'menu_item_id', 'quantity', 'unit_price', 'unit_cost', 'total', 'status', 'notes'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT_TO_KITCHEN = 'sent_to_kitchen';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';

    /** @return BelongsTo<PosOrder, $this> */
    public function posOrder(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class);
    }

    /** @return BelongsTo<MenuItem, $this> */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
