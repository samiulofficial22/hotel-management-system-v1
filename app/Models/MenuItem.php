<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = ['menu_category_id', 'name', 'sku', 'price', 'cost', 'description', 'image', 'is_available', 'sort_order'];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /** Image URL for display; placeholder when no image. */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder-food.svg');
    }

    /** @return BelongsTo<MenuCategory, $this> */
    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
