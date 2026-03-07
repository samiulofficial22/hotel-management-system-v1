<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('spa_services', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->text('description')->nullable();
            $blueprint->integer('duration_minutes');
            $blueprint->decimal('price', 10, 2);
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });

        Schema::create('spa_bookings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $blueprint->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->foreignId('spa_service_id')->constrained()->onDelete('cascade');
            $blueprint->date('booking_date');
            $blueprint->time('booking_time');
            $blueprint->decimal('amount', 10, 2);
            $blueprint->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $blueprint->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $blueprint->string('payment_method')->nullable();
            $blueprint->text('notes')->nullable();
            $blueprint->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spa_bookings');
        Schema::dropIfExists('spa_services');
    }
};
