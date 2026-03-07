<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'wash_price', 'iron_price', 'dry_clean_price', 'is_active'];

    public function orderItems()
    {
        return $this->hasMany(LaundryOrderItem::class);
    }
}
