<?php

// database/migrations/2024_01_01_000005_create_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['order_id', 'medication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};