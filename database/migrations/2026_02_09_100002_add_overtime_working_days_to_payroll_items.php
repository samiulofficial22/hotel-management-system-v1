<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->decimal('overtime_amount', 12, 2)->default(0)->after('base_salary');
            $table->decimal('working_days', 5, 2)->nullable()->after('overtime_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['overtime_amount', 'working_days']);
        });
    }
};
