<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\RentInvoice;
use App\Models\TenantCredit;
use App\Models\TenantCreditAllocation;
use App\Support\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Create a manual payment and mark it completed.
     *
     * The payment is first allocated to the invoice it was
     * created for.
     *
     * Any remaining amount is then allocated to the tenant's
     * other outstanding invoices in the same currency.
     *
     * Any amount remaining after all outstanding invoices are
     * paid becomes tenant credit.
     *
     * A receipt is automatically issued after successful
     * allocation.
     *
     * The receipt email is sent only after the database
     * transaction has successfully committed.
     */
    public function createManualPayment(
        RentInvoice $invoice,
        array $data,
        ?int $recordedBy = null
    ): Payment {
        $payment = DB::transaction(function () use (
            $invoice,
            $data,
            $recordedBy
        ) {
            $invoice = RentInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            $this->ensureInvoiceCanReceivePayment($invoice);

            $amount = $this->validatePaymentAmount(
                $data['amount'] ?? null
            );

            $currency = $this->validateCurrency(
                $invoice->currency
            );

            $payment = Payment::create([
                'rent_invoice_id' => $invoice->id,
                'amount' => $amount,
                'currency' => $currency,
                'payment_date' =>
                    $data['payment_date']
                    ?? now()->toDateString(),
                'payment_method' =>
                    $data['payment_method']
                    ?? Payment::METHOD_OTHER,
                'provider' => null,
                'transaction_reference' =>
                    $data['transaction_reference']
                    ?? null,
                'status' =>
                    Payment::STATUS_COMPLETED,
                'metadata' =>
                    $data['metadata']
                    ?? null,
                'notes' =>
                    $data['notes']
                    ?? null,
                'recorded_by' => $recordedBy,
            ]);

            /*
             * Allocate the completed payment first.
             */
            $this->allocatePayment(
                $payment,
                $invoice
            );

            /*
             * Issue the official receipt only after the
             * payment has been successfully allocated.
             *
             * PaymentReceiptService is idempotent, so this
             * cannot create a duplicate receipt for the
             * same payment.
             */
            app(PaymentReceiptService::class)
                ->createForPayment($payment);

            return $payment->fresh([
                'allocations.rentInvoice',
                'tenantCredits',
                'receipt',
            ]);
        });

        /*
         * IMPORTANT:
         *
         * Send the email AFTER the transaction has committed.
         *
         * This means a mail server failure will not cause the
         * successful payment/accounting transaction to roll back.
         */
        app(PaymentReceiptEmailService::class)
            ->send($payment);

        try {
            $tenantName = $payment->rentInvoice?->lease?->tenant?->full_name ?? 'a tenant';
            $amountFormatted = number_format((float) $payment->amount, 2);
            \Illuminate\Support\Facades\Notification::send(
                \App\Models\User::all(),
                new \App\Notifications\SystemNotification(
                    title: 'Payment Received',
                    message: "Payment of {$payment->currency} {$amountFormatted} recorded for {$tenantName}.",
                    url: route('payments.show', $payment->id),
                    icon: 'bi-cash-coin',
                    badgeColor: 'bg-success',
                    type: 'payment'
                )
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return $payment->fresh([
            'allocations.rentInvoice',
            'tenantCredits',
            'receipt',
        ]);
    }

    /**
     * Create a pending online payment.
     *
     * No invoice allocation or tenant credit is created here.
     *
     * Allocation happens only after the provider confirms
     * successful payment through a verified callback/webhook.
     */
    public function createPendingPayment(
        RentInvoice $invoice,
        float $amount,
        string $provider,
        string $paymentMethod,
        ?string $transactionReference = null,
        array $metadata = [],
        ?int $recordedBy = null
    ): Payment {
        return DB::transaction(function () use (
            $invoice,
            $amount,
            $provider,
            $paymentMethod,
            $transactionReference,
            $metadata,
            $recordedBy
        ) {
            $invoice = RentInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            $this->ensureInvoiceCanReceivePayment($invoice);

            $amount = $this->validatePaymentAmount(
                $amount
            );

            $currency = $this->validateCurrency(
                $invoice->currency
            );

            return Payment::create([
                'rent_invoice_id' => $invoice->id,
                'amount' => $amount,
                'currency' => $currency,
                'payment_date' =>
                    now()->toDateString(),
                'payment_method' =>
                    $paymentMethod,
                'provider' =>
                    $provider,
                'transaction_reference' =>
                    $transactionReference,
                'status' =>
                    Payment::STATUS_PENDING,
                'metadata' =>
                    $metadata ?: null,
                'notes' => null,
                'recorded_by' =>
                    $recordedBy,
            ]);
        });
    }

    /**
     * Mark a pending provider payment as completed.
     *
     * This should normally be called by a verified provider
     * callback/webhook handler.
     *
     * Important:
     * We intentionally do NOT call ensureInvoiceCanReceivePayment()
     * here.
     *
     * A provider payment may have already been successfully
     * collected even if the original invoice was subsequently
     * paid by another payment.
     *
     * In that situation allocatePayment() first attempts to apply
     * the payment to the original invoice. If that invoice is
     * already fully paid, the payment is allocated to other
     * outstanding invoices belonging to the same tenant/currency,
     * or becomes tenant credit.
     *
     * A receipt is automatically issued after successful
     * allocation.
     *
     * The receipt email is sent only after the database
     * transaction has successfully committed.
     */
    public function completePayment(
        Payment $payment,
        ?string $transactionReference = null,
        array $metadata = []
    ): Payment {
        $payment = DB::transaction(function () use (
            $payment,
            $transactionReference,
            $metadata
        ) {
            $payment = Payment::lockForUpdate()
                ->findOrFail($payment->id);

            /*
             * Idempotency protection.
             *
             * If the provider sends the same successful callback
             * more than once, do not allocate the payment again.
             *
             * We still make sure a receipt exists. This also allows
             * older completed payments to receive their receipt if
             * they were completed before automatic receipt creation
             * was introduced.
             */
            if (
                $payment->status ===
                Payment::STATUS_COMPLETED
            ) {
                app(PaymentReceiptService::class)
                    ->createForPayment($payment);

                return $payment->fresh([
                    'allocations.rentInvoice',
                    'tenantCredits',
                    'receipt',
                ]);
            }

            if ($payment->isFinal()) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'This payment has already reached a final status.',
                ]);
            }

            /*
             * Lock the original invoice so the tenant relationship
             * and currency remain consistent while completing the
             * provider payment.
             */
            $invoice = RentInvoice::lockForUpdate()
                ->findOrFail(
                    $payment->rent_invoice_id
                );

            $this->validateCurrency(
                $payment->currency
            );

            if (
                $payment->currency !==
                $invoice->currency
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Payment currency does not match the invoice currency.',
                ]);
            }

            /*
             * DO NOT call ensureInvoiceCanReceivePayment() here.
             *
             * The payment may already have been collected by the
             * provider even if the invoice has since been paid.
             */

            if ($transactionReference !== null) {
                $payment->transaction_reference =
                    $transactionReference;
            }

            if (!empty($metadata)) {
                $payment->metadata = array_merge(
                    $payment->metadata ?? [],
                    $metadata
                );
            }

            $payment->status =
                Payment::STATUS_COMPLETED;

            $payment->payment_date =
                $payment->payment_date
                ?? now()->toDateString();

            $payment->save();

            /*
             * Allocate only after the payment has been
             * successfully marked completed.
             */
            $this->allocatePayment(
                $payment,
                $invoice
            );

            /*
             * Generate the official receipt only after
             * accounting allocation succeeds.
             *
             * The receipt service is idempotent.
             */
            app(PaymentReceiptService::class)
                ->createForPayment($payment);

            return $payment->fresh([
                'allocations.rentInvoice',
                'tenantCredits',
                'receipt',
            ]);
        });

        /*
         * IMPORTANT:
         *
         * Send the email AFTER the transaction has committed.
         *
         * Payment/accounting data is therefore safe even if
         * the mail server fails.
         *
         * PaymentReceiptEmailService also checks email_sent_at,
         * so repeated provider callbacks will not send duplicates.
         */
        app(PaymentReceiptEmailService::class)
            ->send($payment);

        return $payment->fresh([
            'allocations.rentInvoice',
            'tenantCredits',
            'receipt',
        ]);
    }

    /**
     * Allocate a completed payment.
     *
     * The invoice that the payment was originally initiated for
     * always receives priority.
     *
     * If money remains after paying that invoice, the remainder is
     * allocated to the tenant's other outstanding invoices in
     * billing-period order.
     *
     * Any amount remaining after all outstanding invoices are paid
     * becomes tenant credit.
     */
    private function allocatePayment(
        Payment $payment,
        RentInvoice $startingInvoice
    ): void {
        if (
            $payment->status !==
            Payment::STATUS_COMPLETED
        ) {
            throw ValidationException::withMessages([
                'payment' =>
                    'Only completed payments can be allocated.',
            ]);
        }

        /*
         * Never allocate the same payment twice.
         */
        if ($payment->allocations()->exists()) {
            return;
        }

        /*
         * The original invoice identifies the tenant.
         */
        $tenantId = $startingInvoice
            ->lease()
            ->value('tenant_id');

        if (!$tenantId) {
            throw ValidationException::withMessages([
                'payment' =>
                    'The invoice does not belong to a valid tenant.',
            ]);
        }

        $currency = $this->validateCurrency(
            $payment->currency
        );

        if ($currency !== $startingInvoice->currency) {
            throw ValidationException::withMessages([
                'payment' =>
                    'Payment currency does not match the invoice currency.',
            ]);
        }

        $remaining = round(
            (float) $payment->amount,
            2
        );

        /*
         * ---------------------------------------------------------
         * 1. Allocate to the invoice the payment was initiated for.
         * ---------------------------------------------------------
         *
         * If the tenant selected Invoice #2 for payment, Invoice #2
         * must receive the payment first.
         *
         * This prevents an older invoice from incorrectly receiving
         * money intended for the selected invoice.
         */
        $startingInvoice = RentInvoice::query()
            ->lockForUpdate()
            ->findOrFail(
                $startingInvoice->id
            );

        if (
            $startingInvoice->status !==
            RentInvoice::STATUS_CANCELLED
        ) {
            $outstanding =
                $startingInvoice->outstanding_amount;

            if ($outstanding > 0) {
                $allocationAmount = round(
                    min(
                        $remaining,
                        $outstanding
                    ),
                    2
                );

                if ($allocationAmount > 0) {
                    PaymentAllocation::create([
                        'payment_id' =>
                            $payment->id,

                        'rent_invoice_id' =>
                            $startingInvoice->id,

                        'amount' =>
                            $allocationAmount,
                    ]);

                    $startingInvoice
                        ->recalculatePaymentStatus();

                    $remaining = round(
                        $remaining - $allocationAmount,
                        2
                    );
                }
            }
        }

        /*
         * ---------------------------------------------------------
         * 2. Allocate any remainder to other invoices.
         * ---------------------------------------------------------
         *
         * Only invoices belonging to the same tenant and currency
         * are eligible.
         *
         * The original invoice is excluded because it was already
         * handled above.
         */
        if ($remaining > 0) {
            $invoices = RentInvoice::query()
                ->whereHas('lease', function ($query) use ($tenantId) {
                    $query->where(
                        'tenant_id',
                        $tenantId
                    );
                })
                ->where(
                    'currency',
                    $currency
                )
                ->where(
                    'id',
                    '!=',
                    $startingInvoice->id
                )
                ->where(
                    'status',
                    '!=',
                    RentInvoice::STATUS_CANCELLED
                )
                ->where(function ($query) {
                    $query
                        ->whereColumn(
                            'amount_paid',
                            '<',
                            'amount'
                        )
                        ->orWhere(
                            'status',
                            '!=',
                            RentInvoice::STATUS_PAID
                        );
                })
                ->orderBy('billing_period')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($invoices as $invoice) {
                if ($remaining <= 0) {
                    break;
                }

                $invoice->refresh();

                if (
                    $invoice->status ===
                    RentInvoice::STATUS_CANCELLED
                ) {
                    continue;
                }

                $outstanding =
                    $invoice->outstanding_amount;

                if ($outstanding <= 0) {
                    continue;
                }

                $allocationAmount = round(
                    min(
                        $remaining,
                        $outstanding
                    ),
                    2
                );

                if ($allocationAmount <= 0) {
                    continue;
                }

                PaymentAllocation::create([
                    'payment_id' =>
                        $payment->id,

                    'rent_invoice_id' =>
                        $invoice->id,

                    'amount' =>
                        $allocationAmount,
                ]);

                $invoice->recalculatePaymentStatus();

                $remaining = round(
                    $remaining - $allocationAmount,
                    2
                );
            }
        }

        /*
         * ---------------------------------------------------------
         * 3. Convert any remaining amount into tenant credit.
         * ---------------------------------------------------------
         */
        if ($remaining > 0) {
            TenantCredit::create([
                'tenant_id' =>
                    $tenantId,

                'payment_id' =>
                    $payment->id,

                'amount' =>
                    $remaining,

                'remaining_amount' =>
                    $remaining,

                'currency' =>
                    $currency,

                'status' =>
                    TenantCredit::STATUS_AVAILABLE,

                'notes' =>
                    'Credit created from payment '
                    . $payment->id
                    . ' after invoice allocation.',
            ]);
        }
    }

    /**
     * Mark a pending payment as failed.
     */
    public function failPayment(
        Payment $payment,
        array $metadata = []
    ): Payment {
        return DB::transaction(function () use (
            $payment,
            $metadata
        ) {
            $payment = Payment::lockForUpdate()
                ->findOrFail($payment->id);

            if (
                $payment->status ===
                Payment::STATUS_FAILED
            ) {
                return $payment;
            }

            if ($payment->isFinal()) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'This payment has already reached a final status.',
                ]);
            }

            $payment->status =
                Payment::STATUS_FAILED;

            if (!empty($metadata)) {
                $payment->metadata = array_merge(
                    $payment->metadata ?? [],
                    $metadata
                );
            }

            $payment->save();

            return $payment;
        });
    }

    /**
     * Cancel a pending payment.
     */
    public function cancelPayment(
        Payment $payment,
        array $metadata = []
    ): Payment {
        return DB::transaction(function () use (
            $payment,
            $metadata
        ) {
            $payment = Payment::lockForUpdate()
                ->findOrFail($payment->id);

            if (
                $payment->status ===
                Payment::STATUS_CANCELLED
            ) {
                return $payment;
            }

            if ($payment->isFinal()) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'This payment has already reached a final status.',
                ]);
            }

            $payment->status =
                Payment::STATUS_CANCELLED;

            if (!empty($metadata)) {
                $payment->metadata = array_merge(
                    $payment->metadata ?? [],
                    $metadata
                );
            }

            $payment->save();

            return $payment;
        });
    }

    /**
     * Refund a completed payment.
     *
     * This reverses:
     *
     * 1. Direct payment allocations.
     * 2. Tenant credit allocations created from this payment.
     * 3. Any remaining tenant credit created from this payment.
     *
     * Historical allocation records are preserved.
     *
     * The original receipt is intentionally preserved because
     * it represents a payment that was genuinely received and
     * officially receipted before the refund.
     */
    public function refundPayment(
        Payment $payment,
        array $metadata = []
    ): Payment {
        return DB::transaction(function () use (
            $payment,
            $metadata
        ) {
            $payment = Payment::lockForUpdate()
                ->findOrFail($payment->id);

            if (
                $payment->status ===
                Payment::STATUS_REFUNDED
            ) {
                return $payment->fresh([
                    'allocations.rentInvoice',
                    'tenantCredits.allocations.rentInvoice',
                    'receipt',
                ]);
            }

            if (
                $payment->status !==
                Payment::STATUS_COMPLETED
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Only completed payments can be refunded.',
                ]);
            }

            /*
             * Lock all direct payment allocations.
             */
            $paymentAllocations = $payment
                ->allocations()
                ->with('rentInvoice')
                ->lockForUpdate()
                ->get();

            /*
             * Lock all tenant credits created by this payment.
             */
            $credits = TenantCredit::query()
                ->where(
                    'payment_id',
                    $payment->id
                )
                ->lockForUpdate()
                ->get();

            $affectedInvoiceIds = [];

            /*
             * Direct payment allocations remain as historical
             * accounting records.
             */
            foreach ($paymentAllocations as $allocation) {
                $affectedInvoiceIds[] =
                    $allocation->rent_invoice_id;
            }

            /*
             * Reverse every credit allocation created from
             * credits belonging to this payment.
             */
            foreach ($credits as $credit) {
                $creditAllocations =
                    TenantCreditAllocation::query()
                        ->where(
                            'tenant_credit_id',
                            $credit->id
                        )
                        ->lockForUpdate()
                        ->get();

                foreach ($creditAllocations as $creditAllocation) {
                    $affectedInvoiceIds[] =
                        $creditAllocation->rent_invoice_id;
                }

                $credit->status =
                    TenantCredit::STATUS_REFUNDED;

                $credit->remaining_amount = 0;

                $credit->save();
            }

            /*
             * Mark the payment as refunded.
             */
            $payment->status =
                Payment::STATUS_REFUNDED;

            if (!empty($metadata)) {
                $payment->metadata = array_merge(
                    $payment->metadata ?? [],
                    $metadata
                );
            }

            $payment->save();

            /*
             * Recalculate every invoice affected by this payment
             * or by a tenant credit created from this payment.
             */
            $affectedInvoiceIds =
                array_values(
                    array_unique(
                        $affectedInvoiceIds
                    )
                );

            foreach ($affectedInvoiceIds as $invoiceId) {
                $invoice = RentInvoice::lockForUpdate()
                    ->find($invoiceId);

                if (!$invoice) {
                    continue;
                }

                if (
                    $invoice->status !==
                    RentInvoice::STATUS_CANCELLED
                ) {
                    $invoice->recalculatePaymentStatus();
                }
            }

            return $payment->fresh([
                'allocations.rentInvoice',
                'tenantCredits.allocations.rentInvoice',
                'receipt',
            ]);
        });
    }

    /**
     * Ensure the invoice is eligible to receive a new payment.
     *
     * This is used when STARTING a payment.
     *
     * It is intentionally NOT used when completing an already
     * initiated external/provider payment.
     */
    private function ensureInvoiceCanReceivePayment(
        RentInvoice $invoice
    ): void {
        if (
            $invoice->status ===
            RentInvoice::STATUS_CANCELLED
        ) {
            throw ValidationException::withMessages([
                'invoice' =>
                    'A cancelled invoice cannot receive payments.',
            ]);
        }

        $this->validateCurrency(
            $invoice->currency
        );

        if (
            (float) $invoice->amount <= 0
        ) {
            throw ValidationException::withMessages([
                'invoice' =>
                    'The invoice does not have a valid amount.',
            ]);
        }

        if (
            $invoice->outstanding_amount <= 0
        ) {
            throw ValidationException::withMessages([
                'invoice' =>
                    'This invoice has already been fully paid.',
            ]);
        }
    }

    /**
     * Validate the invoice/payment currency.
     */
    private function validateCurrency(
        string $currency
    ): string {
        $currency = strtoupper(
            trim($currency)
        );

        if (!in_array(
            $currency,
            Currency::SUPPORTED,
            true
        )) {
            throw ValidationException::withMessages([
                'currency' =>
                    'The selected currency is not supported.',
            ]);
        }

        return $currency;
    }

    /**
     * Validate the raw payment amount.
     */
    private function validatePaymentAmount(
        mixed $amount
    ): float {
        if (
            $amount === null ||
            !is_numeric($amount)
        ) {
            throw ValidationException::withMessages([
                'amount' =>
                    'A valid payment amount is required.',
            ]);
        }

        $amount = round(
            (float) $amount,
            2
        );

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' =>
                    'Payment amount must be greater than zero.',
            ]);
        }

        return $amount;
    }
}