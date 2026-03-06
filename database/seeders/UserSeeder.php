<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Create admin and staff users with roles. Every role has at least one user.
     * Password for all: password (hashed by User model's cast)
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
        ['email' => 'admin@hotel.test'],
        ['name' => 'Admin User', 'password' => 'password', 'language_preference' => 'en']
        );
        $admin->assignRole('Admin');

        $manager = User::firstOrCreate(
        ['email' => 'manager@hotel.test'],
        ['name' => 'Manager User', 'password' => 'password', 'language_preference' => 'en']
        );
        if (!$manager->hasRole('Manager')) {
            $manager->assignRole('Manager');
        }

        $receptionist = User::firstOrCreate(
        ['email' => 'reception@hotel.test'],
        ['name' => 'Receptionist User', 'password' => 'password', 'language_preference' => 'en']
        );
        if (!$receptionist->hasRole('Receptionist')) {
            $receptionist->assignRole('Receptionist');
        }

        $housekeeping = User::firstOrCreate(
        ['email' => 'housekeeping@hotel.test'],
        ['name' => 'Housekeeping User', 'password' => 'password', 'language_preference' => 'en']
        );
        if (!$housekeeping->hasRole('Housekeeper')) {
            $housekeeping->assignRole('Housekeeper');
        }

        $accountant = User::firstOrCreate(
        ['email' => 'accountant@hotel.test'],
        ['name' => 'Accountant User', 'password' => 'password', 'language_preference' => 'en']
        );
        if (!$accountant->hasRole('Accountant')) {
            $accountant->assignRole('Accountant');
        }

        // Extra staff so every user has plenty of colleagues and data variety
        User::factory(5)->create()->each(function (User $user): void {
            if (!$user->hasAnyRole(['Admin', 'Manager', 'Receptionist', 'Housekeeper', 'Accountant'])) {
                $user->assignRole(fake()->randomElement(['Receptionist', 'Receptionist', 'Housekeeper']));
            }
        });
    }
}
