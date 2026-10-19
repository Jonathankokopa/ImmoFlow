@extends('layouts.app')

@section('title', 'Payments Management')
@section('page-heading', 'Payments Overview')

@section('content')

<style>
    /* =========================================================
       PAYMENTS PAGE
    ========================================================== */

    .payments-page {
        --payment-border: #e2e8f0;
        --payment-muted: #64748b;
        --payment-dark: #0f172a;
        --payment-soft: #f8fafc;
        --payment-white: #ffffff;
    }

    /* =========================================================
       HEADER
    ========================================================== */

    .payments-header {
        background: linear-gradient(
            135deg,
            #0f172a 0%,
            #1e293b 100%
        );
        border-radius: 18px;
        padding: 24px;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        margin-bottom: 24px;
    }

    .payments-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .payments-header h1 {
        letter-spacing: -0.025em;
    }

    .payments-header p {
        color: rgba(255, 255, 255, 0.68);
    }

    .payments-header .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    .payments-header .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.45);
        color: #ffffff;
    }

    /* =========================================================
       ALERTS
    ========================================================== */

    .payment-alert {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }

    /* =========================================================
       STAT CARDS
    ========================================================== */

    .payment-stat-card {
        height: 100%;
        background: var(--payment-white);
        border: 1px solid var(--payment-border);
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }

    .payment-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .payment-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .payment-stat-label {
        color: var(--payment-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.045em;
    }

    .payment-currency-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 7px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .payment-currency-row:last-child {
        border-bottom: 0;
    }

    .currency-code {
        min-width: 40px;
        display: inline-flex;
        justify-content: center;
        padding: 4px 7px;
        border-radius: 7px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    /* =========================================================
       FILTER BAR
    ========================================================== */

    .payment-toolbar {
        background: #ffffff;
        border: 1px solid var(--payment-border);
        border-radius: 16px 16px 0 0;
        padding: 14px 16px;
    }

    .payment-filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .payment-filter-tabs .nav-link {
        border-radius: 9px;
        padding: 7px 11px;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .payment-filter-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .payment-filter-tabs .nav-link.active {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.12);
    }

    .payment-search {
        border-radius: 10px;
        overflow: hidden;
    }

    .payment-search .input-group-text {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
    }

    .payment-search .form-control {
        background: #f8fafc;
        border-color: #e2e8f0;
        font-size: 0.84rem;
    }

    .payment-search .form-control:focus {
        background: #ffffff;
        box-shadow: none;
        border-color: #94a3b8;
    }

    /* =========================================================
       TABLE
    ========================================================== */

    .payments-table-card {
        background: #ffffff;
        border: 1px solid var(--payment-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 22px rgba(15, 23, 42, 0.05);
    }

    .payments-table-wrapper {
        overflow-x: auto;
    }

    .payments-table {
        min-width: 1180px;
        margin-bottom: 0;
    }

    .payments-table thead th {
        background: #f8fafc;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        border-top: 0;
        padding: 12px 14px;
        font-size: 0.69rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.055em;
        white-space: nowrap;
    }

    .payments-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 0.82rem;
        vertical-align: middle;
    }

    .payments-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .payments-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payments-table tbody tr:last-child td {
        border-bottom: 0;
    }

    /* =========================================================
       REFERENCE
    ========================================================== */

    .payment-reference {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 5px 8px;
        border-radius: 7px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
    }

    /* =========================================================
       TENANT
    ========================================================== */

    .tenant-avatar {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: linear-gradient(
            135deg,
            #3b82f6 0%,
            #1d4ed8 100%
        );
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.18);
    }

    .tenant-name {
        font-weight: 700;
        color: #0f172a;
        text-decoration: none;
    }

    .tenant-name:hover {
        color: #2563eb;
    }

    /* =========================================================
       PROPERTY
    ========================================================== */

    .property-name {
        font-weight: 600;
        color: #334155;
        margin-bottom: 3px;
    }

    .unit-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 7px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 600;
    }

    /* =========================================================
       INVOICE
    ========================================================== */

    .invoice-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 7px;
        padding: 5px 8px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        text-decoration: none;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .invoice-badge:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* =========================================================
       AMOUNT
    ========================================================== */

    .payment-amount {
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
    }

    .payment-currency-name {
        color: #94a3b8;
        font-size: 0.66rem;
        margin-top: 2px;
    }

    /* =========================================================
       STATUS
    ========================================================== */

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-completed {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-completed .status-dot {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }

    .status-pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-pending .status-dot {
        background: #f59e0b;
        animation: pulse-amber 1.8s infinite;
    }

    .status-failed {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .status-failed .status-dot {
        background: #ef4444;
    }

    .status-cancelled {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .status-cancelled .status-dot {
        background: #9ca3af;
    }

    .status-refunded {
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .status-refunded .status-dot {
        background: #0284c7;
    }

    @keyframes pulse-amber {
        0% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.35);
        }

        70% {
            box-shadow: 0 0 0 5px rgba(245, 158, 11, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
        }
    }

    /* =========================================================
       PAYMENT METHOD
    ========================================================== */

    .method-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 7px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .bg-mpesa {
        background: #ecfdf5;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .bg-airtel {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .bg-bank {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .bg-cash {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    /* =========================================================
       PROVIDER
    ========================================================== */

    .provider-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 7px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.69rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* =========================================================
       TRANSACTION REFERENCE
    ========================================================== */

    .transaction-reference {
        display: inline-block;
        max-width: 145px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding: 4px 7px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.68rem;
    }

    /* =========================================================
       ACTIONS
    ========================================================== */

    .payment-actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
    }

    .payment-action {
        width: 31px;
        height: 31px;
        padding: 0;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .payment-action:hover {
        transform: translateY(-1px);
    }

    /* =========================================================
       FOOTER
    ========================================================== */

    .payments-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 12px 16px;
        color: #64748b;
        font-size: 0.75rem;
    }

    /* =========================================================
       EMPTY STATE
    ========================================================== */

    .payment-empty {
        padding: 70px 20px;
        text-align: center;
    }

    .payment-empty-icon {
        width: 68px;
        height: 68px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #94a3b8;
        margin-bottom: 18px;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 767.98px) {

        .container-fluid {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .payments-header {
            padding: 18px;
        }

        .payments-header .header-actions {
            width: 100%;
        }

        .payments-header .header-actions .btn {
            flex: 1;
        }

        .payment-toolbar {
            padding: 12px;
        }

        .payment-filter-tabs {
            width: 100%;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 3px;
        }

        .payment-filter-tabs .nav-link {
            flex-shrink: 0;
        }
    }
</style>


@php

    use App\Models\Payment;
    use App\Support\Currency;

    /*
     * Supported currencies are fixed.
     *
     * There is deliberately NO USD fallback.
     */
    $supportedCurrencies = Currency::SUPPORTED;


    /*
     * Completed payments only.
     *
     * Currency totals remain separate.
     */
    $completedPayments = $payments->filter(
        fn ($payment) =>
            $payment->status === Payment::STATUS_COMPLETED
    );


    $collectedByCurrency = collect(
        $supportedCurrencies
    )->mapWithKeys(function ($currency) use ($completedPayments) {

        $amount = $completedPayments
            ->filter(function ($payment) use ($currency) {

                $paymentCurrency = strtoupper(
                    trim(
                        (string) (
                            $payment->currency
                            ?? $payment->rentInvoice?->currency
                            ?? $payment->rentInvoice?->lease?->currency
                            ?? ''
                        )
                    )
                );

                return $paymentCurrency === $currency;
            })
            ->sum(
                fn ($payment) =>
                    (float) ($payment->amount ?? 0)
            );

        return [
            $currency => round($amount, 2),
        ];
    });


    $paymentMethodsCount = $payments
        ->pluck('payment_method')
        ->filter()
        ->unique()
        ->count();


    /*
     * Controller is expected to return latest payments first.
     */
    $latestPayment = $payments->first();


    /*
     * Display-only sequential number.
     *
     * This is NOT the database ID.
     */
    $latestPaymentDisplayNumber = null;

    if ($latestPayment) {

        $latestIndex = $payments->search(
            fn ($payment) =>
                $payment->id === $latestPayment->id
        );

        if ($latestIndex !== false) {

            if (
                method_exists($payments, 'currentPage')
                && method_exists($payments, 'perPage')
            ) {

                $latestPaymentDisplayNumber =
                    (
                        ($payments->currentPage() - 1)
                        * $payments->perPage()
                    )
                    + $latestIndex
                    + 1;

            } else {

                $latestPaymentDisplayNumber =
                    $latestIndex + 1;

            }
        }
    }


    $statusCounts = $payments
        ->groupBy('status')
        ->map(
            fn ($items) => $items->count()
        );


    $totalTransactionsCount = $payments->count();


    $completedCount =
        $statusCounts[Payment::STATUS_COMPLETED] ?? 0;


    $successRate = $totalTransactionsCount > 0
        ? round(
            ($completedCount / $totalTransactionsCount) * 100
        )
        : 0;

@endphp


<div class="container-fluid px-3 px-lg-4 py-4 payments-page">


    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    <div class="payments-header">

        <div class="row align-items-center g-3">

            <div class="col-lg-8">

                <div class="d-flex align-items-center gap-3">

                    <div class="payments-header-icon">

                        <i class="bi bi-wallet2"></i>

                    </div>


                    <div>

                        <div class="text-white-50 small fw-semibold mb-1">
                            FINANCIAL MANAGEMENT
                        </div>

                        <h1 class="h3 fw-bold mb-1">
                            Payment Transactions
                        </h1>

                        <p class="small mb-0">
                            Track collections, payment activity,
                            and transaction records across all supported currencies.
                        </p>

                    </div>

                </div>

            </div>


            <div class="col-lg-4">

                <div class="d-flex justify-content-lg-end align-items-center gap-2 header-actions">

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-outline-light rounded-3 px-3"
                    >
                        <i class="bi bi-printer me-1"></i>
                        Print
                    </button>


                    <a
                        href="{{ route('payments.create') }}"
                        class="btn btn-light rounded-3 px-3 fw-semibold"
                    >
                        <i class="bi bi-plus-circle-fill me-1"></i>
                        Record Payment
                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ALERTS
    ========================================================== --}}

    @if(session('success'))

        <div
            class="alert alert-success payment-alert alert-dismissible fade show mb-4"
            role="alert"
        >

            <div class="d-flex align-items-center">

                <i class="bi bi-check-circle-fill fs-5 me-3"></i>

                <div>

                    <strong>Success!</strong>

                    {{ session('success') }}

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
            class="alert alert-danger payment-alert alert-dismissible fade show mb-4"
            role="alert"
        >

            <div class="d-flex align-items-center">

                <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>

                <div>

                    <strong>Action Required:</strong>

                    {{ session('error') }}

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

        <div class="alert alert-danger payment-alert mb-4">

            <div class="fw-bold mb-2">

                <i class="bi bi-shield-x me-2"></i>

                Please fix the following validation errors:

            </div>


            <ul class="mb-0 ps-4 small">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         STATISTICS
    ========================================================== --}}

    <div class="row g-3 mb-4">


        {{-- Revenue --}}

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payment-stat-card p-3">

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>

                        <div class="payment-stat-label">
                            Revenue Collected
                        </div>

                        <div class="text-muted small mt-1">
                            Completed payments only
                        </div>

                    </div>


                    <div class="payment-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                </div>


                @foreach($supportedCurrencies as $currency)

                    <div class="payment-currency-row">

                        <span class="currency-code">
                            {{ $currency }}
                        </span>

                        <span class="fw-bold text-success small text-end">

                            {{ Currency::format(
                                $collectedByCurrency[$currency] ?? 0,
                                $currency
                            ) }}

                        </span>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- Transactions --}}

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payment-stat-card p-3">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="payment-stat-label">
                            Transactions
                        </div>

                        <div class="text-muted small mt-1">
                            Payment records
                        </div>

                    </div>


                    <div class="payment-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>

                </div>


                <div class="mt-4">

                    <div class="display-6 fw-bold text-dark">
                        {{ $totalTransactionsCount }}
                    </div>

                    <div class="small text-muted mt-1">

                        <i class="bi bi-check-circle text-success me-1"></i>

                        {{ $successRate }}% completed

                    </div>

                </div>

            </div>

        </div>


        {{-- Channels --}}

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payment-stat-card p-3">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="payment-stat-label">
                            Payment Channels
                        </div>

                        <div class="text-muted small mt-1">
                            Active methods
                        </div>

                    </div>


                    <div class="payment-stat-icon bg-info-subtle text-info">
                        <i class="bi bi-credit-card"></i>
                    </div>

                </div>


                <div class="mt-4">

                    <div class="display-6 fw-bold text-dark">
                        {{ $paymentMethodsCount }}
                    </div>

                    <div class="small text-muted mt-1">
                        M-Pesa, bank, cash & mobile
                    </div>

                </div>

            </div>

        </div>


        {{-- Latest Activity --}}

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payment-stat-card p-3">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="payment-stat-label">
                            Latest Activity
                        </div>

                        <div class="text-muted small mt-1">
                            Most recent payment
                        </div>

                    </div>


                    <div class="payment-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>

                </div>


                <div class="mt-4">

                    @if($latestPayment)

                        <div class="fw-bold text-dark">

                            {{
                                $latestPayment->payment_date?->format('d M Y')
                                ?? '—'
                            }}

                        </div>


                        @php

                            $latestCurrency = strtoupper(
                                trim(
                                    (string) (
                                        $latestPayment->currency
                                        ?? $latestPayment->rentInvoice?->currency
                                        ?? $latestPayment->rentInvoice?->lease?->currency
                                        ?? ''
                                    )
                                )
                            );

                            $latestCurrencySupported =
                                in_array(
                                    $latestCurrency,
                                    $supportedCurrencies,
                                    true
                                );

                        @endphp


                        <div class="small text-muted mt-1">

                            @if($latestPaymentDisplayNumber !== null)

                                #{{ $latestPaymentDisplayNumber }}

                            @endif

                            @if($latestPayment->payment_method)

                                ·
                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $latestPayment->payment_method
                                        )
                                    )
                                }}

                            @endif

                            ·

                            @if($latestCurrencySupported)

                                {{ $latestCurrency }}

                            @else

                                Currency Required

                            @endif

                        </div>

                    @else

                        <div class="text-muted">
                            No activity
                        </div>

                        <div class="small text-muted mt-1">
                            Waiting for first payment
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PAYMENTS TABLE
    ========================================================== --}}

    <div class="payments-table-card">


        {{-- Toolbar --}}

        <div class="payment-toolbar">

            <div class="row align-items-center g-3">

                <div class="col-xl-7">

                    <div
                        class="payment-filter-tabs nav nav-pills"
                        id="paymentFilterTabs"
                    >

                        <button
                            type="button"
                            class="nav-link active"
                            onclick="filterTable('all', this)"
                        >

                            All

                            <span class="badge bg-secondary-subtle text-dark rounded-pill ms-1">

                                {{ $totalTransactionsCount }}

                            </span>

                        </button>


                        <button
                            type="button"
                            class="nav-link"
                            onclick="filterTable('completed', this)"
                        >

                            Completed

                            <span class="badge bg-success-subtle text-success rounded-pill ms-1">

                                {{ $statusCounts[Payment::STATUS_COMPLETED] ?? 0 }}

                            </span>

                        </button>


                        <button
                            type="button"
                            class="nav-link"
                            onclick="filterTable('pending', this)"
                        >

                            Pending

                            <span class="badge bg-warning-subtle text-warning rounded-pill ms-1">

                                {{ $statusCounts[Payment::STATUS_PENDING] ?? 0 }}

                            </span>

                        </button>


                        <button
                            type="button"
                            class="nav-link"
                            onclick="filterTable('failed', this)"
                        >

                            Failed / Cancelled

                            <span class="badge bg-danger-subtle text-danger rounded-pill ms-1">

                                {{
                                    ($statusCounts[Payment::STATUS_FAILED] ?? 0)
                                    +
                                    ($statusCounts[Payment::STATUS_CANCELLED] ?? 0)
                                }}

                            </span>

                        </button>

                    </div>

                </div>


                <div class="col-xl-5">

                    <div class="input-group payment-search">

                        <span class="input-group-text border-end-0">

                            <i class="bi bi-search"></i>

                        </span>


                        <input
                            type="text"
                            id="paymentSearchInput"
                            class="form-control border-start-0 ps-0"
                            placeholder="Search tenant, invoice, property, reference..."
                            onkeyup="searchPaymentTable()"
                        >


                        <button
                            type="button"
                            class="btn btn-light border"
                            onclick="clearSearch()"
                            title="Clear search"
                        >

                            <i class="bi bi-x-lg"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- Table --}}

        <div class="payments-table-wrapper">

            @if($payments->count())

                <table
                    class="table payments-table"
                    id="paymentsTable"
                >

                    <thead>

                        <tr>

                            <th style="width: 65px;">
                                Ref
                            </th>

                            <th>
                                Tenant
                            </th>

                            <th>
                                Property / Unit
                            </th>

                            <th>
                                Invoice
                            </th>

                            <th>
                                Payment Date
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Method
                            </th>

                            <th>
                                Provider
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Transaction
                            </th>

                            <th
                                class="text-end"
                                style="width: 135px;"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($payments as $payment)

                            @php

                                /*
                                 * Sequential display number.
                                 *
                                 * Never use the database ID as the
                                 * visible payment reference.
                                 */
                                if (
                                    method_exists($payments, 'currentPage')
                                    && method_exists($payments, 'perPage')
                                ) {

                                    $displayNumber =
                                        (
                                            ($payments->currentPage() - 1)
                                            * $payments->perPage()
                                        )
                                        + $loop->iteration;

                                } else {

                                    $displayNumber =
                                        $loop->iteration;

                                }


                                $paymentAmount = (float) (
                                    $payment->amount ?? 0
                                );


                                $invoice =
                                    $payment->rentInvoice;


                                /*
                                 * Historical payment currency is
                                 * authoritative.
                                 *
                                 * No USD fallback.
                                 */
                                $currency = strtoupper(
                                    trim(
                                        (string) (
                                            $payment->currency
                                            ?? $invoice?->currency
                                            ?? $invoice?->lease?->currency
                                            ?? ''
                                        )
                                    )
                                );


                                $currencySupported =
                                    in_array(
                                        $currency,
                                        $supportedCurrencies,
                                        true
                                    );


                                $isProviderPayment =
                                    $payment->provider !== null;


                                $tenant =
                                    $invoice?->lease?->tenant;


                                $unit =
                                    $invoice?->lease?->unit;


                                $property =
                                    $unit?->property;


                                $tenantName =
                                    $tenant?->full_name
                                    ?? $tenant?->name
                                    ?? 'Direct Payment';


                                $tenantInitials =
                                    strtoupper(
                                        substr(
                                            $tenantName,
                                            0,
                                            2
                                        )
                                    );


                                $statusPillClass = match (
                                    $payment->status
                                ) {

                                    Payment::STATUS_COMPLETED =>
                                        'status-completed',

                                    Payment::STATUS_PENDING =>
                                        'status-pending',

                                    Payment::STATUS_FAILED =>
                                        'status-failed',

                                    Payment::STATUS_CANCELLED =>
                                        'status-cancelled',

                                    Payment::STATUS_REFUNDED =>
                                        'status-refunded',

                                    default =>
                                        'status-cancelled',
                                };


                                $statusLabel = match (
                                    $payment->status
                                ) {

                                    Payment::STATUS_PENDING =>
                                        'Pending',

                                    Payment::STATUS_COMPLETED =>
                                        'Completed',

                                    Payment::STATUS_FAILED =>
                                        'Failed',

                                    Payment::STATUS_CANCELLED =>
                                        'Cancelled',

                                    Payment::STATUS_REFUNDED =>
                                        'Refunded',

                                    default =>
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $payment->status
                                            )
                                        ),
                                };


                                $methodLower = strtolower(
                                    $payment->payment_method ?? ''
                                );


                                $methodClass = match (true) {

                                    str_contains(
                                        $methodLower,
                                        'mpesa'
                                    )
                                    || str_contains(
                                        $methodLower,
                                        'm-pesa'
                                    ) =>
                                        'bg-mpesa',

                                    str_contains(
                                        $methodLower,
                                        'airtel'
                                    ) =>
                                        'bg-airtel',

                                    str_contains(
                                        $methodLower,
                                        'bank'
                                    )
                                    || str_contains(
                                        $methodLower,
                                        'transfer'
                                    ) =>
                                        'bg-bank',

                                    str_contains(
                                        $methodLower,
                                        'cash'
                                    ) =>
                                        'bg-cash',

                                    default =>
                                        'bg-light text-dark',
                                };


                                $methodIcon = match (true) {

                                    str_contains(
                                        $methodLower,
                                        'mpesa'
                                    )
                                    || str_contains(
                                        $methodLower,
                                        'm-pesa'
                                    ) =>
                                        'bi-phone',

                                    str_contains(
                                        $methodLower,
                                        'airtel'
                                    ) =>
                                        'bi-phone-vibrate',

                                    str_contains(
                                        $methodLower,
                                        'bank'
                                    ) =>
                                        'bi-bank',

                                    str_contains(
                                        $methodLower,
                                        'cash'
                                    ) =>
                                        'bi-cash',

                                    default =>
                                        'bi-credit-card',
                                };

                            @endphp


                            <tr
                                data-status="{{ strtolower($payment->status) }}"
                                data-search="{{ strtolower(
                                    $tenantName
                                    . ' '
                                    . ($invoice?->invoice_number ?? '')
                                    . ' '
                                    . ($property?->name ?? '')
                                    . ' '
                                    . ($payment->transaction_reference ?? '')
                                    . ' '
                                    . $payment->id
                                    . ' '
                                    . $displayNumber
                                    . ' '
                                    . $currency
                                ) }}"
                            >


                                {{-- Reference --}}

                                <td>

                                    <span
                                        class="payment-reference"
                                        title="Sequential display reference"
                                    >
                                        #{{ $displayNumber }}
                                    </span>

                                </td>


                                {{-- Tenant --}}

                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <div class="tenant-avatar">
                                            {{ $tenantInitials }}
                                        </div>


                                        <div>

                                            @if($tenant)

                                                <a
                                                    href="{{ route('tenants.show', $tenant) }}"
                                                    class="tenant-name"
                                                >
                                                    {{ $tenantName }}
                                                </a>

                                            @else

                                                <div class="fw-semibold text-muted">
                                                    Unassigned / System
                                                </div>

                                            @endif


                                            <div class="text-muted small mt-1">

                                                By
                                                {{ $payment->recordedBy?->name ?? 'System' }}

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Property / Unit --}}

                                <td>

                                    @if($property)

                                        <div class="property-name">
                                            {{ $property->name }}
                                        </div>


                                        @if($unit)

                                            <span class="unit-badge">

                                                <i class="bi bi-door-open me-1"></i>

                                                Unit {{ $unit->unit_number }}

                                            </span>

                                        @endif

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Invoice --}}

                                <td>

                                    @if($invoice)

                                        <a
                                            href="{{ route('rent-invoices.show', $invoice) }}"
                                            class="invoice-badge"
                                        >

                                            <i class="bi bi-file-text me-1"></i>

                                            {{ $invoice->invoice_number }}

                                        </a>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Payment Date --}}

                                <td>

                                    <div class="fw-semibold text-dark">

                                        {{
                                            $payment->payment_date?->format('d M Y')
                                            ?? '—'
                                        }}

                                    </div>


                                    @if($payment->payment_date)

                                        <div class="text-muted small">

                                            {{
                                                $payment->payment_date->format('H:i')
                                            }}

                                        </div>

                                    @endif

                                </td>


                                {{-- Amount --}}

                                <td>

                                    @if($currencySupported)

                                        <div class="payment-amount">

                                            {{ Currency::format(
                                                $paymentAmount,
                                                $currency
                                            ) }}

                                        </div>


                                        <div class="payment-currency-name">

                                            {{ Currency::name($currency) }}

                                        </div>

                                    @else

                                        <div class="payment-amount">

                                            {{ number_format(
                                                $paymentAmount,
                                                2
                                            ) }}

                                        </div>


                                        <span class="badge bg-warning-subtle text-warning border mt-1">

                                            Currency Required

                                        </span>

                                    @endif

                                </td>


                                {{-- Method --}}

                                <td>

                                    <span class="method-badge {{ $methodClass }}">

                                        <i class="bi {{ $methodIcon }}"></i>

                                        {{
                                            $payment->payment_method
                                                ? ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->payment_method
                                                    )
                                                )
                                                : 'Manual'
                                        }}

                                    </span>

                                </td>


                                {{-- Provider --}}

                                <td>

                                    @if($payment->provider)

                                        <span class="provider-badge">

                                            <i class="bi bi-cpu"></i>

                                            {{
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->provider
                                                    )
                                                )
                                            }}

                                        </span>

                                    @else

                                        <span class="text-muted small">
                                            Manual Record
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}

                                <td>

                                    <span class="status-pill {{ $statusPillClass }}">

                                        <span class="status-dot"></span>

                                        {{ $statusLabel }}

                                    </span>

                                </td>


                                {{-- Transaction Reference --}}

                                <td>

                                    @if($payment->transaction_reference)

                                        <code
                                            class="transaction-reference"
                                            title="{{ $payment->transaction_reference }}"
                                        >

                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $payment->transaction_reference,
                                                    18
                                                )
                                            }}

                                        </code>

                                    @else

                                        <span class="text-muted small">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td class="text-end">

                                    <div class="payment-actions">


                                        {{-- View --}}

                                        <a
                                            href="{{ route('payments.show', $payment) }}"
                                            class="btn btn-sm btn-light border payment-action text-primary"
                                            title="View Payment"
                                        >

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Receipt --}}

                                        @if(Route::has('payments.receipt'))

                                            <a
                                                href="{{ route('payments.receipt', $payment) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-light border payment-action text-success"
                                                title="View Receipt"
                                            >

                                                <i class="bi bi-receipt"></i>

                                            </a>

                                        @endif


                                        @if(!$isProviderPayment)


                                            {{-- Edit --}}

                                            <a
                                                href="{{ route('payments.edit', $payment) }}"
                                                class="btn btn-sm btn-light border payment-action text-warning"
                                                title="Edit Payment"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            {{-- Delete --}}

                                            <form
                                                action="{{ route('payments.destroy', $payment) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete/void this payment entry?');"
                                            >

                                                @csrf

                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-light border payment-action text-danger"
                                                    title="Delete Payment"
                                                >

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                {{-- =====================================================
                     EMPTY STATE
                ====================================================== --}}

                <div class="payment-empty">

                    <div class="payment-empty-icon">

                        <i class="bi bi-cash-stack"></i>

                    </div>


                    <h5 class="fw-bold text-dark mb-2">
                        No Payment Records Found
                    </h5>


                    <p
                        class="text-muted small mb-4 mx-auto"
                        style="max-width: 420px;"
                    >
                        There are currently no payment transactions
                        recorded in the system.
                    </p>


                    <a
                        href="{{ route('payments.create') }}"
                        class="btn btn-primary rounded-3 px-4"
                    >

                        <i class="bi bi-plus-circle me-1"></i>

                        Record First Payment

                    </a>

                </div>

            @endif

        </div>


        {{-- Footer --}}

        <div class="payments-footer">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                <div>

                    Showing

                    <strong class="text-dark">
                        {{ $payments->count() }}
                    </strong>

                    payment entries

                </div>


                <div>

                    <i class="bi bi-shield-check text-success me-1"></i>

                    Secure Financial Records

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
     FILTERING / SEARCH
