<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_credit_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_credit_id')
                ->constrained('tenant_credits')
                ->restrictOnDelete();

            $table->foreignId('rent_invoice_id')
                ->constrained('rent_invoices')
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->timestamps();

            $table->unique(
                [
                    'tenant_credit_id',
                    'rent_invoice_id',
                ],
                'tenant_credit_invoice_unique'
            );

            $table->index('rent_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_credit_allocations');
    }
};