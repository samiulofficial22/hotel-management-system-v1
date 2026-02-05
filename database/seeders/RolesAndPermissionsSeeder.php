<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Roles: Admin, Manager, Receptionist, Housekeeping, Accountant
     * Permission middleware on routes; create permissions and assign to roles.
     */
    public function run(): void
    {
        $guardName = config('auth.defaults.guard');

        $permissions = [
            'dashboard.view',
            'room_types.manage', 'rooms.manage',
            'guests.manage', 'bookings.manage', 'bookings.checkin_checkout',
            'invoices.manage', 'payments.manage',
            'reports.view', 'users.manage', 'roles.manage',
            'pos.manage', 'pos.view', 'kitchen.manage', 'kitchen.view',
            'banquet.manage', 'banquet.view',
            'housekeeping.manage', 'housekeeping.view',
            'minibar.manage', 'minibar.view',
            'store.manage', 'store.view',
            'maintenance.manage', 'maintenance.view',
            'accounts.manage', 'accounts.view',
            'hr.manage', 'hr.view', 'payroll.manage', 'payroll.view',
            'marketing.manage', 'marketing.view',
            'departments.manage', // NEW – SAFE ADDITION: Department module
            'guest.manage', // NEW – SAFE ADDITION: Guest requests & approval
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guardName]);
        }

        $roles = [
            'Admin' => $permissions, // includes departments.manage
            'Manager' => ['dashboard.view', 'room_types.manage', 'rooms.manage', 'guests.manage', 'bookings.manage', 'bookings.checkin_checkout', 'invoices.manage', 'payments.manage', 'reports.view', 'pos.manage', 'pos.view', 'kitchen.manage', 'kitchen.view', 'banquet.manage', 'banquet.view', 'housekeeping.manage', 'housekeeping.view', 'minibar.manage', 'minibar.view', 'store.manage', 'store.view', 'maintenance.manage', 'maintenance.view', 'departments.manage'],
            'Receptionist' => ['dashboard.view', 'rooms.manage', 'guests.manage', 'bookings.manage', 'bookings.checkin_checkout', 'invoices.manage', 'payments.manage', 'pos.manage', 'pos.view', 'kitchen.view', 'banquet.view', 'maintenance.view'],
            'Housekeeping' => ['dashboard.view', 'rooms.manage', 'housekeeping.manage', 'housekeeping.view', 'minibar.view'],
            'Accountant' => ['dashboard.view', 'invoices.manage', 'payments.manage', 'reports.view', 'pos.view', 'accounts.manage', 'accounts.view', 'payroll.manage', 'payroll.view'],
            // NEW – SAFE ADDITION: Guest role for portal access only (no admin permissions by default).
            'Guest' => [],
        ];

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guardName]);
            $role->syncPermissions($rolePerms);
        }
    }
}
