<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RoomResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_type_id' => $this->room_type_id,
            'room_type' => $this->whenLoaded('roomType', fn () => new RoomTypeResource($this->roomType)),
            'number' => $this->number,
            'floor' => $this->floor,
            'status' => $this->status,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];
    }
}
