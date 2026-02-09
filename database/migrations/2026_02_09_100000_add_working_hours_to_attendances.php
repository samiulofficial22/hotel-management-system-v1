<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('working_hours', 5, 2)->nullable()->after('check_out');
            $table->decimal('overtime_hours', 5, 2)->nullable()->after('working_hours');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['working_hours', 'overtime_hours']);
        });
    }
};
