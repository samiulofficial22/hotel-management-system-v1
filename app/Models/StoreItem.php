<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreItem extends Model
{
    protected $fillable = ['name', 'sku', 'category', 'quantity', 'unit', 'reorder_level', 'notes', 'is_active', 'department_id'];

    protected $casts = ['is_active' => 'boolean'];

    /** NEW – SAFE ADDITION: Optional department. */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return HasMany<StoreMovement, $this> */
    public function movements(): HasMany
    {
        return $this->hasMany(StoreMovement::class);
    }
}
