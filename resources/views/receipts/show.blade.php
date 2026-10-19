@php
    $receipt = $data['receipt'];
    $payment = $data['payment'];
    $tenant = $data['tenant'];
    $property = $data['property'];
    $unit = $data['unit'];
    $lease = $data['lease'];
    $invoice = $data['invoice'];
    $allocations = $data['allocations'];
    $summary = $data['allocation_summary'];
    $balance = $data['balance'];
    $display = $data['display'];

    /*
    |--------------------------------------------------------------------------
    | Receipt Locale
    |--------------------------------------------------------------------------
    |
    | The official receipt language is stored on payment_receipts.language.
    | Only English and French are currently supported.
    |
    */

    $locale = $receipt['language'] ?? 'en';

    if (!in_array($locale, ['en', 'fr'], true)) {
        $locale = 'en';
    }

    app()->setLocale($locale);

    $isFrench = $locale === 'fr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ __('receipt.receipt') }} —
        {{ $receipt['number'] }}
    </title>

    <style>

        :root {
            --ink: #172033;
            --muted: #697386;
            --line: #e5e9f0;
            --soft: #f6f8fb;
            --paper: #ffffff;
            --accent: #173f5f;
            --accent-soft: #edf4f8;
            --success: #087443;
            --success-soft: #eaf8f1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef1f5;
            color: var(--ink);

            font-family:
                Inter,
                ui-sans-serif,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;

            line-height: 1.5;
        }

        a {
            color: inherit;
        }

        .page {
            max-width: 960px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .receipt {
            background: var(--paper);
            border: 1px solid #e1e6ed;
            border-radius: 18px;
            overflow: hidden;

            box-shadow:
                0 18px 50px rgba(23, 32, 51, 0.08);
        }


        /* =====================================================
           HEADER
        ====================================================== */

        .header {
            padding: 34px 40px 30px;
            border-bottom: 1px solid var(--line);
        }

        .header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .brand-mark {
            width: 46px;
            height: 46px;

            border-radius: 12px;

            background: var(--accent);
            color: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 800;
            font-size: 20px;
            letter-spacing: -1px;
        }

        .brand-name {
            margin: 0;

            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .property-address {
            margin-top: 4px;

            color: var(--muted);
            font-size: 13px;

            max-width: 430px;
        }

        .receipt-title {
            text-align: right;
        }

        .receipt-title h1 {
            margin: 0;

            font-size: 27px;
            line-height: 1.1;

            letter-spacing: -0.7px;
        }

        .receipt-number {
            margin-top: 7px;

            color: var(--muted);
            font-size: 13px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            margin-top: 12px;
            padding: 7px 12px;

            border-radius: 999px;

            background: var(--success-soft);
            color: var(--success);

            font-size: 12px;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .status-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: currentColor;
        }


        /* =====================================================
           AMOUNT HERO
        ====================================================== */

        .amount-hero {
            margin: 28px 40px 0;
            padding: 25px 28px;

            background: var(--accent);
            color: #fff;

            border-radius: 15px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }

        .amount-label {
            font-size: 12px;
            opacity: .78;

            text-transform: uppercase;
            letter-spacing: 1px;

            font-weight: 700;
        }

        .amount {
            margin-top: 5px;

            font-size: 38px;
            line-height: 1;

            font-weight: 850;
            letter-spacing: -1.2px;
        }

        .amount-date {
            text-align: right;

            font-size: 13px;
            opacity: .86;
        }


        /* =====================================================
           META STRIP
        ====================================================== */

        .meta {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 1px;

            margin-top: 26px;

            background: var(--line);

            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .meta-item {
            padding: 18px 40px;

            background: #fff;
        }

        .meta-label {
            color: var(--muted);

            font-size: 11px;

            text-transform: uppercase;
            letter-spacing: .7px;

            font-weight: 700;
        }

        .meta-value {
            margin-top: 5px;

            font-size: 14px;
            font-weight: 700;

            overflow-wrap: anywhere;
        }


        /* =====================================================
           MAIN BODY
        ====================================================== */

        .body {
            padding: 34px 40px 40px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-heading {
            margin-bottom: 13px;

            font-size: 11px;
            color: var(--muted);

            text-transform: uppercase;
            letter-spacing: 1px;

            font-weight: 800;
        }


        /* =====================================================
           INFORMATION CARDS
        ====================================================== */

        .info-grid {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 14px;
        }

        .info-card {
            border: 1px solid var(--line);
            border-radius: 12px;

            padding: 17px;

            background: #fff;
        }

        .info-card h3 {
            margin: 0 0 12px;

            font-size: 13px;
            font-weight: 800;
        }

        .info-row {
            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 5px 0;

            font-size: 13px;
        }

        .info-row .label {
            color: var(--muted);
        }

        .info-row .value {
            text-align: right;
            font-weight: 600;

            overflow-wrap: anywhere;
        }


        /* =====================================================
           PAYMENT BREAKDOWN
        ====================================================== */

        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 12px;

            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 14px;

            background: var(--soft);
            color: var(--muted);

            text-align: left;

            font-size: 10px;

            text-transform: uppercase;
            letter-spacing: .7px;
        }

        td {
            padding: 14px;

            border-top: 1px solid var(--line);

            font-size: 13px;
            vertical-align: top;
        }

        th.amount-cell,
        td.amount-cell {
            text-align: right;
        }

        .description {
            font-weight: 700;
        }

        .subtext {
            display: block;

            margin-top: 2px;

            color: var(--muted);

            font-size: 11px;
        }


        /* =====================================================
           SUMMARY
        ====================================================== */

        .summary {
            display: flex;
            justify-content: flex-end;
        }

        .summary-box {
            width: 390px;

            border: 1px solid var(--line);
            border-radius: 12px;

            overflow: hidden;
        }

        .summary-row {
            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 13px 16px;

            font-size: 13px;

            border-bottom: 1px solid var(--line);
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .summary-row .label {
            color: var(--muted);
        }

        .summary-row strong {
            font-weight: 800;

            text-align: right;
        }

        .summary-row.balance {
            background: var(--soft);
        }

        .summary-row.balance .value {
            font-size: 16px;
        }


        /* =====================================================
           AMOUNT IN WORDS
        ====================================================== */

        .words {
            padding: 17px;

            border: 1px solid var(--line);
            border-radius: 12px;

            background: var(--soft);
        }

        .words-label {
            color: var(--muted);

            font-size: 11px;

            text-transform: uppercase;
            letter-spacing: .7px;

            font-weight: 800;
        }

        .words-value {
            margin-top: 6px;

            font-size: 14px;
            font-weight: 700;

            line-height: 1.6;
        }


        /* =====================================================
           FOOTER
        ====================================================== */

        .footer {
            padding: 22px 40px 28px;

            border-top: 1px solid var(--line);

            text-align: center;
        }

        .footer-note {
            color: var(--muted);

            font-size: 11px;
        }

        .footer-brand {
            margin-top: 7px;

            color: var(--accent);

            font-size: 11px;
            font-weight: 800;
        }


        /* =====================================================
           PRINT
        ====================================================== */

        @media print {

            body {
                background: #fff;
            }

            .page {
                max-width: none;

                margin: 0;
                padding: 0;
            }

            .receipt {
                border: 0;
                border-radius: 0;

                box-shadow: none;
            }

            .amount-hero,
            .status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 700px) {

            .page {
                margin: 15px auto;
                padding: 0 10px;
            }

            .header,
            .body,
            .footer {
                padding-left: 22px;
                padding-right: 22px;
            }

            .header-top,
            .amount-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .receipt-title {
                text-align: left;
            }

            .amount-hero {
                margin-left: 22px;
                margin-right: 22px;
            }

            .amount-date {
                text-align: left;
            }

            .meta {
                grid-template-columns: 1fr;
            }

            .meta-item {
                padding-left: 22px;
                padding-right: 22px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .summary {
                justify-content: stretch;
            }

            .summary-box {
                width: 100%;
            }

            .table-wrap {
                overflow-x: auto;
            }

            table {
                min-width: 620px;
            }
        }

    </style>

</head>


<body>

<div class="page">

    <article class="receipt">


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <header class="header">

            <div class="header-top">

                <div class="brand">

                    <div class="brand-mark">
                        KR
                    </div>

                    <div>

                        <p class="brand-name">
                            {{ $property['name'] ?: 'KopsRent' }}
                        </p>

                        <div class="property-address">

                            @if ($property['address'])

                                {{ $property['address'] }}

                            @endif

                            @if ($property['city'])

                                @if ($property['address'])
                                    ·
                                @endif

                                {{ $property['city'] }}

                            @endif

                        </div>

                    </div>

                </div>


                <div class="receipt-title">

                    <h1>
                        {{ __('receipt.receipt') }}
                    </h1>

                    <div class="receipt-number">

                        {{ __('receipt.receipt_no') }}

                        <strong>
                            {{ $receipt['number'] }}
                        </strong>

                    </div>

                    <div class="status">

                        <span class="status-dot"></span>

                        {{ __('receipt.paid') }}

                    </div>

                </div>

            </div>

        </header>


        {{-- =====================================================
             AMOUNT HERO
        ====================================================== --}}

        <section class="amount-hero">

            <div>

                <div class="amount-label">
                    {{ __('receipt.amount_paid') }}
                </div>

                <div class="amount">
                    {{ $payment['formatted_amount'] }}
                </div>

            </div>


            <div class="amount-date">

                <strong>
                    {{ $payment['payment_date_formatted'] }}
                </strong>

                <br>

                {{ __('receipt.issued') }}:
                {{ $receipt['issued_at_formatted'] }}

            </div>

        </section>


        {{-- =====================================================
             META
        ====================================================== --}}

        <section class="meta">

            <div class="meta-item">

                <div class="meta-label">
                    {{ __('receipt.method') }}
                </div>

                <div class="meta-value">
                    {{ $payment['payment_method_label'] }}
                </div>

            </div>


            <div class="meta-item">

                <div class="meta-label">
                    {{ __('receipt.reference') }}
                </div>

                <div class="meta-value">
                    {{ $display['payment_reference'] ?: '—' }}
                </div>

            </div>


            <div class="meta-item">

                <div class="meta-label">
                    {{ __('receipt.invoice') }}
                </div>

                <div class="meta-value">
                    {{ $invoice['invoice_number'] }}
                </div>

            </div>

        </section>


        {{-- =====================================================
             MAIN BODY
        ====================================================== --}}

        <main class="body">


            {{-- =================================================
                 TENANT / PROPERTY
            ================================================== --}}

            <section class="section">

                <div class="section-heading">

                    {{ __('receipt.tenant') }}

                    &amp;

                    {{ __('receipt.property') }}

                </div>


                <div class="info-grid">


                    {{-- Tenant --}}

                    <div class="info-card">

                        <h3>
                            {{ __('receipt.tenant') }}
                        </h3>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.name') }}
                            </span>

                            <span class="value">
                                {{ $tenant['full_name'] ?: '—' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.phone') }}
                            </span>

                            <span class="value">
                                {{ $tenant['phone'] ?: '—' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.email') }}
                            </span>

                            <span class="value">
                                {{ $tenant['email'] ?: '—' }}
                            </span>

                        </div>


                        @if ($tenant['national_id'])

                            <div class="info-row">

                                <span class="label">
                                    {{ __('receipt.id_passport') }}
                                </span>

                                <span class="value">
                                    {{ $tenant['national_id'] }}
                                </span>

                            </div>

                        @endif

                    </div>


                    {{-- Unit --}}

                    <div class="info-card">

                        <h3>
                            {{ __('receipt.unit') }}
                        </h3>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.property') }}
                            </span>

                            <span class="value">
                                {{ $property['name'] ?: '—' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.unit') }}
                            </span>

                            <span class="value">
                                {{ $unit['unit_number'] ?: '—' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.billing_period') }}
                            </span>

                            <span class="value">
                                {{ $invoice['billing_period_formatted'] ?: '—' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="label">
                                {{ __('receipt.invoice') }}
                            </span>

                            <span class="value">
                                {{ $invoice['invoice_number'] }}
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 PAYMENT BREAKDOWN
            ================================================== --}}

            <section class="section">

                <div class="section-heading">
                    {{ __('receipt.breakdown') }}
                </div>


                <div class="table-wrap">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    {{ __('receipt.particulars') }}
                                </th>

                                <th>
                                    {{ __('receipt.invoice_amount') }}
                                </th>

                                <th class="amount-cell">
                                    {{ __('receipt.received') }}
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($allocations as $allocation)

                                <tr>

                                    <td>

                                        <span class="description">
                                            {{ $allocation['description'] }}
                                        </span>


                                        @if ($allocation['invoice_number'])

                                            <span class="subtext">

                                                {{ $allocation['invoice_number'] }}

                                                @if ($allocation['billing_period_formatted'])

                                                    ·

                                                    {{ $allocation['billing_period_formatted'] }}

                                                @endif

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $display['currency_symbol'] }}

                                        {{ number_format(
                                            $allocation['invoice_amount'],
                                            2
                                        ) }}

                                    </td>


                                    <td class="amount-cell">

                                        <strong>

                                            {{ $display['currency_symbol'] }}

                                            {{ number_format(
                                                $allocation['amount_paid'],
                                                2
                                            ) }}

                                        </strong>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="3">

                                        {{ __('receipt.amount_paid') }}

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>


            {{-- =================================================
                 SUMMARY
            ================================================== --}}

            <section class="section">

                <div class="summary">

                    <div class="summary-box">


                        <div class="summary-row">

                            <span class="label">
                                {{ __('receipt.amount_paid') }}
                            </span>

                            <strong>
                                {{ $payment['formatted_amount'] }}
                            </strong>

                        </div>


                        @if ($summary['credit_amount'] > 0)

                            <div class="summary-row">

                                <span class="label">
                                    {{ __('receipt.credit_created') }}
                                </span>

                                <strong>
                                    {{ $summary['formatted_credit_amount'] }}
                                </strong>

                            </div>

                        @endif


                        @if ($summary['unallocated_amount'] > 0)

                            <div class="summary-row">

                                <span class="label">
                                    {{ __('receipt.unallocated') }}
                                </span>

                                <strong>
                                    {{ $summary['formatted_unallocated_amount'] }}
                                </strong>

                            </div>

                        @endif


                        <div class="summary-row balance">

                            <span class="label">
                                {{ __('receipt.balance') }}
                            </span>

                            <strong class="value">
                                {{ $balance['formatted_remaining_amount'] }}
                            </strong>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 AMOUNT IN WORDS
            ================================================== --}}

            <section class="section">

                <div class="words">

                    <div class="words-label">
                        {{ __('receipt.amount_words') }}
                    </div>

                    <div class="words-value">
                        {{ $display['amount_in_words'] }}
                    </div>

                </div>

            </section>


        </main>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <footer class="footer">

            <div class="footer-note">
                {{ __('receipt.system_generated') }}
            </div>

            <div class="footer-brand">
                {{ __('receipt.powered') }}
            </div>

        </footer>


    </article>

</div>

</body>

</html>