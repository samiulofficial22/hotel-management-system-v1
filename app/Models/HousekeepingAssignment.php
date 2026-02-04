<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousekeepingAssignment extends Model
{
    protected $fillable = ['room_id', 'assigned_to', 'date', 'status', 'notes', 'completed_at'];

    protected $casts = ['date' => 'date', 'completed_at' => 'datetime'];

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /** Status badge Bootstrap class and label for UI. */
    public static function statusBadgeConfig(string $status): array
    {
        $map = [
            self::STATUS_PENDING => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            self::STATUS_IN_PROGRESS => ['label' => 'In Progress', 'class' => 'bg-info text-dark'],
            self::STATUS_COMPLETED => ['label' => 'Completed', 'class' => 'bg-success'],
        ];
        return $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
