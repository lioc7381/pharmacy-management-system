<?php

// database/migrations/2024_01_01_000004_create_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('prescription_id')->nullable()->unique()->constrained('prescriptions')->restrictOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['in_preparation', 'ready_for_delivery', 'completed', 'cancelled', 'failed_delivery'])
                  ->default('in_preparation');
            $table->foreignId('assigned_delivery_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index(['client_id', 'status']); // For user order history
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};