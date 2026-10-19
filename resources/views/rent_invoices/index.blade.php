@extends('layouts.app')

@section('title', 'Rent Invoices')

@section('page-heading', 'Rent Invoices')

@section('content')

@php
$supportedCurrencies = \App\Support\Currency::SUPPORTED;

$currencyTotals = [];

foreach ($supportedCurrencies as $currency) {
    $currencyTotals[$currency] = [
        'paid' => 0,
        'unpaid' => 0,
        'overdue' => 0,
        'cancelled' => 0,
        'total' => 0,
    ];
}

foreach ($invoices as $invoice) {
    $invoiceCurrency = strtoupper((string) $invoice->currency);

    /*
     * Never silently assign a fallback currency.
     * Invalid or missing currencies are excluded from
     * the currency summary until corrected.
     */
    if (!in_array($invoiceCurrency, $supportedCurrencies, true)) {
        continue;
    }

    $amount = (float) $invoice->amount;
    $paid = (float) ($invoice->total_paid ?? 0);

    $outstanding = (float) (
        $invoice->outstanding_amount
        ?? max(0, $amount - $paid)
    );

    $currencyTotals[$invoiceCurrency]['total'] += $amount;

    if (
        $invoice->status ===
        \App\Models\RentInvoice::STATUS_PAID
    ) {
        $currencyTotals[$invoiceCurrency]['paid'] += $amount;
    } elseif (
        $invoice->status ===
        \App\Models\RentInvoice::STATUS_CANCELLED
    ) {
        $currencyTotals[$invoiceCurrency]['cancelled'] += $amount;
    } elseif (
        $invoice->due_date &&
        \Carbon\Carbon::parse($invoice->due_date)->isPast() &&
        $outstanding > 0
    ) {
        $currencyTotals[$invoiceCurrency]['overdue'] += $outstanding;
    } else {
        $currencyTotals[$invoiceCurrency]['unpaid'] += $outstanding;
    }
}

$totalInvoices = $invoices->count();

@endphp

<div class="container-fluid invoice-page px-0">

{{-- =========================================================
     PAGE HEADER
========================================================== --}}

<div class="invoice-header mb-4">

    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-3">

        <div>
            <div class="invoice-eyebrow">
                Finance / Rent Billing
            </div>

            <h1 class="invoice-title">
                Rent Invoices Directory
            </h1>

            <p class="invoice-subtitle">
                View, track, and manage tenant rent invoices across all supported currencies.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a
                href="{{ route('rent-invoices.create') }}"
                class="btn btn-primary invoice-primary-btn"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Create Invoice
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
     FLASH MESSAGES
========================================================== --}}

@if(session('success'))

    <div
        class="alert alert-success alert-dismissible fade show invoice-alert"
        role="alert"
    >
        <div class="d-flex align-items-start gap-2">

            <i class="bi bi-check-circle-fill fs-5"></i>

            <div>
                <div class="fw-semibold">Success</div>
                <div>{{ session('success') }}</div>
            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

@endif


@if(session('error'))

    <div
        class="alert alert-danger alert-dismissible fade show invoice-alert"
        role="alert"
    >
        <div class="d-flex align-items-start gap-2">

            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

            <div>
                <div class="fw-semibold">Unable to complete action</div>
                <div>{{ session('error') }}</div>
            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

@endif


@if($errors->any())

    <div
        class="alert alert-danger alert-dismissible fade show invoice-alert"
        role="alert"
    >
        <div class="d-flex align-items-start gap-2">

            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

            <div>
                <div class="fw-semibold mb-1">
                    Please correct the following errors:
                </div>

                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

@endif


{{-- =========================================================
     CURRENCY SUMMARY
========================================================== --}}

<div class="section-heading mb-3">

    <div>
        <h2 class="section-title">
            Financial Overview
        </h2>

        <p class="section-subtitle">
            Invoice balances are kept separate by currency.
        </p>
    </div>

</div>


