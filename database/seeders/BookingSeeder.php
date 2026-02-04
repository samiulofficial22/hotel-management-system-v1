<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Create fake bookings for every user: past (checked_out), today check-in/out, future.
     * Spread created_by across all staff so each user has bookings.
     */
    public function run(): void
    {
        $guests = Guest::all();
        $rooms = Room::all();
        $users = User::all();
        if ($guests->count() < 5 || $rooms->count() < 5 || $users->isEmpty()) {
            return;
        }

        $today = Carbon::today();
        $usedRoomDates = []; // [room_id => [date => true]] to avoid overbooking
        $bookingNum = 1;

        $createBooking = function (Carbon $checkIn, Carbon $checkOut, string $status, ?int $createdBy) use ($guests, $rooms, &$usedRoomDates, &$bookingNum) {
            $room = $rooms->random();
            $guest = $guests->random();
            // Simple overlap check: pick a room that doesn't have these dates
            for ($attempt = 0; $attempt < 20; $attempt++) {
                $overlap = false;
                for ($d = $checkIn->copy(); $d->lt($checkOut); $d->addDay()) {
                    $key = $room->id . '-' . $d->toDateString();
                    if (!empty($usedRoomDates[$key])) {
                        $overlap = true;
                        break;
                    }
                }
                if (!$overlap) {
                    break;
                }
                $room = $rooms->random();
            }
            for ($d = $checkIn->copy(); $d->lt($checkOut); $d->addDay()) {
                $usedRoomDates[$room->id . '-' . $d->toDateString()] = true;
            }

            $bn = 'BK' . $checkIn->format('Ymd') . str_pad((string) $bookingNum++, 4, '0', STR_PAD_LEFT);
            $rate = (float) fake()->randomElement([80, 100, 120, 150, 200, 250]);

            return Booking::firstOrCreate(
                ['booking_number' => $bn],
                [
                    'guest_id' => $guest->id,
                    'room_id' => $room->id,
                    'created_by' => $createdBy,
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkOut,
                    'checked_in_at' => in_array($status, [Booking::STATUS_CHECKED_IN, Booking::STATUS_CHECKED_OUT]) ? $checkIn->copy()->setTime(14, 0) : null,
                    'checked_out_at' => $status === Booking::STATUS_CHECKED_OUT ? $checkOut->copy()->setTime(11, 0) : null,
                    'booking_type' => fake()->randomElement([Booking::TYPE_ADVANCE, Booking::TYPE_WALK_IN]),
                    'status' => $status,
                    'adults' => fake()->numberBetween(1, 3),
                    'children' => fake()->optional(0.3)->numberBetween(0, 2) ?? 0,
                    'room_rate' => $rate,
                    'late_checkout_fee' => $status === Booking::STATUS_CHECKED_OUT && fake()->boolean(20) ? 25.00 : 0,
                ]
            );
        };

        $userIds = $users->pluck('id')->toArray();

        // Past checked_out (for invoices/payments)
        for ($i = 0; $i < 15; $i++) {
            $daysAgo = rand(3, 30);
            $checkIn = $today->copy()->subDays($daysAgo + rand(2, 5));
            $checkOut = $checkIn->copy()->addDays(rand(2, 4));
            $createBooking($checkIn, $checkOut, Booking::STATUS_CHECKED_OUT, $userIds[array_rand($userIds)]);
        }

        // Today check-outs (dashboard alerts)
        for ($i = 0; $i < 4; $i++) {
            $checkOut = $today;
            $checkIn = $today->copy()->subDays(rand(2, 5));
            $createBooking($checkIn, $checkOut, Booking::STATUS_CHECKED_IN, $userIds[array_rand($userIds)]);
        }

        // Today check-ins (dashboard alerts)
        for ($i = 0; $i < 5; $i++) {
            $checkIn = $today;
            $checkOut = $today->copy()->addDays(rand(2, 5));
            $createBooking($checkIn, $checkOut, Booking::STATUS_CONFIRMED, $userIds[array_rand($userIds)]);
        }

        // Current stays (checked_in)
        for ($i = 0; $i < 8; $i++) {
            $checkIn = $today->copy()->subDays(rand(1, 4));
            $checkOut = $today->copy()->addDays(rand(1, 4));
            $createBooking($checkIn, $checkOut, Booking::STATUS_CHECKED_IN, $userIds[array_rand($userIds)]);
        }

        // Future confirmed
        for ($i = 0; $i < 25; $i++) {
            $checkIn = $today->copy()->addDays(rand(1, 21));
            $checkOut = $checkIn->copy()->addDays(rand(2, 6));
            $createBooking($checkIn, $checkOut, Booking::STATUS_CONFIRMED, $userIds[array_rand($userIds)]);
        }

        // Set room statuses: occupied for rooms with checked_in, cleaning for some
        Booking::where('status', Booking::STATUS_CHECKED_IN)->get()->each(function (Booking $b) {
            $b->room->update(['status' => Room::STATUS_OCCUPIED]);
        });
        $cleaningRooms = Booking::where('status', Booking::STATUS_CHECKED_OUT)->whereDate('checked_out_at', $today)->limit(3)->get()->pluck('room_id');
        Room::whereIn('id', $cleaningRooms)->update(['status' => Room::STATUS_CLEANING]);
    }
}
