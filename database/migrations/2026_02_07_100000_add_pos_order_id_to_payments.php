<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('pos_order_id')->nullable()->after('booking_id')->constrained('pos_orders')->nullOnDelete();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE payments MODIFY invoice_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('invoice_id')->nullable()->change();
            });
        }
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['pos_order_id']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE payments MODIFY invoice_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('invoice_id')->nullable(false)->change();
            });
        }
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
        });
    }
};
