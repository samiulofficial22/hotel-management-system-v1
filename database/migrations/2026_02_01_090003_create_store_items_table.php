<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 50)->nullable()->unique();
            $table->string('category', 50)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit', 20)->default('pcs');
            $table->unsignedInteger('reorder_level')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_items');
    }
};
