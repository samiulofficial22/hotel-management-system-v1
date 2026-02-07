<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingRequestReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking
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
        $guest = $this->booking->guest;
        $guestName = $guest ? trim($guest->first_name . ' ' . $guest->last_name) : 'Guest';

        return [
            'type' => 'booking_request',
            'message' => __('messages.New booking request from :name', ['name' => $guestName]),
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'guest_name' => $guestName,
            'url' => route('guest.requests.index'),
        ];
    }
}
