<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banquet_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banquet_venue_id')->constrained()->cascadeOnDelete();
            $table->string('event_name');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('guest_count');
            $table->string('package_name')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, confirmed, cancelled, completed
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banquet_bookings');
    }
};
