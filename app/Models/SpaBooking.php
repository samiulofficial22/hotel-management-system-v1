<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'guest_id', 'spa_service_id', 'booking_date',
        'booking_time', 'amount', 'status', 'payment_status',
        'payment_method', 'notes', 'created_by'
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function service()
    {
        return $this->belongsTo(SpaService::class , 'spa_service_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class , 'created_by');
    }
}
