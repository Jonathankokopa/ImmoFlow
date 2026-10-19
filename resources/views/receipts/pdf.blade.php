<!DOCTYPE html>
<html lang="{{ $data['receipt']['language'] ?? 'en' }}">
<head>
    <meta charset="UTF-8">

    <title>{{ $data['receipt']['number'] ?? 'Receipt' }}</title>

    <style>
        @page {
            margin: 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
        }

        .property-name {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        .muted {
            color: #6b7280;
        }

        .title {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #111827;
        }

        .status {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
        }

        .amount-box {
            background: #f3f4f6;
            padding: 15px;
            margin: 18px 0;
            border-left: 4px solid #111827;
        }

        .amount-label {
            color: #6b7280;
            font-size: 10px;
        }

        .amount {
            font-size: 25px;
            font-weight: bold;
            color: #111827;
        }

        .grid {
            width: 100%;
            margin-bottom: 18px;
        }

        .grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px 0 0;
        }

        .grid td:last-child {
            padding-right: 0;
            padding-left: 10px;
        }

        .section {
            border: 1px solid #d1d5db;
            padding: 12px;
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .label {
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
        }

        .value {
            font-size: 11px;
            margin-bottom: 7px;
        }

        table.breakdown {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.breakdown th {
            background: #111827;
            color: white;
            text-align: left;
            padding: 8px;
            font-size: 10px;
        }

        table.breakdown td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
        }

        .right {
            text-align: right;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .summary td {
            padding: 5px 0;
        }

        .summary .total {
            border-top: 2px solid #111827;
            font-weight: bold;
            font-size: 13px;
            padding-top: 9px;
        }

        .words {
            background: #f9fafb;
            padding: 12px;
            margin-top: 18px;
        }

        .footer {
            border-top: 1px solid #d1d5db;
            margin-top: 25px;
            padding-top: 10px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>

<body>

@php
    $receipt = $data['receipt'];
    $payment = $data['payment'];
    $tenant = $data['tenant'];
    $property = $data['property'];
    $unit = $data['unit'];
    $invoice = $data['invoice'];
    $allocations = $data['allocations'];
    $balance = $data['balance'];
    $currency = $data['currency'];
    $amount = $data['amount'];
    $amountFormatted = $data['amount_formatted'];
    $billingPeriod = $data['billing_period'];
    $amountInWords = $data['amount_in_words'];
@endphp

<div class="header">
    <div class="brand">KR</div>

    <div class="property-name">
        {{ $property['name'] ?? 'KopsRent Property' }}
    </div>

    <div class="muted">
        {{ $property['address'] ?? '' }}

        @if (!empty($property['city']))
            · {{ $property['city'] }}
        @endif
    </div>
</div>

<table class="grid">
    <tr>
        <td>
            <div class="title">
                {{ __('receipt.receipt') }}
            </div>

            <span class="status">
                {{ __('receipt.paid') }}
            </span>
        </td>

        <td class="right">
            <div class="label">
                {{ __('receipt.receipt_no') }}
            </div>

            <div class="value">
                {{ $receipt['number'] }}
            </div>

            <div class="label">
                {{ __('receipt.issued') }}
            </div>

            <div class="value">
                {{ $receipt['issued_at_formatted'] }}
            </div>
        </td>
    </tr>
</table>

<div class="amount-box">
    <div class="amount-label">
        {{ __('receipt.amount_paid') }}
    </div>

    <div class="amount">
        {{ $currency }} {{ $amountFormatted }}
    </div>
</div>

<table class="grid">
    <tr>
        <td>
            <div class="section">
                <div class="section-title">
                    {{ __('receipt.tenant') }}
                </div>

                <div class="label">
                    {{ __('receipt.name') }}
                </div>

                <div class="value">
                    {{ $tenant['name'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.phone') }}
                </div>

                <div class="value">
                    {{ $tenant['phone'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.email') }}
                </div>

                <div class="value">
                    {{ $tenant['email'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.id_passport') }}
                </div>

                <div class="value">
                    {{ $tenant['id_passport'] ?? 'N/A' }}
                </div>
            </div>
        </td>

        <td>
            <div class="section">
                <div class="section-title">
                    {{ __('receipt.property') }}
                </div>

                <div class="label">
                    {{ __('receipt.property') }}
                </div>

                <div class="value">
                    {{ $property['name'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.unit') }}
                </div>

                <div class="value">
                    {{ $unit['number'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.invoice') }}
                </div>

                <div class="value">
                    {{ $invoice['number'] ?? 'N/A' }}
                </div>

                <div class="label">
                    {{ __('receipt.billing_period') }}
                </div>

                <div class="value">
                    {{ $billingPeriod ?: 'N/A' }}
                </div>
            </div>
        </td>
    </tr>
</table>

<div class="section">
    <div class="section-title">
        {{ __('receipt.breakdown') }}
    </div>

    <table class="breakdown">
        <thead>
            <tr>
                <th>
                    {{ __('receipt.particulars') }}
                </th>

                <th class="right">
                    {{ __('receipt.invoice_amount') }}
                </th>

                <th class="right">
                    {{ __('receipt.received') }}
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($allocations as $allocation)
                <tr>
                    <td>
                        {{ $allocation['invoice_number'] ?? 'Invoice' }}
                    </td>

                    <td class="right">
                        {{ $currency }}
                        {{ $invoice['amount_formatted'] ?? '0.00' }}
                    </td>

                    <td class="right">
                        {{ $currency }}
                        {{ $allocation['amount_formatted'] ?? '0.00' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td>
                        {{ $invoice['number'] ?? 'Invoice' }}
                    </td>

                    <td class="right">
                        {{ $currency }}
                        {{ $invoice['amount_formatted'] ?? '0.00' }}
                    </td>

                    <td class="right">
                        {{ $currency }}
                        {{ $amountFormatted }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>
                {{ __('receipt.amount_paid') }}
            </td>

            <td class="right">
                {{ $currency }} {{ $amountFormatted }}
            </td>
        </tr>

        <tr>
            <td>
                {{ __('receipt.credit_created') }}
            </td>

            <td class="right">
                {{ $currency }} {{ $balance['credit_formatted'] }}
            </td>
        </tr>

        <tr>
            <td>
                {{ __('receipt.unallocated') }}
            </td>

            <td class="right">
                {{ $currency }} {{ $balance['credit_formatted'] }}
            </td>
        </tr>

        <tr>
            <td class="total">
                {{ __('receipt.balance') }}
            </td>

            <td class="right total">
                {{ $currency }} {{ $balance['outstanding_formatted'] }}
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">
        {{ __('receipt.payment') }}
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="label">
                    {{ __('receipt.method') }}
                </div>

                <div class="value">
                    {{ ucfirst(str_replace('_', ' ', $payment['method'] ?? 'other')) }}
                </div>
            </td>

            <td>
                <div class="label">
                    {{ __('receipt.reference') }}
                </div>

                <div class="value">
                    {{ $payment['reference'] ?? 'N/A' }}
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="label">
                    {{ __('receipt.issued') }}
                </div>

                <div class="value">
                    {{ $payment['paid_at_formatted'] ?? $receipt['issued_at_formatted'] }}
                </div>
            </td>

            <td>
                <div class="label">
                    {{ __('receipt.invoice') }}
                </div>

                <div class="value">
                    {{ $invoice['number'] ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="words">
    <strong>
        {{ __('receipt.amount_words') }}
    </strong>

    <br>

    {{ $amountInWords }}
</div>

<div class="footer">
    {{ __('receipt.system_generated') }}

    <br>

    {{ __('receipt.powered') }}
</div>

</body>
</html>