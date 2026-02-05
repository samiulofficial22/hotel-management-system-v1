<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Employee module fields. All new columns nullable; no change to existing logic.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_code', 30)->nullable()->after('id');
            $table->string('name', 200)->nullable()->after('employee_code');
            $table->string('employment_type', 50)->nullable()->after('department_id');
            $table->decimal('salary', 12, 2)->nullable()->after('join_date');
            $table->string('shift', 50)->nullable()->after('salary');
            $table->string('status', 30)->nullable()->after('is_active');
            $table->string('photo')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code', 'name', 'employment_type',
                'salary', 'shift', 'status', 'photo',
            ]);
        });
    }
};
