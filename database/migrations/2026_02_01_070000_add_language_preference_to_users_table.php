<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * User language preference for multi-language support (en, bn).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('language_preference', 5)->default('en')->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('language_preference');
        });
    }
};
