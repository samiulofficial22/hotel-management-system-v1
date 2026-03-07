<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$tables = [
    'banquet_bookings',
    'banquet_venues',
    'rooms',
    'room_types',
    'guests',
    'bookings',
    'invoices',
    'invoice_items',
    'payments',
    'notification_logs',
    'outlets',
    'menu_categories',
    'menu_items',
    'pos_tables',
    'pos_orders',
    'pos_order_items',
    'housekeeping_assignments',
    'minibar_items',
    'minibar_consumptions',
    'store_items',
    'store_movements',
    'maintenance_requests',
    'ledger_entries',
    'chart_of_accounts',
    'employees',
    'attendances',
    'payroll_runs',
    'payroll_items',
    'marketing_campaigns',
    'campaign_recipients',
    'pos_action_logs',
    'notifications',
    'failed_jobs',
    'personal_access_tokens'
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->truncate();
        echo "Truncated $table\n";
    }
}

// Handle Users
$adminRoles = ['admin', 'super admin'];
$preserveUserIds = DB::table('model_has_roles')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->whereIn('roles.name', $adminRoles)
    ->pluck('model_id')
    ->toArray();

// Delete users that are not admins or super admins
DB::table('users')->whereNotIn('id', $preserveUserIds)->delete();
echo "Deleted non-admin users. Kept " . count($preserveUserIds) . " users.\n";

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Database reset complete.\n";
