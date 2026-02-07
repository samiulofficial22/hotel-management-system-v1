<?php

namespace App\Notifications;

use App\Models\HousekeepingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RoomAssignedForCleaningNotification extends Notification
{
    use Queueable;

    public function __construct(
        public HousekeepingAssignment $assignment
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $room = $this->assignment->room;
        $roomNumber = $room ? $room->number : (string) $this->assignment->room_id;
        $date = $this->assignment->date?->format('Y-m-d') ?? '';

        return [
            'type' => 'room_assigned_cleaning',
            'message' => __('Room :room assigned for cleaning on :date', ['room' => $roomNumber, 'date' => $date]),
            'assignment_id' => $this->assignment->id,
            'room_id' => $this->assignment->room_id,
            'room_number' => $roomNumber,
            'date' => $date,
            'url' => route('housekeeping.index', ['date' => $date]),
        ];
    }
}
