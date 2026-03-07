<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

DB::statement("SET FOREIGN_KEY_CHECKS=0;");
$adminRoles = ["admin", "super admin", "Super Admin"];
$preserveUserIds = DB::table("model_has_roles")
    ->join("roles", "model_has_roles.role_id", "=", "roles.id")
    ->whereIn("roles.name", $adminRoles)
    ->pluck("model_id")
    ->toArray();

$deletedCount = DB::table("users")->whereNotIn("id", $preserveUserIds)->delete();
DB::statement("SET FOREIGN_KEY_CHECKS=1;");

echo "Cleaned up $deletedCount non-admin users. Current user counts: \n";
User::all()->each(function($u) {
    echo "- " . $u->name . " (" . implode(",", $u->getRoleNames()->toArray()) . ")\n";
});
