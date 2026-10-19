<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('owners')
                ->cascadeOnDelete();

            $table->foreignId('property_type_id')
                ->constrained('property_types')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->text('description')->nullable();

            $table->unsignedInteger('total_units')->default(0);

            $table->enum('status', [
                'active',
                'inactive',
                'maintenance',
            ])->default('active');

            $table->date('date_added')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};