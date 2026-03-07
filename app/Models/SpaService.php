<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'duration_minutes', 'price', 'is_active'];

    public function bookings()
    {
        return $this->hasMany(SpaBooking::class);
    }
}
