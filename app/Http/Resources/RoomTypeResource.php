<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RoomTypeResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_rate' => (float) $this->base_rate,
            'max_occupancy' => $this->max_occupancy,
            'size_sqm' => $this->size_sqm,
            'is_active' => $this->is_active,
        ];
    }
}
