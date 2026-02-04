<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Standard', 'Deluxe', 'Superior', 'Suite', 'Executive', 'Family']);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->sentence(10),
            'base_rate' => fake()->randomFloat(2, 50, 300),
            'max_occupancy' => fake()->numberBetween(2, 5),
            'size_sqm' => fake()->numberBetween(20, 60),
            'is_active' => true,
        ];
    }
}
