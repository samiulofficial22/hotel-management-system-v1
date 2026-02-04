<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Notification log: email/SMS sent, channel, recipient, status (queue-based sending).
     */
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // booking_confirmation, check_in_notification, check_out_summary, payment_reminder, etc.
            $table->string('channel', 20)->default('email'); // email, sms
            $table->string('recipient', 255);
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'queued'])->default('pending');
            $table->string('external_id', 100)->nullable(); // provider message id
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('notifiable_type', 100)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'channel']);
            $table->index('status');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
