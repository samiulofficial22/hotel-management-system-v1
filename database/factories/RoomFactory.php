<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'room_type_id' => RoomType::factory(),
            'number' => (string) fake()->unique()->numberBetween(200, 299),
            'floor' => (string) fake()->numberBetween(1, 5),
            'status' => Room::STATUS_AVAILABLE,
            'notes' => fake()->optional(0.3)->sentence(),
            'is_active' => true,
        ];
    }
}
