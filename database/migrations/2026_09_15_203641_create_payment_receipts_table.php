<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            |
            | One completed payment produces one receipt.
            |
            */

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Receipt Number
            |--------------------------------------------------------------------------
            |
            | KopsRent's internal receipt/document number.
            |
            | This is deliberately separate from the provider transaction
            | reference such as an M-Pesa receipt number.
            |
            | Example:
            | RC-2026-000018
            |
            */

            $table->string('receipt_number')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Language
            |--------------------------------------------------------------------------
            |
            | Receipt language is independent of currency.
            |
            | Examples:
            | en = English
            | fr = French
            |
            */

            $table->string('language', 5)
                ->default('en');

            /*
            |--------------------------------------------------------------------------
            | Issued At
            |--------------------------------------------------------------------------
            |
            | The actual date/time the KopsRent receipt was issued.
            |
            */

            $table->timestamp('issued_at');

            /*
            |--------------------------------------------------------------------------
            | Verification Token
            |--------------------------------------------------------------------------
            |
            | Used to create a secure public verification URL / QR code.
            |
            | Do not expose sequential database IDs as the verification
            | identifier.
            |
            */

            $table->uuid('verification_token')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | PDF / Delivery Tracking
            |--------------------------------------------------------------------------
            |
            | These fields allow us to later track whether the receipt has
            | been generated and/or emailed without changing the accounting
            | record.
            |
            */

            $table->timestamp('pdf_generated_at')
                ->nullable();

            $table->timestamp('email_sent_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Constraints & Indexes
            |--------------------------------------------------------------------------
            */

            /*
             * A payment should have only one official receipt.
             */
            $table->unique(
                'payment_id',
                'payment_receipts_payment_unique'
            );

            /*
             * Useful for receipt searches and verification.
             */
            $table->index([
                'language',
                'issued_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};