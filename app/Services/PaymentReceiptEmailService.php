<?php

namespace App\Services;

use App\Mail\PaymentReceiptMail;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PaymentReceiptEmailService
{
    /**
     * Send the official receipt to the tenant.
     *
     * The receipt must already exist.
     */
    public function send(Payment $payment): void
    {
        $payment = $payment->fresh([
            'receipt',
        ]);

        $receipt = $payment->receipt;

        if (!$receipt) {
            throw new RuntimeException(
                'Cannot email receipt because the payment does not have an official receipt.'
            );
        }

        /*
         * Do not send the same receipt twice.
         */
        if ($receipt->email_sent_at !== null) {
            return;
        }

        /*
         * Get the tenant email from the receipt data service.
         */
        $data = app(
            PaymentReceiptDataService::class
        )->build($payment);

        $email =
            $data['tenant']['email'] ?? null;

        if (!$email) {
            Log::warning(
                'Payment receipt email skipped because the tenant has no email address.',
                [
                    'payment_id' =>
                        $payment->id,

                    'receipt_id' =>
                        $receipt->id,
                ]
            );

            return;
        }

        /*
         * Send the official receipt.
         */
        Mail::to($email)
            ->send(
                new PaymentReceiptMail(
                    $payment
                )
            );

        /*
         * Only mark the receipt as emailed after
         * Laravel successfully hands the message to
         * the configured mail transport.
         */
        $receipt->update([
            'email_sent_at' => now(),
        ]);
    }
}