============================================================== --}}

<script>

    let activePaymentFilter = 'all';


    function filterTable(status, element) {

        activePaymentFilter = status;


        document
            .querySelectorAll('#paymentFilterTabs .nav-link')
            .forEach(btn => {
                btn.classList.remove('active');
            });


        if (element) {
            element.classList.add('active');
        }


        applyPaymentFilters();
    }


    function searchPaymentTable() {

        applyPaymentFilters();

    }


    function applyPaymentFilters() {

        const searchInput =
            document.getElementById(
                'paymentSearchInput'
            );


        const searchTerm =
            searchInput
                ? searchInput.value.toLowerCase().trim()
                : '';


        const table =
            document.getElementById(
                'paymentsTable'
            );


        if (!table) {
            return;
        }


        const rows =
            table.querySelectorAll(
                'tbody tr'
            );


        rows.forEach(row => {

            const rowStatus =
                row.getAttribute(
                    'data-status'
                ) || '';


            const searchData =
                row.getAttribute(
                    'data-search'
                ) || '';


            let matchesStatus = false;


            switch (activePaymentFilter) {

                case 'completed':

                    matchesStatus =
                        rowStatus === 'completed';

                    break;


                case 'pending':

                    matchesStatus =
                        rowStatus === 'pending';

                    break;


                case 'failed':

                    matchesStatus =
                        rowStatus === 'failed'
                        || rowStatus === 'cancelled';

                    break;


                case 'all':

                default:

                    matchesStatus = true;

                    break;

            }


            const matchesSearch =
                !searchTerm
                || searchData.includes(
                    searchTerm
                );


            row.style.display =
                matchesStatus && matchesSearch
                    ? ''
                    : 'none';

        });

    }


    function clearSearch() {

        const input =
            document.getElementById(
                'paymentSearchInput'
            );


        if (input) {
            input.value = '';
        }


        applyPaymentFilters();

    }

</script>

@endsection