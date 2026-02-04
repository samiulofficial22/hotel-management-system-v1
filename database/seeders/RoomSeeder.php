<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Seed rooms per room type. Avoid duplicate room numbers.
     */
    public function run(): void
    {
        $roomTypes = RoomType::all();
        $number = 101;
        foreach ($roomTypes as $type) {
            $count = $type->name === 'Suite' ? 4 : 6;
            for ($i = 0; $i < $count; $i++) {
                $numStr = (string) $number;
                Room::firstOrCreate(
                    ['number' => $numStr],
                    [
                        'room_type_id' => $type->id,
                        'floor' => (string) (intdiv($number - 100, 10) + 1),
                        'status' => Room::STATUS_AVAILABLE,
                        'is_active' => true,
                    ]
                );
                $number++;
            }
        }
    }
}
