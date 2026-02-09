<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MaintenanceRequest $maintenanceRequest,
        public string $completedByName
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
        $room = $this->maintenanceRequest->room;
        $roomNumber = $room ? $room->number : ($this->maintenanceRequest->room_id ? '#' . $this->maintenanceRequest->room_id : '-');

        $msg = __('messages.Maintenance completed: :title', ['title' => $this->maintenanceRequest->title]);
        if ($roomNumber !== '-') {
            $msg .= ' (' . __('messages.Room') . ' ' . $roomNumber . ')';
        }
        $msg .= ' – ' . __('messages.Completed by') . ' ' . $this->completedByName;

        return [
            'type' => 'maintenance_completed',
            'message' => $msg,
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'title' => $this->maintenanceRequest->title,
            'room_number' => $roomNumber,
            'url' => route('maintenance.show', $this->maintenanceRequest),
        ];
    }
}
