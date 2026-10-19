<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Carbon;

class PaymentReceiptDataService
{
    /**
     * Build the canonical data structure used by:
     * - PDF receipt
     * - Email receipt
     * - Web receipt
     */
    public function build(Payment $payment): array
    {
        $payment->loadMissing([
            'receipt',
            'rentInvoice.lease.tenant',
            'rentInvoice.lease.unit.property',
            'allocations.rentInvoice',
        ]);

        $receipt = $payment->receipt;
        $invoice = $payment->rentInvoice;
        $lease = $invoice?->lease;
        $tenant = $lease?->tenant;
        $unit = $lease?->unit;
        $property = $unit?->property;

        $currency = $payment->currency
            ?: ($invoice?->currency ?? 'KES');

        $amount = (float) $payment->amount;

        /*
         * Receipt
         */
        $issuedAt = $receipt?->issued_at
            ? Carbon::parse($receipt->issued_at)
            : now();

        /*
         * Payment date
         */
        $paymentDate = $payment->payment_date
            ? Carbon::parse($payment->payment_date)
            : null;

        /*
         * Invoice
         */
        $invoiceAmount = $invoice
            ? (float) $invoice->amount
            : 0.0;

        $invoiceAmountPaid = $invoice
            ? (float) $invoice->amount_paid
            : 0.0;

        $billingPeriod = $invoice?->billing_period
            ? Carbon::parse($invoice->billing_period)
            : null;

        $billingPeriodFormatted = $billingPeriod
            ? $billingPeriod->format('F Y')
            : '';

        /*
         * Payment allocations
         */
        $allocations = [];

        foreach ($payment->allocations as $allocation) {
            $allocationInvoice = $allocation->rentInvoice;

            $allocationAmount = (float) $allocation->amount;

            $allocationCurrency = $allocationInvoice?->currency
                ?? $currency;

            $allocations[] = [
                'invoice_id' => $allocationInvoice?->id,

                'invoice_number' =>
                    $allocationInvoice?->invoice_number ?? '',

                'amount' => $allocationAmount,

                'amount_formatted' => number_format(
                    $allocationAmount,
                    2,
                    '.',
                    ','
                ),

                'currency' => $allocationCurrency,
            ];
        }

        /*
         * Total allocated by this payment
         */
        $allocatedAmount = round(
            collect($allocations)->sum('amount'),
            2
        );

        /*
         * Remaining invoice balance after this payment
         */
        $outstandingAmount = round(
            max(0, $invoiceAmount - $allocatedAmount),
            2
        );

        /*
         * Tenant credit created by this payment
         */
        $creditAmount = round(
            (float) $payment->tenantCredits()->sum('amount'),
            2
        );

        /*
         * Amount not allocated to an invoice or credit
         */
        $unallocatedAmount = round(
            max(
                0,
                $amount - $allocatedAmount - $creditAmount
            ),
            2
        );

        /*
         * Allocation summary
         */
        $allocationSummary = collect($allocations)
            ->map(function (array $allocation): string {
                $invoiceNumber = $allocation['invoice_number']
                    ?: 'Unspecified invoice';

                return $invoiceNumber
                    . ': '
                    . $allocation['amount_formatted']
                    . ' '
                    . $allocation['currency'];
            })
            ->implode(', ');

        /*
         * Tenant
         *
         * Tenant model uses `full_name`.
         */
        $tenantName = $tenant?->full_name ?? '';

        /*
         * Amount in words
         */
        $amountInWords = $this->amountInWords(
            $amount,
            $currency
        );

        return [
            /*
             * Receipt
             */
            'receipt' => [
                'id' => $receipt?->id,

                'number' =>
                    $receipt?->receipt_number ?? '',

                'language' =>
                    $receipt?->language ?? 'en',

                'issued_at' => $issuedAt,

                'issued_at_formatted' =>
                    $issuedAt->format('d M Y H:i'),
            ],

            /*
             * Payment
             */
            'payment' => [
                'id' => $payment->id,

                'amount' => $amount,

                'currency' => $currency,

                'amount_formatted' => number_format(
                    $amount,
                    2,
                    '.',
                    ','
                ),

                'paid_at' => $paymentDate,

                'paid_at_formatted' => $paymentDate
                    ? $paymentDate->format('d M Y')
                    : '',

                'method' => $this->paymentMethodLabel(
                    $payment->payment_method
                ),

                'provider' =>
                    $payment->provider ?? '',

                'reference' =>
                    $payment->transaction_reference ?? '',
            ],

            /*
             * Tenant
             */
            'tenant' => [
                'id' => $tenant?->id,

                'name' => $tenantName,

                'phone' =>
                    $tenant?->phone ?? '',

                'email' =>
                    $tenant?->email ?? '',

                'address' =>
                    $tenant?->address ?? '',

                'city' =>
                    $tenant?->city ?? '',

                'id_passport' =>
                    $tenant?->national_id ?? '',
            ],

            /*
             * Property
             */
            'property' => [
                'id' => $property?->id,

                'name' =>
                    $property?->name ?? '',

                'address' =>
                    $property?->address ?? '',

                'city' =>
                    $property?->city ?? '',
            ],

            /*
             * Unit
             */
            'unit' => [
                'id' => $unit?->id,

                'name' =>
                    $unit?->name ?? '',

                'number' =>
                    $unit?->unit_number
                    ?? $unit?->number
                    ?? $unit?->name
                    ?? '',
            ],

            /*
             * Lease
             */
            'lease' => [
                'id' => $lease?->id,

                'start_date' =>
                    $lease?->start_date,

                'end_date' =>
                    $lease?->end_date,

                'start_date_formatted' =>
                    $lease?->start_date
                        ? Carbon::parse(
                            $lease->start_date
                        )->format('d M Y')
                        : '',

                'end_date_formatted' =>
                    $lease?->end_date
                        ? Carbon::parse(
                            $lease->end_date
                        )->format('d M Y')
                        : '',
            ],

            /*
             * Invoice
             */
            'invoice' => [
                'id' => $invoice?->id,

                'number' =>
                    $invoice?->invoice_number ?? '',

                'amount' => $invoiceAmount,

                'amount_formatted' => number_format(
                    $invoiceAmount,
                    2,
                    '.',
                    ','
                ),

                'amount_paid' =>
                    $invoiceAmountPaid,

                'amount_paid_formatted' => number_format(
                    $invoiceAmountPaid,
                    2,
                    '.',
                    ','
                ),

                'currency' =>
                    $invoice?->currency ?? $currency,

                'billing_period' =>
                    $billingPeriod,

                'billing_period_formatted' =>
                    $billingPeriodFormatted,

                'due_date' =>
                    $invoice?->due_date,

                'due_date_formatted' =>
                    $invoice?->due_date
                        ? Carbon::parse(
                            $invoice->due_date
                        )->format('d M Y')
                        : '',
            ],

            /*
             * Billing period
             */
            'billing_period' =>
                $billingPeriodFormatted,

            /*
             * Allocations
             */
            'allocations' =>
                $allocations,

            /*
             * Allocation summary
             */
            'allocation_summary' =>
                $allocationSummary,

            /*
             * Balance
             */
            'balance' => [
                'invoice_amount' =>
                    $invoiceAmount,

                'invoice_amount_formatted' =>
                    number_format(
                        $invoiceAmount,
                        2,
                        '.',
                        ','
                    ),

                'allocated' =>
                    $allocatedAmount,

                'allocated_formatted' =>
                    number_format(
                        $allocatedAmount,
                        2,
                        '.',
                        ','
                    ),

                'outstanding' =>
                    $outstandingAmount,

                'outstanding_formatted' =>
                    number_format(
                        $outstandingAmount,
                        2,
                        '.',
                        ','
                    ),

                'credit' =>
                    $creditAmount,

                'credit_formatted' =>
                    number_format(
                        $creditAmount,
                        2,
                        '.',
                        ','
                    ),

                'unallocated' =>
                    $unallocatedAmount,

                'unallocated_formatted' =>
                    number_format(
                        $unallocatedAmount,
                        2,
                        '.',
                        ','
                    ),
            ],

            /*
             * Amount in words
             */
            'amount_in_words' =>
                $amountInWords,

            /*
             * Top-level currency
             */
            'currency' =>
                $currency,

            /*
             * Top-level amount
             */
            'amount' =>
                $amount,

            /*
             * Top-level formatted amount
             */
            'amount_formatted' =>
                number_format(
                    $amount,
                    2,
                    '.',
                    ','
                ),

            /*
             * Display values
             */
            'display' => [
                'currency' =>
                    $currency,

                'amount' =>
                    number_format(
                        $amount,
                        2,
                        '.',
                        ','
                    ),

                'invoice_amount' =>
                    number_format(
                        $invoiceAmount,
                        2,
                        '.',
                        ','
                    ),

                'allocated' =>
                    number_format(
                        $allocatedAmount,
                        2,
                        '.',
                        ','
                    ),

                'outstanding' =>
                    number_format(
                        $outstandingAmount,
                        2,
                        '.',
                        ','
                    ),

                'credit' =>
                    number_format(
                        $creditAmount,
                        2,
                        '.',
                        ','
                    ),

                'unallocated' =>
                    number_format(
                        $unallocatedAmount,
                        2,
                        '.',
                        ','
                    ),
            ],
        ];
    }

