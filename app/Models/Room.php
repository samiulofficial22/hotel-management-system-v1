<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_CLEANING = 'cleaning';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_OUT_OF_ORDER = 'out_of_order';

    /** Status badge Bootstrap class and label for UI. */
    public static function statusBadgeConfig(string $status): array
    {
        $map = [
            self::STATUS_AVAILABLE => ['label' => 'Available', 'class' => 'bg-success'],
            self::STATUS_OCCUPIED => ['label' => 'Occupied', 'class' => 'bg-primary'],
            self::STATUS_CLEANING => ['label' => 'Cleaning', 'class' => 'bg-warning text-dark'],
            self::STATUS_MAINTENANCE => ['label' => 'Maintenance', 'class' => 'bg-info text-dark'],
            self::STATUS_OUT_OF_ORDER => ['label' => 'Out of Order', 'class' => 'bg-danger'],
        ];
        return $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
    }

    protected $fillable = [
        'room_type_id',
        'number',
        'floor',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** @return BelongsTo<RoomType, $this> */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<HousekeepingAssignment, $this> */
    public function housekeepingAssignments(): HasMany
    {
        return $this->hasMany(HousekeepingAssignment::class);
    }
}