<div class="row g-3 mb-4">

    @foreach($supportedCurrencies as $currency)

        @php
            $summary = $currencyTotals[$currency];

            $currencyAccent = match ($currency) {
                'USD' => 'usd',
                'KES' => 'kes',
                'CDF' => 'cdf',
                default => 'default',
            };
        @endphp

        <div class="col-12 col-md-6 col-xl-4">

            <div class="currency-card currency-card-{{ $currencyAccent }} h-100">

                <div class="currency-card-top">

                    <div class="currency-icon">
                        {{ \App\Support\Currency::symbol($currency) }}
                    </div>

                    <div class="flex-grow-1">

                        <div class="currency-code">
                            {{ $currency }}
                        </div>

                        <div class="currency-name">
                            {{ \App\Support\Currency::name($currency) }}
                        </div>

                    </div>

                    <span class="currency-count">
                        {{ $totalInvoices }}
                        {{ $totalInvoices === 1 ? 'invoice' : 'invoices' }}
                    </span>

                </div>


                <div class="currency-total-label">
                    Total Invoiced
                </div>

                <div class="currency-total">
                    {{ \App\Support\Currency::format(
                        $summary['total'],
                        $currency
                    ) }}
                </div>


                <div class="currency-metrics">

                    <div class="currency-metric">

                        <div class="metric-label">
                            <span class="metric-dot metric-dot-success"></span>
                            Paid
                        </div>

                        <div class="metric-value">
                            {{ \App\Support\Currency::format(
                                $summary['paid'],
                                $currency
                            ) }}
                        </div>

                    </div>


                    <div class="currency-metric">

                        <div class="metric-label">
                            <span class="metric-dot metric-dot-warning"></span>
                            Outstanding
                        </div>

                        <div class="metric-value">
                            {{ \App\Support\Currency::format(
                                $summary['unpaid'],
                                $currency
                            ) }}
                        </div>

                    </div>


                    <div class="currency-metric">

                        <div class="metric-label">
                            <span class="metric-dot metric-dot-danger"></span>
                            Overdue
                        </div>

                        <div class="metric-value">
                            {{ \App\Support\Currency::format(
                                $summary['overdue'],
                                $currency
                            ) }}
                        </div>

                    </div>


                    <div class="currency-metric">

                        <div class="metric-label">
                            <span class="metric-dot metric-dot-secondary"></span>
                            Cancelled
                        </div>

                        <div class="metric-value">
                            {{ \App\Support\Currency::format(
                                $summary['cancelled'],
                                $currency
                            ) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endforeach

</div>


{{-- =========================================================
     INVOICE DIRECTORY
========================================================== --}}

<div class="invoice-directory-card">

    <div class="directory-header">

        <div>

            <div class="directory-title-row">

                <div class="directory-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div>
                    <h2 class="directory-title">
                        Rent Invoices
                    </h2>

                    <p class="directory-subtitle">
                        {{ $totalInvoices }}
                        {{ $totalInvoices === 1 ? 'invoice' : 'invoices' }}
                        in the directory
                    </p>
                </div>

            </div>

        </div>


        <div class="directory-tools">

            <div class="invoice-search">

                <i class="bi bi-search"></i>

                <input
                    type="search"
                    id="invoiceSearch"
                    class="form-control"
                    placeholder="Search invoices..."
                    autocomplete="off"
                >

            </div>


            <select
                id="invoiceStatusFilter"
                class="form-select invoice-filter"
                aria-label="Filter invoices by status"
            >
                <option value="all">All statuses</option>
                <option value="paid">Paid</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="unpaid">Unpaid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>

        </div>

    </div>


    <div class="table-responsive">

        <table
            class="table invoice-table align-middle mb-0"
            id="invoiceTable"
        >

            <thead>

                <tr>

                    <th class="ps-4 invoice-ref-column">
                        #
                    </th>

                    <th>
                        Invoice
                    </th>

                    <th>
                        Tenant
                    </th>

                    <th>
                        Property / Unit
                    </th>

                    <th>
                        Billing Period
                    </th>

                    <th>
                        Amount
                    </th>

                    <th>
                        Due Date
                    </th>

                    <th>
                        Status
                    </th>

                    <th class="text-end pe-4">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($invoices as $invoice)

                    @php
                        $invoiceCurrency = strtoupper(
                            (string) $invoice->currency
                        );

                        $currencyValid = in_array(
                            $invoiceCurrency,
                            $supportedCurrencies,
                            true
                        );

                        $amount = (float) $invoice->amount;

                        $paid = (float) (
                            $invoice->total_paid ?? 0
                        );

                        $outstanding = (float) (
                            $invoice->outstanding_amount
                            ?? max(0, $amount - $paid)
                        );

                        $status = (string) $invoice->status;

                        $tenantName = $invoice->lease?->tenant?->full_name
                            ?? $invoice->lease?->tenant?->name
                            ?? 'Tenant';

                        $unitName = $invoice->lease?->unit?->name
                            ?? $invoice->lease?->unit?->unit_number
                            ?? 'Unit';

                        $propertyName = $invoice->lease?->unit?->property?->name
                            ?? 'Property';

                        $searchText = strtolower(
                            implode(' ', [
                                $invoice->invoice_number,
                                $tenantName,
                                $propertyName,
                                $unitName,
                                $invoiceCurrency,
                                $status,
                            ])
                        );
                    @endphp


                    <tr
                        data-status="{{ $status }}"
                        data-search="{{ $searchText }}"
                    >

                        {{-- Reference --}}

                        <td class="ps-4">

                            <div class="invoice-row-number">
                                {{ $loop->iteration }}
                            </div>

                        </td>


                        {{-- Invoice --}}

                        <td>

                            <div class="invoice-number">
                                {{ $invoice->invoice_number }}
                            </div>

                            <div class="invoice-db-reference">
                                Invoice #{{ $invoice->id }}
                            </div>

                        </td>


                        {{-- Tenant --}}

                        <td>

                            @if($invoice->lease?->tenant)

                                <a
                                    href="{{ route(
                                        'tenants.show',
                                        $invoice->lease->tenant
                                    ) }}"
                                    class="table-person-link"
                                >
                                    <span class="person-avatar">
                                        {{
                                            collect(
                                                preg_split(
                                                    '/\s+/',
                                                    trim($tenantName)
                                                )
                                            )
                                            ->filter()
                                            ->map(
                                                fn ($part) =>
                                                    strtoupper(
                                                        substr($part, 0, 1)
                                                    )
                                            )
                                            ->take(2)
                                            ->implode('')
                                        }}
                                    </span>

                                    <span>
                                        {{ $tenantName }}
                                    </span>
                                </a>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Property / Unit --}}

                        <td>

                            @if($invoice->lease?->unit)

                                @if($invoice->lease->unit->property)

                                    <a
                                        href="{{ route(
                                            'properties.show',
                                            $invoice->lease->unit->property
                                        ) }}"
                                        class="property-link"
                                    >
                                        {{ $propertyName }}
                                    </a>

                                @else

                                    <div class="property-link">
                                        {{ $propertyName }}
                                    </div>

                                @endif

                                <div class="unit-reference">
                                    <i class="bi bi-door-open me-1"></i>
                                    {{ $unitName }}
                                </div>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Billing Period --}}

                        <td>

                            @if($invoice->billing_period)

                                @php
                                    $billingPeriod = \Carbon\Carbon::parse(
                                        $invoice->billing_period
                                    );
                                @endphp

                                <div class="date-primary">
                                    {{ $billingPeriod->format('F Y') }}
                                </div>

                                <div class="date-secondary">
                                    Billing period
                                </div>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Amount --}}

                        <td>

                            @if($currencyValid)

                                <div class="amount-primary">
                                    {{ \App\Support\Currency::format(
                                        $amount,
                                        $invoiceCurrency
                                    ) }}
                                </div>

                                @if($outstanding > 0)

                                    <div class="amount-outstanding">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Outstanding:
                                        {{ \App\Support\Currency::format(
                                            $outstanding,
                                            $invoiceCurrency
                                        ) }}
                                    </div>

                                @else

                                    <div class="amount-paid">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Fully settled
                                    </div>

                                @endif

                            @else

                                <div class="amount-primary">
                                    {{ number_format($amount, 2) }}
                                </div>

                                <div class="amount-outstanding text-danger">
                                    Currency not set
                                </div>

                            @endif

                        </td>


                        {{-- Due Date --}}

                        <td>

                            @if($invoice->due_date)

                                @php
                                    $dueDate = \Carbon\Carbon::parse(
                                        $invoice->due_date
                                    );

                                    $isOverdue = (
                                        $outstanding > 0 &&
                                        $dueDate->isPast()
                                    );
                                @endphp

                                <div class="date-primary">
                                    {{ $dueDate->format('d M Y') }}
                                </div>

                                @if($isOverdue)

                                    <div class="due-overdue">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Overdue
                                    </div>

                                @else

                                    <div class="date-secondary">
                                        Payment due
                                    </div>

                                @endif

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Status --}}

                        <td>

                            @switch($invoice->status)

                                @case(\App\Models\RentInvoice::STATUS_PAID)

                                    <span class="status-badge status-paid">
                                        <span class="status-dot"></span>
                                        Paid
                                    </span>

                                    @break

                                @case(\App\Models\RentInvoice::STATUS_PARTIALLY_PAID)

                                    <span class="status-badge status-partial">
                                        <span class="status-dot"></span>
                                        Partially Paid
                                    </span>

                                    @break

                                @case(\App\Models\RentInvoice::STATUS_UNPAID)

                                    <span class="status-badge status-unpaid">
                                        <span class="status-dot"></span>
                                        Unpaid
                                    </span>

                                    @break

                                @case(\App\Models\RentInvoice::STATUS_OVERDUE)

                                    <span class="status-badge status-overdue">
                                        <span class="status-dot"></span>
                                        Overdue
                                    </span>

                                    @break

                                @case(\App\Models\RentInvoice::STATUS_CANCELLED)

                                    <span class="status-badge status-cancelled">
                                        <span class="status-dot"></span>
                                        Cancelled
                                    </span>

                                    @break

                                @default

                                    <span class="status-badge status-unpaid">
                                        <span class="status-dot"></span>
                                        {{ ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $invoice->status
                                            )
                                        ) }}
                                    </span>

                            @endswitch

                        </td>


                        {{-- Actions --}}

                        <td class="text-end pe-4">

                            <div class="invoice-actions">

                                @if(Route::has('rent-invoices.show'))

                                    <a
                                        href="{{ route(
                                            'rent-invoices.show',
                                            $invoice
                                        ) }}"
                                        class="invoice-action-btn"
                                        title="View Invoice"
                                        aria-label="View Invoice"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                @endif


                                @if(Route::has('rent-invoices.edit'))

                                    <a
                                        href="{{ route(
                                            'rent-invoices.edit',
                                            $invoice
                                        ) }}"
                                        class="invoice-action-btn"
                                        title="Edit Invoice"
                                        aria-label="Edit Invoice"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endif


                                @if(Route::has('rent-invoices.destroy'))

                                    <form
                                        action="{{ route(
                                            'rent-invoices.destroy',
                                            $invoice
                                        ) }}"
                                        method="POST"
                                        class="d-inline invoice-delete-form"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="invoice-action-btn invoice-delete-btn"
                                            title="Delete Invoice"
                                            aria-label="Delete Invoice"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="invoice-empty-cell"
                        >

                            <div class="empty-state">

                                <div class="empty-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>

                                <h3>
                                    No invoices found
                                </h3>

                                <p>
                                    There are no rent invoices in the directory yet.
                                </p>

                                <a
                                    href="{{ route('rent-invoices.create') }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Create First Invoice
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse


                <tr
                    id="invoiceNoResults"
                    class="d-none"
                >
                    <td
                        colspan="9"
                        class="invoice-empty-cell"
                    >
                        <div class="empty-state compact">

                            <div class="empty-icon">
                                <i class="bi bi-search"></i>
                            </div>

                            <h3>
                                No matching invoices
                            </h3>

                            <p class="mb-0">
                                Try a different search term or status filter.
                            </p>

                        </div>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>


    {{-- Directory Footer --}}

    @if($totalInvoices > 0)

        <div class="directory-footer">

            <div class="text-muted small">

                <i class="bi bi-info-circle me-1"></i>

                Showing
                <strong id="visibleInvoiceCount">
                    {{ $totalInvoices }}
                </strong>
                of
                <strong>{{ $totalInvoices }}</strong>
                invoices

            </div>

            <div class="text-muted small">
                Currency totals are calculated separately.
            </div>

        </div>

    @endif

