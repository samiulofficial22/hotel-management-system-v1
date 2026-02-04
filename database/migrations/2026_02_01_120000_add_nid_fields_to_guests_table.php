<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('nid_number', 50)->nullable()->after('id_number');
            $table->string('nid_photo_front', 255)->nullable()->after('nid_number');
            $table->string('nid_photo_back', 255)->nullable()->after('nid_photo_front');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['nid_number', 'nid_photo_front', 'nid_photo_back']);
        });
    }
};
