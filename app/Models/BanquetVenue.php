<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BanquetVenue extends Model
{
    protected $fillable = ['name', 'capacity', 'hourly_rate', 'fixed_rate', 'description', 'is_active'];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'fixed_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /** @return HasMany<BanquetBooking, $this> */
    public function banquetBookings(): HasMany
    {
        return $this->hasMany(BanquetBooking::class);
    }
}
