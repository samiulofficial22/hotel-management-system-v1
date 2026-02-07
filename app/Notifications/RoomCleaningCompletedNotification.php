<?php

namespace App\Notifications;

use App\Models\HousekeepingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RoomCleaningCompletedNotification extends Notification
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
        $completedBy = $this->assignment->assignedTo->name ?? __('Housekeeper');
        $notes = $this->assignment->notes ? trim($this->assignment->notes) : '';

        $message = __('Room :room cleaning completed by :name.', ['room' => $roomNumber, 'name' => $completedBy]);
        if ($notes !== '') {
            $message .= ' ' . __('Message') . ': ' . $notes;
        }

        return [
            'type' => 'room_cleaning_completed',
            'message' => $message,
            'assignment_id' => $this->assignment->id,
            'room_id' => $this->assignment->room_id,
            'room_number' => $roomNumber,
            'date' => $date,
            'completed_by' => $completedBy,
            'notes' => $notes,
            'url' => route('housekeeping.index', ['date' => $date]),
        ];
    }
}
