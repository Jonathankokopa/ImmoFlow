@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

@php
    $supportedCurrencies = \App\Support\Currency::SUPPORTED;

    $formatMoney = function ($amount, $currency) {
        if (
            !$currency ||
            !in_array($currency, \App\Support\Currency::SUPPORTED, true)
        ) {
            return number_format((float) $amount, 2);
        }

        return \App\Support\Currency::format(
            (float) $amount,
            $currency
        );
    };

    $selectedCurrencyLabel = $selectedCurrency
        ? \App\Support\Currency::name($selectedCurrency)
        : 'All currencies';

    $currentFinancial = $selectedCurrency !== ''
        ? ($financialByCurrency[$selectedCurrency] ?? null)
        : null;
@endphp

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                    <i class="bi bi-bar-chart-line me-1"></i>
                    Analytics
                </span>

                <span class="text-muted small">
                    {{ $periodLabel }}
                </span>
            </div>

            <h1 class="h3 fw-bold mb-1">
                Reports & Analytics
            </h1>

            <p class="text-muted mb-0">
                Monitor revenue, collections, expenses, occupancy and lease activity.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a
                href="{{ route('reports.export', [
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                    'currency' => $selectedCurrency,
                ]) }}"
                class="btn btn-success"
            >
                <i class="bi bi-file-earmark-excel me-1"></i>
                Export Excel
            </a>

            <button
                type="button"
                class="btn btn-outline-secondary"
                onclick="window.print()"
            >
                <i class="bi bi-printer me-1"></i>
                Print
            </button>
        </div>
    </div>

    {{-- PERIOD FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('reports.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">
                            From
                        </label>

                        <input
                            type="date"
                            name="from"
                            class="form-control"
                            value="{{ $from->format('Y-m-d') }}"
                        >
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">
                            To
                        </label>

                        <input
                            type="date"
                            name="to"
                            class="form-control"
                            value="{{ $to->format('Y-m-d') }}"
                        >
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">
                            Currency
                        </label>

                        <select
                            name="currency"
                            class="form-select"
                        >
                            <option value="">
                                All currencies
                            </option>

                            @foreach($supportedCurrencies as $currency)
                                <option
                                    value="{{ $currency }}"
                                    @selected($selectedCurrency === $currency)
                                >
                                    {{ \App\Support\Currency::name($currency) }}
                                    ({{ \App\Support\Currency::symbol($currency) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>
                            Apply Report
                        </button>
                    </div>

                </div>
            </form>

            @php
                $quickPeriods = [
                    'month' => [
                        'label' => 'Current Month',
                        'from' => now()->startOfMonth()->format('Y-m-d'),
                        'to' => now()->endOfMonth()->format('Y-m-d'),
                    ],
                    'previous' => [
                        'label' => 'Previous Month',
                        'from' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
                        'to' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
                    ],
                    'quarter' => [
                        'label' => 'Last 3 Months',
                        'from' => now()->subMonths(2)->startOfMonth()->format('Y-m-d'),
                        'to' => now()->endOfMonth()->format('Y-m-d'),
                    ],
                    'year' => [
                        'label' => 'Year to Date',
                        'from' => now()->startOfYear()->format('Y-m-d'),
                        'to' => now()->endOfYear()->format('Y-m-d'),
                    ],
                ];
            @endphp

            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach($quickPeriods as $period)
                    <a
                        href="{{ route('reports.index', [
                            'from' => $period['from'],
                            'to' => $period['to'],
                            'currency' => $selectedCurrency,
                        ]) }}"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        {{ $period['label'] }}
                    </a>
                @endforeach
            </div>

        </div>
    </div>

    {{-- CURRENCY NOTICE --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-info-circle-fill fs-5"></i>

        <div>
            <div class="fw-semibold">
                Multi-currency reporting
            </div>

            <div class="small">
                KES, USD and CDF are reported independently.
                No exchange-rate conversion or cross-currency addition is performed.
                Historical invoice, payment and maintenance currencies are preserved.
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    @if($selectedCurrency !== '' && $currentFinancial)

        <div class="row g-3 mb-4">

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Invoiced
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ $formatMoney(
                                $currentFinancial['invoiced'],
                                $selectedCurrency
                            ) }}
                        </div>

                        <div class="small text-muted mt-2">
                            {{ $currentFinancial['invoice_count'] }}
                            invoices
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Collected
                        </div>

                        <div class="fs-4 fw-bold text-success">
                            {{ $formatMoney(
                                $currentFinancial['collected'],
                                $selectedCurrency
                            ) }}
                        </div>

                        <div class="small text-muted mt-2">
                            {{ $currentFinancial['payment_count'] }}
                            completed payments
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Outstanding
                        </div>

                        <div class="fs-4 fw-bold text-warning">
                            {{ $formatMoney(
                                $currentFinancial['outstanding'],
                                $selectedCurrency
                            ) }}
                        </div>

                        <div class="small text-muted mt-2">
                            Current invoice balances
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Net Position
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ $formatMoney(
                                $currentFinancial['net_position'],
                                $selectedCurrency
                            ) }}
                        </div>

                        <div class="small text-muted mt-2">
                            Collections less maintenance
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @else

        {{-- ALL CURRENCIES --}}
        <div class="row g-3 mb-4">

            @foreach($supportedCurrencies as $currency)

                @php
                    $financial = $financialByCurrency[$currency] ?? [
                        'invoice_count' => 0,
                        'payment_count' => 0,
                        'maintenance_count' => 0,
                        'invoiced' => 0,
                        'collected' => 0,
                        'outstanding' => 0,
                        'maintenance' => 0,
                        'net_position' => 0,
                        'collection_rate' => 0,
                    ];
                @endphp

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-bold">
                                        {{ \App\Support\Currency::name($currency) }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $currency }}
                                    </div>
                                </div>

                                <span class="badge bg-light text-dark border">
                                    {{ \App\Support\Currency::symbol($currency) }}
                                </span>
                            </div>

                            <div class="row g-3">

                                <div class="col-6">
                                    <div class="small text-muted">
                                        Invoiced
                                    </div>

                                    <div class="fw-bold">
                                        {{ $formatMoney(
                                            $financial['invoiced'],
                                            $currency
                                        ) }}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="small text-muted">
                                        Collected
                                    </div>

                                    <div class="fw-bold text-success">
                                        {{ $formatMoney(
                                            $financial['collected'],
                                            $currency
                                        ) }}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="small text-muted">
                                        Outstanding
                                    </div>

                                    <div class="fw-bold text-warning">
                                        {{ $formatMoney(
                                            $financial['outstanding'],
                                            $currency
                                        ) }}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="small text-muted">
                                        Maintenance
                                    </div>

                                    <div class="fw-bold">
                                        {{ $formatMoney(
                                            $financial['maintenance'],
                                            $currency
                                        ) }}
                                    </div>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">
                                    Collection rate
                                </span>

                                <span class="fw-bold">
                                    {{ number_format(
                                        $financial['collection_rate'],
                                        1
                                    ) }}%
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>

    @endif

    {{-- OCCUPANCY + PERIOD INFO --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">
                                Portfolio Occupancy
                            </h5>

                            <div class="small text-muted">
                                Current unit status
                            </div>
                        </div>

                        <span class="badge bg-primary-subtle text-primary">
                            {{ number_format($occupancyRate, 1) }}%
                        </span>
                    </div>

                    <div class="progress" style="height: 12px;">
                        <div
                            class="progress-bar"
                            style="width: {{ $occupancyRate }}%;"
                        ></div>
                    </div>

                    <div class="row text-center mt-4">

                        <div class="col-4">
                            <div class="fs-4 fw-bold">
                                {{ $totalUnits }}
                            </div>

                            <div class="small text-muted">
                                Total Units
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="fs-4 fw-bold text-success">
                                {{ $occupiedUnits }}
                            </div>

                            <div class="small text-muted">
                                Occupied
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="fs-4 fw-bold text-secondary">
                                {{ $vacantUnits }}
                            </div>

                            <div class="small text-muted">
                                Vacant
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Reporting period
                    </div>

                    <div class="fs-5 fw-bold mb-3">
                        {{ $periodLabel }}
                    </div>

                    <div class="small text-muted">
                        Currency scope
                    </div>

                    <div class="fw-semibold mb-3">
                        {{ $selectedCurrencyLabel }}
                    </div>

                    <div class="small text-muted">
                        Active leases
                    </div>

                    <div class="fs-5 fw-bold">
                        {{ $activeLeases->count() }}
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- MONTHLY REPORT --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex flex-wrap justify-content-between gap-2">

                <div>
                    <h5 class="fw-bold mb-1">
                        Monthly Financial Report
                    </h5>

                    <div class="small text-muted">
                        Month-by-month performance for the selected period.
                    </div>
                </div>

                <span class="badge bg-light text-dark border align-self-start">
                    {{ count($monthlyFinancial) }}
                    months
                </span>

            </div>

        </div>

        <div class="card-body px-0">

            @if($selectedCurrency !== '')

                <div class="table-responsive">

                    <table class="table align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Month</th>
                                <th>Invoiced</th>
                                <th>Collected</th>
                                <th>Maintenance</th>
                                <th>Net Position</th>
                                <th>Collection</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($monthlyFinancial as $month)

                                @php
                                    $monthly =
                                        $month['currencies'][$selectedCurrency];
                                @endphp

                                <tr>

                                    <td class="ps-4 fw-semibold">
                                        {{ $month['label'] }}
                                    </td>

                                    <td>
                                        {{ $formatMoney(
                                            $monthly['invoiced'],
                                            $selectedCurrency
                                        ) }}
                                    </td>

                                    <td class="text-success fw-semibold">
                                        {{ $formatMoney(
                                            $monthly['collected'],
                                            $selectedCurrency
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $formatMoney(
                                            $monthly['maintenance'],
                                            $selectedCurrency
                                        ) }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $formatMoney(
                                            $monthly['net_position'],
                                            $selectedCurrency
                                        ) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ number_format(
                                                $monthly['collection_rate'],
                                                1
                                            ) }}%
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="6"
                                        class="text-center py-5 text-muted"
                                    >
                                        No monthly data for this period.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            @else

                <div class="table-responsive">

                    <table class="table align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">Month</th>

                                @foreach($supportedCurrencies as $currency)
                                    <th colspan="2" class="text-center">
                                        {{ $currency }}
                                    </th>
                                @endforeach
                            </tr>

                            <tr>
                                <th class="ps-4"></th>

                                @foreach($supportedCurrencies as $currency)
                                    <th class="small">Collected</th>
                                    <th class="small">Maintenance</th>
                                @endforeach
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($monthlyFinancial as $month)

                                <tr>

                                    <td class="ps-4 fw-semibold">
                                        {{ $month['label'] }}
                                    </td>

                                    @foreach($supportedCurrencies as $currency)

                                        @php
                                            $monthly =
                                                $month['currencies'][$currency];
                                        @endphp

                                        <td>
                                            {{ $formatMoney(
                                                $monthly['collected'],
                                                $currency
                                            ) }}
                                        </td>

                                        <td>
                                            {{ $formatMoney(
                                                $monthly['maintenance'],
                                                $currency
                                            ) }}
                                        </td>

                                    @endforeach

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-8">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Financial Performance
                            </h5>

                            <div class="small text-muted">
                                Monthly invoicing, collections and maintenance.
                            </div>
                        </div>

                        <select
                            id="chartCurrency"
                            class="form-select form-select-sm"
                            style="width: 180px;"
                        >

                            @foreach($supportedCurrencies as $currency)

                                <option
                                    value="{{ $currency }}"
                                    @selected(
                                        $selectedCurrency === $currency
                                        ||
                                        (
                                            $selectedCurrency === ''
                                            && $loop->first
                                        )
                                    )
                                >
                                    {{ \App\Support\Currency::name($currency) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div style="height: 350px;">
                        <canvas id="financialChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <h5 class="fw-bold mb-1">
                        Payment Methods
                    </h5>

                    <div class="small text-muted mb-4">
                        Completed payments during this period.
                    </div>

                    <div style="height: 300px;">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- EXPIRING LEASES --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <h5 class="fw-bold mb-1">
                Lease Expiry Watch
            </h5>

            <div class="small text-muted">
                Active leases ending within the next 30 days.
            </div>

        </div>

        <div class="card-body px-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tenant</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>End Date</th>
                            <th>Rent</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($expiringLeases as $lease)

                            @php
                                $leaseCurrency =
                                    strtoupper(
                                        (string) $lease->currency
                                    );
                            @endphp

                            <tr>

                                <td class="ps-4 fw-semibold">
                                    {{ $lease->tenant?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $lease->unit?->property?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $lease->unit?->unit_number ?? '—' }}
                                </td>

                                <td>
                                    {{ $lease->end_date
                                        ? \Carbon\Carbon::parse(
                                            $lease->end_date
                                        )->format('d M Y')
                                        : '—'
                                    }}
                                </td>

                                <td>

                                    @if(in_array(
                                        $leaseCurrency,
                                        $supportedCurrencies,
                                        true
                                    ))

                                        {{ $formatMoney(
                                            $lease->monthly_rent,
                                            $leaseCurrency
                                        ) }}

                                    @else
                                        —
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="text-center py-5 text-muted"
                                >
                                    No leases are expiring within 30 days.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- PAYMENTS --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">
                        Payment Activity
                    </h5>

                    <div class="small text-muted">
                        Completed payments recorded during the selected period.
                    </div>
                </div>

                <span class="badge bg-primary-subtle text-primary">
                    {{ $payments->count() }}
                    payments
                </span>

            </div>

        </div>

        <div class="card-body px-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Tenant</th>
                            <th>Invoice</th>
                            <th>Method</th>
                            <th>Currency</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($payments as $payment)

                            @php
                                $paymentCurrency =
                                    strtoupper(
                                        (string) $payment->currency
                                    );
                            @endphp

                            <tr>

                                <td class="ps-4">
                                    {{ $payment->payment_date
                                        ? \Carbon\Carbon::parse(
                                            $payment->payment_date
                                        )->format('d M Y')
                                        : '—'
                                    }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $payment->rentInvoice?->lease?->tenant?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $payment->rentInvoice?->invoice_number ?? '—' }}
                                </td>

                                <td>
                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $payment->payment_method ?? 'Manual'
                                        )
                                    ) }}
                                </td>

                                <td>

                                    @if(in_array(
                                        $paymentCurrency,
                                        $supportedCurrencies,
                                        true
                                    ))

                                        <span class="badge bg-light text-dark border">
                                            {{ $paymentCurrency }}
                                        </span>

                                    @else
                                        —
                                    @endif

                                </td>

                                <td class="text-end pe-4 fw-semibold">

                                    @if(in_array(
                                        $paymentCurrency,
                                        $supportedCurrencies,
                                        true
                                    ))

                                        {{ $formatMoney(
                                            $payment->amount,
                                            $paymentCurrency
                                        ) }}

                                    @else

                                        {{ number_format(
                                            (float) $payment->amount,
                                            2
                                        ) }}

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="text-center py-5 text-muted"
                                >
                                    No completed payments found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- MAINTENANCE --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">
                        Maintenance Expenses
                    </h5>

                    <div class="small text-muted">
                        Expenses recorded during the selected period.
                    </div>
                </div>

                <span class="badge bg-light text-dark border">
                    {{ $maintenanceExpenses->count() }}
                    expenses
                </span>

            </div>

        </div>

        <div class="card-body px-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Description</th>
                            <th>Currency</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($maintenanceExpenses as $expense)

                            @php
                                $expenseCurrency =
                                    strtoupper(
                                        (string) $expense->currency
                                    );

                                $maintenanceRequest =
                                    $expense->maintenanceRequest;
                            @endphp

                            <tr>

                                <td class="ps-4">
                                    {{ $expense->expense_date
                                        ? \Carbon\Carbon::parse(
                                            $expense->expense_date
                                        )->format('d M Y')
                                        : '—'
                                    }}
                                </td>

                                <td>
                                    {{ $maintenanceRequest?->unit?->property?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $maintenanceRequest?->unit?->unit_number ?? '—' }}
                                </td>

                                <td>
                                    {{ $maintenanceRequest?->description ?? 'Maintenance expense' }}
                                </td>

                                <td>

                                    @if(in_array(
                                        $expenseCurrency,
                                        $supportedCurrencies,
                                        true
                                    ))

                                        <span class="badge bg-light text-dark border">
                                            {{ $expenseCurrency }}
                                        </span>

                                    @else
                                        —
                                    @endif

                                </td>

                                <td class="text-end pe-4 fw-semibold">

                                    @if(in_array(
                                        $expenseCurrency,
                                        $supportedCurrencies,
                                        true
                                    ))

                                        {{ $formatMoney(
                                            $expense->amount,
                                            $expenseCurrency
                                        ) }}

                                    @else

                                        {{ number_format(
                                            (float) $expense->amount,
                                            2
                                        ) }}

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="text-center py-5 text-muted"
                                >
                                    No maintenance expenses found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection

