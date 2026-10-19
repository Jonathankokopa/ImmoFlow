<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Receipt Verification
    </title>

    <style>
        body {
            margin: 0;
            padding: 40px 20px;
            background: #f5f7fa;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            color: #1f2937;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 32px;
            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.08);
        }

        .verified {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-weight: 700;
            font-size: 14px;
        }

        h1 {
            margin: 20px 0 8px;
            font-size: 28px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 28px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .row:last-child {
            border-bottom: 0;
        }

        .label {
            color: #6b7280;
        }

        .value {
            font-weight: 600;
            text-align: right;
        }

        .amount {
            margin: 24px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            text-align: center;
        }

        .amount-label {
            color: #6b7280;
            font-size: 14px;
        }

        .amount-value {
            margin-top: 8px;
            font-size: 32px;
            font-weight: 800;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <span class="verified">
            ✓ Payment Verified
        </span>

        <h1>
            Rent Payment Receipt
        </h1>

        <div class="subtitle">
            This receipt has been verified
            against the KopsRent payment record.
        </div>

        <div class="amount">

            <div class="amount-label">
                Amount Paid
            </div>

            <div class="amount-value">
                {{ $payment->formatted_amount }}
            </div>

        </div>

        <div class="row">
            <div class="label">
                Receipt Number
            </div>

            <div class="value">
                {{ $receipt->receipt_number }}
            </div>
        </div>

        <div class="row">
            <div class="label">
                Payment Date
            </div>

            <div class="value">
                {{ $payment->payment_date?->format('d M Y') }}
            </div>
        </div>

        <div class="row">
            <div class="label">
                Payment Method
            </div>

            <div class="value">
                {{ ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $payment->payment_method
                    )
                ) }}
            </div>
        </div>

        @if ($payment->transaction_reference)

            <div class="row">

                <div class="label">
                    Payment Reference
                </div>

                <div class="value">
                    {{ $payment->transaction_reference }}
                </div>

            </div>

        @endif

        @if ($payment->rentInvoice?->lease?->tenant)

            <div class="row">

                <div class="label">
                    Tenant
                </div>

                <div class="value">
                    {{ $payment->rentInvoice->lease->tenant->full_name }}
                </div>

            </div>

        @endif

        @if ($payment->rentInvoice?->lease?->unit)

            <div class="row">

                <div class="label">
                    Unit
                </div>

                <div class="value">
                    {{ $payment->rentInvoice->lease->unit->unit_number }}
                </div>

            </div>

        @endif

        @if ($payment->rentInvoice)

            <div class="row">

                <div class="label">
                    Invoice
                </div>

                <div class="value">
                    {{ $payment->rentInvoice->invoice_number }}
                </div>

            </div>

        @endif

        <div class="footer">
            This is a system-generated receipt.
            <br>
            Receipt verification powered by KopsRent.
        </div>

    </div>

</div>

</body>
</html>