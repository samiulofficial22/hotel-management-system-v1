<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMaintenanceRequestNotification extends Notification
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
        $reporter = $this->maintenanceRequest->reportedBy;
        $reporterName = $reporter ? $reporter->name : __('messages.Not assigned');

        $msg = __('messages.New maintenance request: :title', ['title' => $this->maintenanceRequest->title]);
        if ($roomNumber !== '-') {
            $msg .= ' (' . __('messages.Room') . ' ' . $roomNumber . ')';
        }
        $msg .= ' – ' . __('messages.Reported by') . ' ' . $reporterName;

        return [
            'type' => 'new_maintenance_request',
            'message' => $msg,
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'title' => $this->maintenanceRequest->title,
            'room_number' => $roomNumber,
            'priority' => $this->maintenanceRequest->priority ?? 'normal',
            'url' => route('maintenance.show', $this->maintenanceRequest),
        ];
    }
}
