<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    /** Status badge Bootstrap class and label for UI. */
    public static function statusBadgeConfig(string $status): array
    {
        $map = [
            self::STATUS_PENDING => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            self::STATUS_CONFIRMED => ['label' => 'Confirmed', 'class' => 'bg-info text-dark'],
            self::STATUS_CHECKED_IN => ['label' => 'Checked In', 'class' => 'bg-success'],
            self::STATUS_CHECKED_OUT => ['label' => 'Checked Out', 'class' => 'bg-secondary'],
            self::STATUS_CANCELLED => ['label' => 'Cancelled', 'class' => 'bg-danger'],
            self::STATUS_NO_SHOW => ['label' => 'No Show', 'class' => 'bg-dark'],
        ];
        return $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
    }

    public const TYPE_ADVANCE = 'advance';
    public const TYPE_WALK_IN = 'walk_in';

    protected $fillable = [
        'booking_number',
        'guest_id',
        'room_id',
        'created_by',
        'check_in_date',
        'check_out_date',
        'checked_in_at',
        'checked_out_at',
        'booking_type',
        'status',
        'adults',
        'children',
        'room_rate',
        'late_checkout_fee',
        'special_requests',
        'internal_notes',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'room_rate' => 'decimal:2',
        'late_checkout_fee' => 'decimal:2',
    ];

    /** @return BelongsTo<Guest, $this> */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
