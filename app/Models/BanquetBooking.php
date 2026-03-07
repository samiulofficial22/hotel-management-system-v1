<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanquetBooking extends Model
{
    protected $fillable = [
        'banquet_venue_id', 'event_name', 'event_date', 'start_time', 'end_time',
        'guest_count', 'package_name', 'total_amount', 'status',
        'contact_name', 'contact_phone', 'contact_email', 'notes', 'created_by',
        'payment_method', 'payment_status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    /** Status badge Bootstrap class and label for UI. */
    public static function statusBadgeConfig(string $status): array
    {
        $map = [
            self::STATUS_PENDING => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            self::STATUS_CONFIRMED => ['label' => 'Confirmed', 'class' => 'bg-success'],
            self::STATUS_CANCELLED => ['label' => 'Cancelled', 'class' => 'bg-danger'],
            self::STATUS_COMPLETED => ['label' => 'Completed', 'class' => 'bg-secondary'],
        ];
        return $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
    }

    /** @return BelongsTo<BanquetVenue, $this> */
    public function banquetVenue(): BelongsTo
    {
        return $this->belongsTo(BanquetVenue::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function getDurationHoursAttribute(): float
    {
        if (!$this->start_time || !$this->end_time)
            return 0;
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return round($start->diffInMinutes($end) / 60, 2);
    }
}
