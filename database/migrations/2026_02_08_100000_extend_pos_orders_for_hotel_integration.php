<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hotel POS integration: guest/booking link, pos type, payment status, invoice link.
     */
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->foreignId('guest_id')->nullable()->after('outlet_id')->constrained('guests')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->after('guest_id')->constrained('bookings')->nullOnDelete();
            $table->string('pos_type', 30)->default('restaurant')->after('order_number'); // front_desk, restaurant, room_service, minibar, laundry
            $table->string('payment_status', 30)->default('pending')->after('status'); // pending, paid, posted_to_room, voided
            $table->foreignId('invoice_id')->nullable()->after('booking_id')->constrained('invoices')->nullOnDelete();
        });
        // Existing completed orders that already have a payment record => mark as paid
        DB::table('pos_orders')
            ->where('status', 'completed')
            ->whereIn('id', function ($q) {
                $q->select('pos_order_id')->from('payments')->whereNotNull('pos_order_id');
            })
            ->update(['payment_status' => 'paid']);
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['invoice_id']);
        });
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn(['guest_id', 'booking_id', 'pos_type', 'payment_status', 'invoice_id']);
        });
    }
};
