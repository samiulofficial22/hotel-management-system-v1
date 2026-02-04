<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BookingResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'guest_id' => $this->guest_id,
            'guest' => $this->whenLoaded('guest', fn () => new GuestResource($this->guest)),
            'room_id' => $this->room_id,
            'room' => $this->whenLoaded('room', fn () => new RoomResource($this->room)),
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'checked_in_at' => $this->checked_in_at?->toIso8601String(),
            'checked_out_at' => $this->checked_out_at?->toIso8601String(),
            'booking_type' => $this->booking_type,
            'status' => $this->status,
            'adults' => $this->adults,
            'children' => $this->children,
            'room_rate' => (float) $this->room_rate,
            'late_checkout_fee' => (float) $this->late_checkout_fee,
            'special_requests' => $this->special_requests,
            'internal_notes' => $this->internal_notes,
        ];
    }
}
