<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    protected $fillable = ['name', 'type', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const TYPE_RESTAURANT = 'restaurant';
    public const TYPE_CAFE = 'cafe';
    public const TYPE_BAR = 'bar';

    /** @return HasMany<MenuCategory, $this> */
    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    /** @return HasMany<PosTable, $this> */
    public function posTables(): HasMany
    {
        return $this->hasMany(PosTable::class);
    }

    /** @return HasMany<PosOrder, $this> */
    public function posOrders(): HasMany
    {
        return $this->hasMany(PosOrder::class);
    }
}
