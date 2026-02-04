<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Seed room types using factory. Avoid duplicate slugs.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Standard', 'slug' => 'standard', 'base_rate' => 80, 'max_occupancy' => 2, 'size_sqm' => 25],
            ['name' => 'Deluxe', 'slug' => 'deluxe', 'base_rate' => 120, 'max_occupancy' => 3, 'size_sqm' => 32],
            ['name' => 'Superior', 'slug' => 'superior', 'base_rate' => 150, 'max_occupancy' => 3, 'size_sqm' => 38],
            ['name' => 'Suite', 'slug' => 'suite', 'base_rate' => 250, 'max_occupancy' => 4, 'size_sqm' => 50],
        ];

        foreach ($types as $data) {
            RoomType::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'description' => 'Comfortable ' . $data['name'] . ' room.',
                    'is_active' => true,
                ])
            );
        }

        if (RoomType::count() <= 4) {
            RoomType::factory(2)->create();
        }
    }
}
