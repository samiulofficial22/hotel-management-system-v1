<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;

class GuestSeeder extends Seeder
{
    /**
     * Factory-based guest seed data. Plenty of guests for bookings.
     */
    public function run(): void
    {
        Guest::factory(60)->create();
    }
}
