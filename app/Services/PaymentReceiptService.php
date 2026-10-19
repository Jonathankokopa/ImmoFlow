<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentReceiptService
{
    /**
     * Create or retrieve the official receipt for a completed payment.
     *
     * Receipt creation is idempotent:
     * if the payment already has a receipt, the existing receipt
     * is returned instead of creating a duplicate.
     */
    public function createForPayment(
        Payment $payment,
        ?string $language = null
    ): PaymentReceipt {
        /*
         * Reload the payment so we work with the current database
         * state.
         *
         * If this method is called from an existing DB transaction,
         * do not create another transaction around it.
         */
        $payment = Payment::query()
            ->findOrFail($payment->id);

        /*
         * Receipts may only be issued for completed payments.
         */
        if (!$payment->isCompleted()) {
            throw new RuntimeException(
                'A receipt can only be generated for a completed payment.'
            );
        }

        /*
         * Idempotency:
         * one official receipt per payment.
         */
        $existingReceipt = PaymentReceipt::query()
            ->where('payment_id', $payment->id)
            ->first();

        if ($existingReceipt) {
            return $existingReceipt;
        }

        /*
         * Generate KopsRent's internal receipt number.
         *
         * Example:
         * RC-2026-000018
         */
        $receiptNumber = $this->generateReceiptNumber();

        /*
         * Default to English until tenant/property language
         * preferences are introduced.
         */
        $receiptLanguage = $language ?: 'en';

        /*
         * Only allow languages currently supported by the
         * receipt system.
         */
        if (!in_array(
            $receiptLanguage,
            ['en', 'fr'],
            true
        )) {
            $receiptLanguage = 'en';
        }

        return PaymentReceipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => $receiptNumber,
            'language' => $receiptLanguage,
            'issued_at' => now(),
            'verification_token' => (string) Str::uuid(),
        ]);
    }

    /**
     * Generate the next receipt number for the current year.
     *
     * This deliberately does NOT use count() + 1.
     *
     * Deleted receipts can leave gaps in the sequence, so count()
     * cannot safely determine the next receipt number.
     */
    protected function generateReceiptNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'RC-' . $year . '-';

        /*
         * Get the most recently numbered receipt for this year.
         *
         * Sorting by receipt_number works because the numeric part
         * is always zero-padded to six digits.
         */
        $latestReceipt = PaymentReceipt::query()
            ->where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('receipt_number')
            ->first();

        $number = 1;

        if ($latestReceipt) {
            $existingNumber = (int) substr(
                $latestReceipt->receipt_number,
                -6
            );

            $number = $existingNumber + 1;
        }

        /*
         * Protect against an unexpected existing number.
         *
         * Unlike the previous implementation, the candidate is
         * incremented each time, so this loop can never repeatedly
         * generate the exact same number.
         */
        do {
            $receiptNumber = sprintf(
                '%s%06d',
                $prefix,
                $number
            );

            $exists = PaymentReceipt::query()
                ->where(
                    'receipt_number',
                    $receiptNumber
                )
                ->exists();

            if ($exists) {
                $number++;
            }
        } while ($exists);

        return $receiptNumber;
    }
}