<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_credits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->decimal('remaining_amount', 12, 2);

            $table->string('currency', 3);

            $table->enum('status', [
                'available',
                'partially_used',
                'used',
                'refunded',
                'cancelled',
            ])->default('available');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'currency',
                'status',
            ]);

            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_credits');
    }
};