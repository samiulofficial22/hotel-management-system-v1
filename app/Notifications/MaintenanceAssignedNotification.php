<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MaintenanceAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MaintenanceRequest $maintenanceRequest
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

        $msg = __('messages.Maintenance request assigned to you: :title', ['title' => $this->maintenanceRequest->title]);
        if ($roomNumber !== '-') {
            $msg .= ' (' . __('messages.Room') . ' ' . $roomNumber . ')';
        }

        return [
            'type' => 'maintenance_assigned',
            'message' => $msg,
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'title' => $this->maintenanceRequest->title,
            'room_number' => $roomNumber,
            'priority' => $this->maintenanceRequest->priority ?? 'normal',
            'url' => route('maintenance.show', $this->maintenanceRequest),
        ];
    }
}
