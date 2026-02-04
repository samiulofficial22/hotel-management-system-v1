<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    protected $model = Guest::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'nationality' => fake()->optional(0.6)->country(),
            'id_type' => fake()->optional(0.5)->randomElement(['passport', 'national_id', 'driving_license']),
            'id_number' => fake()->optional(0.5)->numerify('########'),
            'date_of_birth' => fake()->optional(0.4)->date(),
            'address' => fake()->optional(0.5)->address(),
            'city' => fake()->optional(0.5)->city(),
            'country' => fake()->optional(0.5)->country(),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