</div>

</div>

@endsection

@push('styles')

<style>

    .invoice-page {
        max-width: 100%;
    }

    .invoice-header {
        padding-top: .25rem;
    }

    .invoice-eyebrow {
        color: #2563eb;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: .35rem;
    }

    .invoice-title {
        margin: 0;
        font-size: clamp(1.55rem, 2.5vw, 2rem);
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .invoice-subtitle {
        margin: .4rem 0 0;
        color: #64748b;
        font-size: .92rem;
    }

    .invoice-primary-btn {
        min-height: 42px;
        padding-inline: 1rem;
        font-weight: 700;
        border-radius: .65rem;
    }

    .invoice-alert {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .05);
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .section-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .section-subtitle {
        margin: .2rem 0 0;
        color: #64748b;
        font-size: .82rem;
    }

    /* Currency Cards */

    .currency-card {
        position: relative;
        overflow: hidden;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 6px 22px rgba(15, 23, 42, .05);
    }

    .currency-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 3px;
        background: #2563eb;
    }

    .currency-card-usd::before {
        background: #2563eb;
    }

    .currency-card-kes::before {
        background: #16a34a;
    }

    .currency-card-cdf::before {
        background: #9333ea;
    }

    .currency-card-top {
        display: flex;
        align-items: center;
        gap: .8rem;
        margin-bottom: 1.2rem;
    }

    .currency-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border-radius: .75rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: .85rem;
        font-weight: 800;
    }

    .currency-card-kes .currency-icon {
        background: #f0fdf4;
        color: #16a34a;
    }

    .currency-card-cdf .currency-icon {
        background: #faf5ff;
        color: #9333ea;
    }

    .currency-code {
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .04em;
    }

    .currency-name {
        color: #64748b;
        font-size: .75rem;
        margin-top: .1rem;
    }

    .currency-count {
        padding: .28rem .55rem;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: .68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .currency-total-label {
        color: #64748b;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .currency-total {
        margin: .2rem 0 1.15rem;
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .currency-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .55rem;
    }

    .currency-metric {
        padding: .65rem .7rem;
        border-radius: .7rem;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
    }

    .metric-label {
        display: flex;
        align-items: center;
        gap: .35rem;
        color: #64748b;
        font-size: .68rem;
        font-weight: 700;
        margin-bottom: .2rem;
    }

    .metric-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex: 0 0 6px;
    }

    .metric-dot-success {
        background: #16a34a;
    }

    .metric-dot-warning {
        background: #d97706;
    }

    .metric-dot-danger {
        background: #dc2626;
    }

    .metric-dot-secondary {
        background: #64748b;
    }

    .metric-value {
        color: #334155;
        font-size: .76rem;
        font-weight: 700;
        line-height: 1.35;
    }

    /* Directory */

    .invoice-directory-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 7px 26px rgba(15, 23, 42, .06);
    }

    .directory-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .directory-title-row {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .directory-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: .7rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1rem;
    }

    .directory-title {
        margin: 0;
        color: #0f172a;
        font-size: .98rem;
        font-weight: 800;
    }

    .directory-subtitle {
        margin: .15rem 0 0;
        color: #64748b;
        font-size: .75rem;
    }

    .directory-tools {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .invoice-search {
        position: relative;
        width: min(280px, 42vw);
    }

    .invoice-search > i {
        position: absolute;
        z-index: 2;
        left: .8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .invoice-search .form-control {
        min-height: 38px;
        padding-left: 2.25rem;
        border-color: #e2e8f0;
        border-radius: .6rem;
        font-size: .8rem;
    }

    .invoice-filter {
        min-width: 150px;
        min-height: 38px;
        border-color: #e2e8f0;
        border-radius: .6rem;
        font-size: .8rem;
        font-weight: 600;
    }

    .invoice-table {
        min-width: 1180px;
    }

    .invoice-table thead th {
        padding-top: .85rem;
        padding-bottom: .85rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .invoice-table tbody td {
        padding-top: .95rem;
        padding-bottom: .95rem;
        border-color: #f1f5f9;
    }

    .invoice-table tbody tr {
        transition: background .15s ease;
    }

    .invoice-table tbody tr:hover {
        background: #f8fafc;
    }

    .invoice-row-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 28px;
        padding-inline: .45rem;
        border-radius: .5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: .7rem;
        font-weight: 800;
    }

    .invoice-number {
        color: #0f172a;
        font-size: .82rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-db-reference {
        margin-top: .15rem;
        color: #94a3b8;
        font-size: .65rem;
    }

    .table-person-link {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        color: #0f172a;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .table-person-link:hover {
        color: #2563eb;
    }

    .person-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: .65rem;
        font-weight: 800;
    }

    .property-link {
        color: #334155;
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
    }

    a.property-link:hover {
        color: #2563eb;
    }

    .unit-reference {
        margin-top: .18rem;
        color: #94a3b8;
        font-size: .68rem;
    }

    .date-primary {
        color: #334155;
        font-size: .77rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .date-secondary {
        margin-top: .15rem;
        color: #94a3b8;
        font-size: .65rem;
    }

    .amount-primary {
        color: #0f172a;
        font-size: .78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .amount-outstanding,
    .amount-paid {
        margin-top: .18rem;
        font-size: .65rem;
        white-space: nowrap;
    }

    .amount-outstanding {
        color: #d97706;
    }

    .amount-paid {
        color: #16a34a;
    }

    .due-overdue {
        margin-top: .18rem;
        color: #dc2626;
        font-size: .65rem;
        font-weight: 700;
    }

    /* Status */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .38rem .62rem;
        border-radius: 999px;
        font-size: .67rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-paid {
        background: #dcfce7;
        color: #166534;
    }

    .status-paid .status-dot {
        background: #16a34a;
    }

    .status-partial {
        background: #fef3c7;
        color: #92400e;
    }

    .status-partial .status-dot {
        background: #d97706;
    }

    .status-unpaid {
        background: #fef3c7;
        color: #92400e;
    }

    .status-unpaid .status-dot {
        background: #d97706;
    }

    .status-overdue {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-overdue .status-dot {
        background: #dc2626;
    }

    .status-cancelled {
        background: #f1f5f9;
        color: #475569;
    }

    .status-cancelled .status-dot {
        background: #64748b;
    }

    /* Actions */

    .invoice-actions {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .invoice-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        padding: 0;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        background: #fff;
        color: #64748b;
        font-size: .78rem;
        text-decoration: none;
        transition: all .15s ease;
    }

    .invoice-action-btn:hover {
        border-color: #2563eb;
        background: #eff6ff;
        color: #2563eb;
    }

    .invoice-delete-btn:hover {
        border-color: #dc2626;
        background: #fef2f2;
        color: #dc2626;
    }

    .directory-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: .85rem 1.25rem;
        border-top: 1px solid #e2e8f0;
        background: #fafafa;
    }

    /* Empty */

    .invoice-empty-cell {
        padding: 0 !important;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 320px;
        padding: 2rem;
        text-align: center;
    }

    .empty-state.compact {
        min-height: 230px;
    }

    .empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        margin-bottom: 1rem;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 1.6rem;
    }

    .empty-state h3 {
        margin-bottom: .35rem;
        color: #334155;
        font-size: .95rem;
        font-weight: 800;
    }

    .empty-state p {
        margin-bottom: 1rem;
        color: #64748b;
        font-size: .8rem;
    }

    /* Dark Mode */

    html[data-theme="dark"] .invoice-title,
    html[data-theme="dark"] .section-title,
    html[data-theme="dark"] .directory-title,
    html[data-theme="dark"] .invoice-number,
    html[data-theme="dark"] .currency-total,
    html[data-theme="dark"] .amount-primary,
    html[data-theme="dark"] .table-person-link {
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-subtitle,
    html[data-theme="dark"] .section-subtitle,
    html[data-theme="dark"] .directory-subtitle,
    html[data-theme="dark"] .currency-name,
    html[data-theme="dark"] .date-secondary,
    html[data-theme="dark"] .invoice-db-reference {
        color: #94a3b8;
    }

    html[data-theme="dark"] .currency-card,
    html[data-theme="dark"] .invoice-directory-card {
        background: #111827;
        border-color: #1f2937;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .2);
    }

    html[data-theme="dark"] .currency-icon {
        background: #172554;
        color: #93c5fd;
    }

    html[data-theme="dark"] .currency-card-kes .currency-icon {
        background: #052e16;
        color: #86efac;
    }

    html[data-theme="dark"] .currency-card-cdf .currency-icon {
        background: #2e1065;
        color: #d8b4fe;
    }

    html[data-theme="dark"] .currency-count,
    html[data-theme="dark"] .currency-metric,
    html[data-theme="dark"] .invoice-row-number {
        background: #1f2937;
        border-color: #374151;
    }

    html[data-theme="dark"] .metric-value {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .directory-header,
    html[data-theme="dark"] .directory-footer {
        border-color: #1f2937;
        background: #111827;
    }

    html[data-theme="dark"] .directory-icon {
        background: #172554;
        color: #93c5fd;
    }

    html[data-theme="dark"] .invoice-search .form-control,
    html[data-theme="dark"] .invoice-filter {
        background: #0f172a;
        border-color: #374151;
        color: #e5e7eb;
    }

    html[data-theme="dark"] .invoice-table thead th {
        background: #0f172a;
        border-color: #1f2937;
        color: #94a3b8;
    }

    html[data-theme="dark"] .invoice-table tbody td {
        border-color: #1f2937;
    }

    html[data-theme="dark"] .invoice-table tbody tr:hover {
        background: #172033;
    }

    html[data-theme="dark"] .property-link {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .invoice-action-btn {
        background: #111827;
        border-color: #374151;
        color: #94a3b8;
    }

    html[data-theme="dark"] .invoice-action-btn:hover {
        background: #172554;
        border-color: #3b82f6;
        color: #93c5fd;
    }

    html[data-theme="dark"] .invoice-delete-btn:hover {
        background: #450a0a;
        border-color: #ef4444;
        color: #fca5a5;
    }

    html[data-theme="dark"] .empty-icon {
        background: #1f2937;
        color: #64748b;
    }

    html[data-theme="dark"] .empty-state h3 {
        color: #e2e8f0;
    }

    /* Responsive */

    @media (max-width: 991.98px) {

        .directory-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .directory-tools {
            width: 100%;
        }

        .invoice-search {
            flex: 1;
            width: auto;
        }

        .invoice-filter {
            min-width: 160px;
        }

    }

    @media (max-width: 575.98px) {

        .invoice-header {
            padding-top: 0;
        }

        .invoice-primary-btn {
            width: 100%;
        }

        .currency-metrics {
            grid-template-columns: 1fr;
        }

        .directory-tools {
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-search {
            width: 100%;
        }

        .invoice-filter {
            width: 100%;
        }

        .directory-footer {
            align-items: flex-start;
            flex-direction: column;
        }

    }

</style>

@endpush

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('invoiceSearch');
    const statusFilter = document.getElementById('invoiceStatusFilter');
    const table = document.getElementById('invoiceTable');
    const visibleCount = document.getElementById('visibleInvoiceCount');
    const noResultsRow = document.getElementById('invoiceNoResults');

    if (!table) {
        return;
    }

    const invoiceRows = Array.from(
        table.querySelectorAll('tbody tr[data-status]')
    );

    function applyInvoiceFilters() {

        const searchTerm = (
            searchInput?.value || ''
        ).toLowerCase().trim();

        const selectedStatus = (
            statusFilter?.value || 'all'
        );

        let visible = 0;

        invoiceRows.forEach(function (row) {

            const rowStatus = (
                row.dataset.status || ''
            ).toLowerCase();

            const rowSearch = (
                row.dataset.search || ''
            ).toLowerCase();

            const statusMatches =
                selectedStatus === 'all' ||
                rowStatus === selectedStatus;

            const searchMatches =
                !searchTerm ||
                rowSearch.includes(searchTerm);

            const shouldShow =
                statusMatches &&
                searchMatches;

            row.classList.toggle(
                'd-none',
                !shouldShow
            );

            if (shouldShow) {
                visible++;
            }

        });

        if (visibleCount) {
            visibleCount.textContent = visible;
        }

        if (noResultsRow) {
            noResultsRow.classList.toggle(
                'd-none',
                visible !== 0 || invoiceRows.length === 0
            );
        }

    }

    searchInput?.addEventListener(
        'input',
        applyInvoiceFilters
    );

    statusFilter?.addEventListener(
        'change',
        applyInvoiceFilters
    );


    document
        .querySelectorAll('.invoice-delete-form')
        .forEach(function (form) {

            form.addEventListener('submit', function (event) {

                const confirmed = window.confirm(
                    'Are you sure you want to delete this invoice? This action cannot be undone.'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {

                    button.disabled = true;

                    button.innerHTML =
                        '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';

                }

            });

        });


    applyInvoiceFilters();

});

</script>

@endpush
