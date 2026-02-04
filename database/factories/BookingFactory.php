<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+1 month');
        $checkOut = (clone $checkIn)->modify('+' . fake()->numberBetween(1, 7) . ' days');
        return [
            'booking_number' => 'BK' . now()->format('Ymd') . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'guest_id' => Guest::factory(),
            'room_id' => Room::factory(),
            'created_by' => null,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'checked_in_at' => null,
            'checked_out_at' => null,
            'booking_type' => fake()->randomElement([Booking::TYPE_ADVANCE, Booking::TYPE_WALK_IN]),
            'status' => Booking::STATUS_CONFIRMED,
            'adults' => fake()->numberBetween(1, 3),
            'children' => fake()->optional(0.3)->numberBetween(0, 2),
            'room_rate' => fake()->randomFloat(2, 80, 250),
            'late_checkout_fee' => 0,
            'special_requests' => fake()->optional(0.2)->sentence(),
            'internal_notes' => null,
        ];
    }
}
