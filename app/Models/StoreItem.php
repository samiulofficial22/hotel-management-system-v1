<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreItem extends Model
{
    protected $fillable = ['name', 'sku', 'category', 'quantity', 'unit', 'reorder_level', 'notes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** @return HasMany<StoreMovement, $this> */
    public function movements(): HasMany
    {
        return $this->hasMany(StoreMovement::class);
    }
}
