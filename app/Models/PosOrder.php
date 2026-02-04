<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $fillable = [
        'outlet_id', 'pos_table_id', 'order_number', 'status',
        'subtotal', 'tax_amount', 'total', 'served_by', 'notes', 'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_SENT_TO_KITCHEN = 'sent_to_kitchen';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /** @return BelongsTo<Outlet, $this> */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /** @return BelongsTo<PosTable, $this> */
    public function posTable(): BelongsTo
    {
        return $this->belongsTo(PosTable::class);
    }

    /** @return BelongsTo<User, $this> */
    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /** @return HasMany<PosOrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }
}
