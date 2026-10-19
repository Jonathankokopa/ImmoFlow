<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->restrictOnDelete();

            $table->foreignId('rent_invoice_id')
                ->constrained('rent_invoices')
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->timestamps();

            $table->unique([
                'payment_id',
                'rent_invoice_id',
            ]);

            $table->index('rent_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};