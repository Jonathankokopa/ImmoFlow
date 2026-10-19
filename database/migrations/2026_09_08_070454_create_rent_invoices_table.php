<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lease_id')
                ->constrained('leases')
                ->restrictOnDelete();

            $table->string('invoice_number')
                ->unique();

            /*
             * Rental period represented by this invoice.
             *
             * Store the first day of the month.
             *
             * Example:
             * 2026-09-01 = September 2026
             */
            $table->date('billing_period');

            $table->date('invoice_date');
            $table->date('due_date');

            /*
             * Invoice amount is stored in the invoice currency.
             *
             * Examples:
             * 50,000.00 KES
             * 500.00 USD
             * 1,200.00 CDF
             */
            $table->decimal('amount', 12, 2);

            /*
             * Currency is inherited from the lease.
             *
             * No database default is used intentionally.
             * The application must explicitly provide the
             * lease currency when creating an invoice.
             */
            $table->string('currency', 3);

            /*
             * Cached amount paid.
             *
             * The payments table remains the financial
             * source of truth.
             */
            $table->decimal('amount_paid', 12, 2)
                ->default(0);

            $table->enum('status', [
                'unpaid',
                'partially_paid',
                'paid',
                'overdue',
                'cancelled',
            ])->default('unpaid');

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
             * A lease can have only one invoice
             * for a particular billing period.
             */
            $table->unique([
                'lease_id',
                'billing_period',
            ]);

            /*
             * Useful for invoice listings and
             * recurring invoice generation.
             */
            $table->index([
                'billing_period',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_invoices');
    }
};