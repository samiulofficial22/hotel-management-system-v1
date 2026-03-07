<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRefreshmentTransaction extends Model
{
    protected $fillable = [
        'booking_id', 'room_id', 'item_id', 'quantity', 'unit_price', 'total_price', 'recorded_by'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function item()
    {
        return $this->belongsTo(RoomRefreshmentItem::class , 'item_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class , 'recorded_by');
    }
}
