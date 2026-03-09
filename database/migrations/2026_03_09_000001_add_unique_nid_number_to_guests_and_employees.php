<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        // Remove duplicate nid_numbers in guests before adding unique index
        // Keep the latest entry for each duplicate nid_number
        DB::statement("
            DELETE g1 FROM guests g1
            INNER JOIN guests g2
            WHERE g1.id < g2.id
              AND g1.nid_number IS NOT NULL
              AND g1.nid_number != ''
              AND g1.nid_number = g2.nid_number
        ");

        Schema::table('guests', function (Blueprint $table) {
            $table->unique('nid_number', 'guests_nid_number_unique');
        });

        // Remove duplicate nid_numbers in employees before adding unique index
        DB::statement("
            DELETE e1 FROM employees e1
            INNER JOIN employees e2
            WHERE e1.id < e2.id
              AND e1.nid_number IS NOT NULL
              AND e1.nid_number != ''
              AND e1.nid_number = e2.nid_number
        ");

        Schema::table('employees', function (Blueprint $table) {
            $table->unique('nid_number', 'employees_nid_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropUnique('guests_nid_number_unique');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_nid_number_unique');
        });
    }
};
