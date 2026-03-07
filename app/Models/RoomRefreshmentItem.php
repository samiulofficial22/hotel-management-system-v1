<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRefreshmentItem extends Model
{
    protected $fillable = [
        'name', 'category', 'price', 'cost_price', 'stock_quantity', 'is_active'
    ];

    public function transactions()
    {
        return $this->hasMany(RoomRefreshmentTransaction::class , 'item_id');
    }
}
