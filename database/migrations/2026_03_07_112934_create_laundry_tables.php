<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('laundry_items', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->decimal('wash_price', 10, 2)->default(0);
            $blueprint->decimal('iron_price', 10, 2)->default(0);
            $blueprint->decimal('dry_clean_price', 10, 2)->default(0);
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });

        Schema::create('laundry_orders', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('room_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->decimal('total_amount', 10, 2);
            $blueprint->enum('status', ['pending', 'processing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $blueprint->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $blueprint->string('payment_method')->nullable();
            $blueprint->text('notes')->nullable();
            $blueprint->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $blueprint->timestamps();
        });

        Schema::create('laundry_order_items', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('laundry_order_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('laundry_item_id')->constrained()->onDelete('cascade');
            $blueprint->enum('service_type', ['wash', 'iron', 'dry_clean']);
            $blueprint->integer('quantity');
            $blueprint->decimal('unit_price', 10, 2);
            $blueprint->decimal('subtotal', 10, 2);
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
        Schema::dropIfExists('laundry_orders');
        Schema::dropIfExists('laundry_items');
    }
};
