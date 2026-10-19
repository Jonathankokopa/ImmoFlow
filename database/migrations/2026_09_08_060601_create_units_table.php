<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')
                ->constrained('properties')
                ->restrictOnDelete();

            $table->string('unit_number');
            $table->unsignedInteger('floor')->nullable();
            $table->unsignedInteger('bedrooms')->default(0);
            $table->unsignedInteger('bathrooms')->default(0);

            $table->decimal('monthly_rent', 12, 2);
            $table->string('currency', 3)->default('USD');

            $table->enum('status', [
                'vacant',
                'occupied',
                'reserved',
                'maintenance',
            ])->default('vacant');

            $table->timestamps();

            $table->unique(['property_id', 'unit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};