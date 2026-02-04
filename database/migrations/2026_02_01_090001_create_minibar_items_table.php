<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minibar_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 50)->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity_in_stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(10);
            $table->string('unit', 20)->default('pcs');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minibar_items');
    }
};
