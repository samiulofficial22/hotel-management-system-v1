<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Outlet;
use App\Models\PosTable;
use Illuminate\Database\Seeder;

class OutletAndPosSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Outlet::firstOrCreate(
            ['code' => 'REST'],
            ['name' => 'Main Restaurant', 'type' => 'restaurant', 'is_active' => true]
        );
        $cafe = Outlet::firstOrCreate(
            ['code' => 'CAFE'],
            ['name' => 'Cafe & Bar', 'type' => 'cafe', 'is_active' => true]
        );

        foreach ([$restaurant, $cafe] as $outlet) {
            $cat = MenuCategory::firstOrCreate(
                ['outlet_id' => $outlet->id, 'name' => 'Beverages'],
                ['sort_order' => 0, 'is_active' => true]
            );
            MenuItem::firstOrCreate(
                ['menu_category_id' => $cat->id, 'name' => 'Coffee'],
                ['price' => 3.50, 'is_available' => true, 'sort_order' => 0]
            );
            MenuItem::firstOrCreate(
                ['menu_category_id' => $cat->id, 'name' => 'Tea'],
                ['price' => 2.50, 'is_available' => true, 'sort_order' => 1]
            );
            $cat2 = MenuCategory::firstOrCreate(
                ['outlet_id' => $outlet->id, 'name' => 'Snacks'],
                ['sort_order' => 1, 'is_active' => true]
            );
            MenuItem::firstOrCreate(
                ['menu_category_id' => $cat2->id, 'name' => 'Sandwich'],
                ['price' => 8.00, 'is_available' => true, 'sort_order' => 0]
            );

            for ($i = 1; $i <= 5; $i++) {
                PosTable::firstOrCreate(
                    ['outlet_id' => $outlet->id, 'name' => 'Table ' . $i],
                    ['capacity' => 4, 'status' => 'available', 'sort_order' => $i]
                );
            }
        }
    }
}
