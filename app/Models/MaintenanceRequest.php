<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'room_id', 'title', 'description', 'status', 'priority',
        'reported_by', 'assigned_to', 'resolved_at', 'resolved_by', 'resolution_notes',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CANCELLED = 'cancelled';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<User, $this> */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /** @return BelongsTo<User, $this> */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return BelongsTo<User, $this> */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /** Status badge Bootstrap class and label for UI. */
    public static function statusBadgeConfig(string $status): array
    {
        $map = [
            self::STATUS_OPEN => ['label' => 'Open', 'class' => 'bg-warning text-dark'],
            self::STATUS_IN_PROGRESS => ['label' => 'In Progress', 'class' => 'bg-info text-dark'],
            self::STATUS_RESOLVED => ['label' => 'Resolved', 'class' => 'bg-success'],
            self::STATUS_CANCELLED => ['label' => 'Cancelled', 'class' => 'bg-danger'],
        ];
        return $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
    }
}
