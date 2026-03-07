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
            'guests.manage', 'bookings.manage', 'bookings.view', 'bookings.checkin_checkout',
            'invoices.manage', 'payments.manage',
            'reports.view', 'users.manage', 'roles.manage', 'roles.assign',
            'pos.manage', 'pos.view', 'pos.create', 'pos.pay', 'pos.void', 'pos.view_reports', 'kitchen.manage', 'kitchen.view',
            'banquet.manage', 'banquet.view',
            'housekeeping.manage', 'housekeeping.view', 'housekeeping.assign_others',
            'minibar.manage', 'minibar.view',
            'store.manage', 'store.view',
            'maintenance.manage', 'maintenance.view',
            'accounts.manage', 'accounts.view',
            'hr.manage', 'hr.view', 'payroll.manage', 'payroll.view', 'payroll.generate', 'payroll.approve', 'payroll.pay',
            'marketing.manage', 'marketing.view',
            'departments.manage',
            'guest.manage', 'guest.approve',
            'laundry.manage', 'laundry.view',
            'spa.manage', 'spa.view',
            'refreshments.manage', 'refreshments.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guardName]);
        }

        $roles = [
            'Super Admin' => array_merge($permissions, ['laundry.manage', 'laundry.view', 'spa.manage', 'spa.view']),
            'Admin' => array_merge($permissions, ['laundry.manage', 'laundry.view', 'spa.manage', 'spa.view']),
            'Manager' => array_merge(['dashboard.view', 'room_types.manage', 'rooms.manage', 'guests.manage', 'bookings.manage', 'bookings.view', 'bookings.checkin_checkout', 'invoices.manage', 'payments.manage', 'reports.view', 'pos.manage', 'pos.view', 'pos.create', 'pos.pay', 'pos.void', 'pos.view_reports', 'kitchen.manage', 'kitchen.view', 'banquet.manage', 'banquet.view', 'housekeeping.manage', 'housekeeping.view', 'housekeeping.assign_others', 'minibar.manage', 'minibar.view', 'store.manage', 'store.view', 'maintenance.manage', 'maintenance.view', 'departments.manage', 'guest.manage', 'guest.approve'], ['laundry.manage', 'laundry.view', 'spa.manage', 'spa.view']),
            'Receptionist' => array_merge(['dashboard.view', 'rooms.manage', 'guests.manage', 'bookings.manage', 'bookings.view', 'bookings.checkin_checkout', 'invoices.manage', 'payments.manage', 'pos.manage', 'pos.view', 'pos.create', 'pos.pay', 'pos.view_reports', 'kitchen.view', 'banquet.view', 'maintenance.view', 'guest.manage'], ['laundry.view', 'spa.view', 'laundry.manage', 'spa.manage']),
            'Housekeeper' => ['dashboard.view', 'rooms.manage', 'housekeeping.manage', 'housekeeping.view', 'minibar.view', 'maintenance.view', 'laundry.view', 'laundry.manage', 'refreshments.view', 'refreshments.manage'],
            'Accountant' => ['dashboard.view', 'invoices.manage', 'payments.manage', 'reports.view', 'pos.view', 'pos.view_reports', 'accounts.manage', 'accounts.view', 'payroll.manage', 'payroll.view', 'payroll.generate', 'payroll.approve', 'payroll.pay', 'laundry.view', 'spa.view'],
            'Guest' => [],
        ];

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guardName]);
            $role->syncPermissions($rolePerms);
        }
    }
}
