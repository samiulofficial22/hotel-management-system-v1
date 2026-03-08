<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'guest_id', 'total_amount', 'status',
        'payment_status', 'payment_method', 'notes', 'created_by',
        'order_date', 'order_time'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function items()
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class , 'created_by');
    }
}
