<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreMovement extends Model
{
    protected $fillable = ['store_item_id', 'quantity_delta', 'type', 'reference', 'notes', 'created_by'];

    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';
    public const TYPE_ADJUSTMENT = 'adjustment';

    /** @return BelongsTo<StoreItem, $this> */
    public function storeItem(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