    /**
     * Convert an amount into words.
     *
     * Does not require PHP's Intl/NumberFormatter extension.
     */
    private function amountInWords(
        float $amount,
        string $currency
    ): string {
        $amount = round($amount, 2);

        $whole = (int) floor($amount);

        $fraction = (int) round(
            ($amount - $whole) * 100
        );

        $words = ucfirst(
            $this->numberToWords($whole)
        );

        $currencyName = match (strtoupper($currency)) {
            'KES' => 'Kenyan Shillings',
            'USD' => 'US Dollars',
            'EUR' => 'Euros',
            'GBP' => 'British Pounds',
            default => strtoupper($currency),
        };

        if ($fraction > 0) {
            return $words
                . ' '
                . $currencyName
                . ' and '
                . $fraction
                . '/100 only';
        }

        return $words
            . ' '
            . $currencyName
            . ' only';
    }

    /**
     * Convert an integer to English words.
     */
    private function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $ones = [
            '',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen',
        ];

        $tens = [
            '',
            '',
            'twenty',
            'thirty',
            'forty',
            'fifty',
            'sixty',
            'seventy',
            'eighty',
            'ninety',
        ];

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return $tens[intdiv($number, 10)]
                . (
                    $number % 10
                        ? ' ' . $ones[$number % 10]
                        : ''
                );
        }

        if ($number < 1000) {
            return $ones[intdiv($number, 100)]
                . ' hundred'
                . (
                    $number % 100
                        ? ' ' . $this->numberToWords(
                            $number % 100
                        )
                        : ''
                );
        }

        if ($number < 1000000) {
            return $this->numberToWords(
                intdiv($number, 1000)
            )
                . ' thousand'
                . (
                    $number % 1000
                        ? ' ' . $this->numberToWords(
                            $number % 1000
                        )
                        : ''
                );
        }

        if ($number < 1000000000) {
            return $this->numberToWords(
                intdiv($number, 1000000)
            )
                . ' million'
                . (
                    $number % 1000000
                        ? ' ' . $this->numberToWords(
                            $number % 1000000
                        )
                        : ''
                );
        }

        return $this->numberToWords(
            intdiv($number, 1000000000)
        )
            . ' billion'
            . (
                $number % 1000000000
                    ? ' ' . $this->numberToWords(
                        $number % 1000000000
                    )
                    : ''
            );
    }

    /**
     * Human-readable payment method.
     */
    private function paymentMethodLabel(
        ?string $method
    ): string {
        return match ($method) {
            Payment::METHOD_CASH =>
                'Cash',

            Payment::METHOD_BANK_TRANSFER =>
                'Bank Transfer',

            Payment::METHOD_MPESA =>
                'M-Pesa',

            Payment::METHOD_AIRTEL_MONEY =>
                'Airtel Money',

            Payment::METHOD_CARD =>
                'Card',

            Payment::METHOD_OTHER =>
                'Other',

            default =>
                $method
                    ? ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $method
                        )
                    )
                    : '',
        };
    }
}