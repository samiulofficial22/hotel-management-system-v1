<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinibarItem extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'quantity_in_stock', 'reorder_level', 'unit', 'is_active'];

    protected $casts = ['price' => 'decimal:2', 'is_active' => 'boolean'];

    /** @return HasMany<MinibarConsumption, $this> */
    public function consumptions(): HasMany
    {
        return $this->hasMany(MinibarConsumption::class);
    }
}
