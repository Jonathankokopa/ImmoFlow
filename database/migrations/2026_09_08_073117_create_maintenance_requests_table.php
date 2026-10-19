<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('maintenance_requests', function (Blueprint $table) {
        $table->id();

        $table->foreignId('unit_id')
            ->constrained('units')
            ->cascadeOnDelete();

        $table->foreignId('tenant_id')
            ->nullable()
            ->constrained('tenants')
            ->nullOnDelete();

        $table->foreignId('category_id')
            ->nullable()
            ->constrained('maintenance_categories')
            ->nullOnDelete();

        $table->string('title');
        $table->text('description');

        $table->enum('priority', [
            'low',
            'medium',
            'high',
            'urgent',
        ])->default('medium');

        $table->enum('status', [
            'pending',
            'assigned',
            'in_progress',
            'completed',
            'cancelled',
        ])->default('pending');

        $table->foreignId('assigned_to')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->date('requested_date');
        $table->date('completed_date')->nullable();

        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
