<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log all POS actions for audit (create, pay, post_to_room, void, etc.).
     */
    public function up(): void
    {
        Schema::create('pos_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')->nullable()->constrained('pos_orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50); // create, add_item, remove_item, complete, pay, post_to_room, void
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['pos_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_action_logs');
    }
};
