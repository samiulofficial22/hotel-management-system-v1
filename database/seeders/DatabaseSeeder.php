<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Order: Roles & Permissions -> Users -> RoomTypes -> Rooms -> Guests -> Bookings
     * Transaction-safe; avoids duplicate primary key by using firstOrCreate / count check.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            GuestSeeder::class,
            BookingSeeder::class,
            InvoiceAndPaymentSeeder::class,
            OutletAndPosSeeder::class,
            BanquetVenueSeeder::class,
        ]);
    }
}
