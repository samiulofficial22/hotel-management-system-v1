<?php

namespace Database\Seeders;

use App\Models\BanquetVenue;
use Illuminate\Database\Seeder;

class BanquetVenueSeeder extends Seeder
{
    public function run(): void
    {
        BanquetVenue::firstOrCreate(
            ['name' => 'Grand Ballroom'],
            ['capacity' => 200, 'hourly_rate' => 500, 'fixed_rate' => null, 'is_active' => true]
        );
        BanquetVenue::firstOrCreate(
            ['name' => 'Meeting Room A'],
            ['capacity' => 50, 'hourly_rate' => 150, 'fixed_rate' => null, 'is_active' => true]
        );
    }
}