@push('styles')

<style>
    @media print {
        .navbar,
        .sidebar,
        .btn,
        form,
        .alert {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .container-fluid {
            width: 100% !important;
        }
    }
</style>

@endpush

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @json($monthlyLabels);

    const monthlyData = @json($monthlyChartData);

    const supportedCurrencies =
        @json($supportedCurrencies);

    const currencySymbols = {
        KES: 'KSh',
        USD: '$',
        CDF: 'FC'
    };

    const selectedCurrency =
        @json(
            $selectedCurrency !== ''
                ? $selectedCurrency
                : ''
        );

    const chartCurrencySelect =
        document.getElementById(
            'chartCurrency'
        );

    const financialCanvas =
        document.getElementById(
            'financialChart'
        );

    const paymentCanvas =
        document.getElementById(
            'paymentMethodChart'
        );

    let financialChart = null;

    function moneyLabel(value, currency) {

        const symbol =
            currencySymbols[currency] || currency;

        return symbol + ' ' +
            Number(value).toLocaleString(
                undefined,
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
    }

    function renderFinancialChart(currency) {

        if (!monthlyData[currency]) {
            return;
        }

        if (financialChart) {
            financialChart.destroy();
        }

        financialChart = new Chart(
            financialCanvas,
            {
                type: 'line',

                data: {
                    labels: labels,

                    datasets: [
                        {
                            label: 'Invoiced',
                            data: monthlyData[currency].invoiced,
                            tension: 0.35,
                            borderWidth: 2
                        },
                        {
                            label: 'Collected',
                            data: monthlyData[currency].collected,
                            tension: 0.35,
                            borderWidth: 2
                        },
                        {
                            label: 'Maintenance',
                            data: monthlyData[currency].maintenance,
                            tension: 0.35,
                            borderWidth: 2
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },

                    plugins: {
                        legend: {
                            position: 'bottom'
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {

                                    return context.dataset.label
                                        + ': '
                                        + moneyLabel(
                                            context.raw,
                                            currency
                                        );
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {

                                    return moneyLabel(
                                        value,
                                        currency
                                    );
                                }
                            }
                        }
                    }
                }
            }
        );
    }

    const initialCurrency =
        selectedCurrency ||
        supportedCurrencies[0];

    if (financialCanvas) {
        renderFinancialChart(
            initialCurrency
        );
    }

    if (chartCurrencySelect) {

        chartCurrencySelect.addEventListener(
            'change',
            function () {

                renderFinancialChart(
                    this.value
                );
            }
        );
    }

    if (paymentCanvas) {

        new Chart(
            paymentCanvas,
            {
                type: 'doughnut',

                data: {
                    labels: @json($methodLabels),

                    datasets: [
                        {
                            data: @json($methodValues)
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            }
        );
    }
});
</script>

@endpush