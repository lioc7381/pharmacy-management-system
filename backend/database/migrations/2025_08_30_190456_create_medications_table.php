<?php

// database/migrations/2024_01_01_000002_create_medications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('strength_form');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('current_quantity')->default(0);
            $table->integer('minimum_threshold')->default(10);
            $table->enum('category', ['Pain Relief', 'Antibiotics', 'Vitamins', 'Cold & Flu', 'Skincare']);
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->timestamps();
            $table->index('name');
            $table->index(['category', 'status']); // For filtered searches
            $table->index('current_quantity'); // For stock checks
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};