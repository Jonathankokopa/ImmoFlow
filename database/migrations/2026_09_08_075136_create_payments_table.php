<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Invoice
            |--------------------------------------------------------------------------
            */

            $table->foreignId('rent_invoice_id')
                ->constrained('rent_invoices')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Amount & Currency
            |--------------------------------------------------------------------------
            |
            | The payment currency must match the rent invoice currency.
            | Currency validation is handled by the application.
            |
            */

            $table->decimal('amount', 12, 2);

            $table->string('currency', 3);

            $table->date('payment_date');

            /*
            |--------------------------------------------------------------------------
            | Payment Method
            |--------------------------------------------------------------------------
            |
            | Examples:
            | cash
            | bank_transfer
            | mpesa
            | airtel_money
            | card
            | other
            |
            */

            $table->string('payment_method')
                ->default('other');

            /*
            |--------------------------------------------------------------------------
            | Payment Provider
            |--------------------------------------------------------------------------
            |
            | NULL means the payment was manually recorded.
            |
            | Examples:
            | mpesa
            | airtel_money
            | stripe
            |
            */

            $table->string('provider')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Provider Transaction Reference
            |--------------------------------------------------------------------------
            |
            | Stores the provider's transaction/reference ID.
            |
            | Examples:
            | M-Pesa CheckoutRequestID
            | M-Pesa receipt number
            | Airtel Money transaction ID
            |
            | The provider/reference combination is unique to prevent
            | duplicate payment records when callbacks are retried.
            |
            */

            $table->string('transaction_reference')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            |
            | Examples:
            | pending
            | completed
            | failed
            | cancelled
            | refunded
            |
            | Kept as a string instead of an enum so new provider-specific
            | states can be introduced later without changing the database.
            |
            */

            $table->string('status')
                ->default('completed');

            /*
            |--------------------------------------------------------------------------
            | Provider Metadata
            |--------------------------------------------------------------------------
            |
            | Stores provider-specific information without adding provider
            | columns to the payments table.
            |
            | Examples:
            | merchant request ID
            | checkout request ID
            | receipt number
            | phone number
            | callback payload
            |
            */

            $table->json('metadata')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Recorded By
            |--------------------------------------------------------------------------
            |
            | Manual payments are associated with the user who recorded them.
            | Online provider payments may have NULL here.
            |
            */

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Constraints & Indexes
            |--------------------------------------------------------------------------
            */

            /*
             * Prevent duplicate provider transactions.
             *
             * Manual payments normally have provider = NULL, while online
             * payments use the provider + transaction reference combination.
             */
            $table->unique([
                'provider',
                'transaction_reference',
            ]);

            /*
             * Used when calculating invoice payment totals/status.
             */
            $table->index([
                'rent_invoice_id',
                'status',
            ]);

            /*
             * Useful for provider reconciliation and callback lookups.
             */
            $table->index([
                'provider',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};