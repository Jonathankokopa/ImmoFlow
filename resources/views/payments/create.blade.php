@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-heading', 'Record Payment')

@section('content')

<style>
    .payment-page {
        --payment-border: #e5e7eb;
        --payment-muted: #64748b;
        --payment-dark: #0f172a;
        --payment-soft: #f8fafc;
        --payment-primary: #0f766e;
        --payment-primary-light: #ccfbf1;
    }

    .payment-page .page-header {
        margin-bottom: 1.5rem;
    }

    .payment-page .page-title {
        color: var(--payment-dark);
        font-size: 1.55rem;
        font-weight: 750;
        letter-spacing: -0.025em;
        margin-bottom: .35rem;
    }

    .payment-page .page-subtitle {
        color: var(--payment-muted);
        font-size: .9rem;
        max-width: 760px;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .payment-card {
        background: #fff;
        border: 1px solid var(--payment-border);
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
        overflow: hidden;
    }

    .payment-card-header {
        background: #fff;
        border-bottom: 1px solid var(--payment-border);
        padding: 1rem 1.25rem;
    }

    .payment-card-header.soft {
        background: var(--payment-soft);
    }

    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        flex-shrink: 0;
    }

    .section-icon.teal {
        background: var(--payment-primary-light);
        color: var(--payment-primary);
    }

    .section-title {
        color: var(--payment-dark);
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
    }

    .section-subtitle {
        color: var(--payment-muted);
        font-size: .76rem;
        margin: .15rem 0 0;
    }

    .step-badge {
        font-size: .7rem;
        font-weight: 700;
        padding: .45rem .65rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        white-space: nowrap;
    }

    .form-label {
        color: #1e293b;
        font-size: .84rem;
        font-weight: 650;
        margin-bottom: .45rem;
    }

    .form-control,
    .form-select {
        border-color: #dbe2ea;
        color: #1e293b;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #14b8a6;
        box-shadow: 0 0 0 .2rem rgba(20, 184, 166, .10);
    }

    .invoice-select {
        min-height: 52px;
        font-size: .9rem;
    }

    .form-hint {
        color: #64748b;
        font-size: .75rem;
        line-height: 1.5;
    }

    .invoice-context {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        padding: 1rem;
        display: none;
    }

    .invoice-context.active {
        display: block;
    }

    .context-label {
        color: #64748b;
        font-size: .67rem;
        font-weight: 750;
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-bottom: .2rem;
    }

    .context-value {
        color: #0f172a;
        font-size: .88rem;
        font-weight: 650;
    }

    .balance-panel {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 12px;
        padding: .8rem 1rem;
    }

    .balance-panel .balance-label {
        color: #9a3412;
        font-size: .67rem;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .balance-panel .balance-value {
        color: #c2410c;
        font-size: 1.15rem;
        font-weight: 750;
        margin-top: .15rem;
    }

    .amount-wrapper {
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .amount-wrapper:focus-within {
        border-color: #14b8a6;
        box-shadow: 0 0 0 .2rem rgba(20, 184, 166, .10);
    }

    .amount-wrapper .currency-addon {
        min-width: 68px;
        justify-content: center;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        color: #0f172a;
        font-weight: 750;
        font-size: .9rem;
    }

    .amount-wrapper input {
        border: 0 !important;
        box-shadow: none !important;
        min-height: 52px;
        font-size: 1.15rem;
        font-weight: 700;
    }

    .method-option {
        position: relative;
    }

    .method-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .method-label {
        display: flex;
        align-items: center;
        gap: .75rem;
        border: 1px solid #dbe2ea;
        border-radius: 11px;
        padding: .72rem .8rem;
        cursor: pointer;
        background: #fff;
        transition: all .15s ease;
        min-height: 62px;
    }

    .method-label:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .method-option input:checked + .method-label {
        border-color: #14b8a6;
        background: #f0fdfa;
        box-shadow: 0 0 0 2px rgba(20, 184, 166, .08);
    }

    .method-icon {
        width: 35px;
        height: 35px;
        border-radius: 9px;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .method-name {
        color: #1e293b;
        font-size: .8rem;
        font-weight: 700;
    }

    .method-description {
        color: #64748b;
        font-size: .68rem;
        margin-top: .1rem;
    }

    .summary-card {
        position: sticky;
        top: 1rem;
    }

    .summary-header {
        background: #0f172a;
        color: #fff;
        padding: 1rem 1.15rem;
    }

    .summary-body {
        padding: 1.15rem;
    }

    .summary-empty {
        text-align: center;
        padding: 1.5rem .5rem;
        color: #94a3b8;
    }

    .summary-amount {
        background: #f0fdfa;
        border: 1px solid #ccfbf1;
        border-radius: 13px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .summary-amount-label {
        color: #64748b;
        font-size: .68rem;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .summary-amount-value {
        color: #0f766e;
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -.02em;
        margin-top: .2rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .72rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-row:last-child {
        border-bottom: 0;
    }

    .summary-row-label {
        color: #64748b;
        font-size: .73rem;
    }

    .summary-row-value {
        color: #1e293b;
        font-size: .78rem;
        font-weight: 650;
        text-align: right;
        max-width: 65%;
    }

    .currency-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: .35rem .65rem;
        font-size: .72rem;
        font-weight: 750;
    }

    .allocation-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 11px;
        padding: .75rem .85rem;
        font-size: .75rem;
        line-height: 1.5;
    }

    .action-card {
        padding: 1rem;
    }

    .record-button {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        border: none;
        min-height: 52px;
        font-weight: 750;
    }

    .record-button:hover {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    }

    .mpesa-card {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        border-radius: 16px;
        color: #fff;
        box-shadow: 0 10px 25px rgba(6, 95, 70, .18);
        overflow: hidden;
    }

    .mpesa-header {
        padding: 1.15rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,.14);
    }

    .mpesa-body {
        padding: 1.25rem;
    }

    .mpesa-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fff;
        color: #047857;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .mpesa-title {
        color: #fff;
        font-size: 1rem;
        font-weight: 750;
        margin: 0;
    }

    .mpesa-subtitle {
        color: rgba(255,255,255,.72);
        font-size: .75rem;
        margin: .2rem 0 0;
        line-height: 1.5;
    }

    .mpesa-badge {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        color: #fff;
        border-radius: 999px;
        padding: .4rem .7rem;
        font-size: .68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .mpesa-card .form-control {
        min-height: 50px;
        border: 0;
    }

    .mpesa-card .form-label {
        color: #fff;
    }

    .mpesa-help {
        color: rgba(255,255,255,.65);
        font-size: .68rem;
        margin-top: .35rem;
    }

    .mpesa-button {
        min-height: 50px;
        background: #f59e0b;
        border: none;
        color: #172033;
        font-weight: 800;
    }

    .mpesa-button:hover {
        background: #fbbf24;
        color: #172033;
    }

    .alert {
        border-radius: 12px;
    }

    @media (max-width: 991.98px) {
        .summary-card {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .payment-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .page-title {
            font-size: 1.3rem !important;
        }

        .payment-card-header,
        .summary-header,
        .summary-body,
        .mpesa-header,
        .mpesa-body {
            padding: 1rem;
        }

        .method-label {
            min-height: 56px;
        }

        .mpesa-badge {
            display: none;
        }
    }
</style>

@php
    $supportedCurrencies = \App\Support\Currency::SUPPORTED;
@endphp

<div class="container-fluid px-4 py-3 payment-page">

    {{-- Page Header --}}
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-cash-coin text-success me-2"></i>
                Record New Payment
            </h1>

            <p class="page-subtitle">
                Record a tenant payment against an outstanding invoice.
                The payment currency is inherited automatically from the selected invoice.
            </p>
        </div>

        <a href="{{ route('payments.index') }}"
           class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-arrow-left me-1"></i>
            Payments Log
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                Please review the payment details.
            </div>

            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('payments.store') }}"
          id="manual-payment-form">
        @csrf

        <div class="row g-4">

            {{-- =========================================================
                 LEFT: PAYMENT FORM
            ========================================================== --}}
            <div class="col-lg-8">

                {{-- Invoice Selection --}}
                <div class="payment-card mb-4">

                    <div class="payment-card-header soft">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="section-icon">
                                    <i class="bi bi-receipt"></i>
                                </span>

                                <div>
                                    <h2 class="section-title">
                                        Select Invoice
                                    </h2>

                                    <p class="section-subtitle">
                                        Choose the invoice this payment should be applied to.
                                    </p>
                                </div>
                            </div>

                            <span class="step-badge">
                                Step 1
                            </span>
                        </div>
                    </div>

                    <div class="p-4">

                        <label for="rent_invoice_id" class="form-label">
                            Outstanding Invoice
                            <span class="text-danger">*</span>
                        </label>

                        <select name="rent_invoice_id"
                                id="rent_invoice_id"
                                class="form-select invoice-select rounded-3 @error('rent_invoice_id') is-invalid @enderror"
                                required>

                            <option value="">
                                — Select an outstanding invoice —
                            </option>

                            @forelse ($rentInvoices as $invoice)

                                @php
                                    $tenant = $invoice->lease?->tenant;
                                    $unit = $invoice->lease?->unit;
                                    $property = $unit?->property;

                                    $invoiceCurrency = strtoupper(
                                        trim((string) ($invoice->currency ?? ''))
                                    );

                                    /*
                                     * Do not silently assign USD.
                                     * A missing/unsupported currency is represented
                                     * as empty and is not offered as a valid payment target.
                                     */
                                    $invoiceCurrencyIsSupported =
                                        in_array($invoiceCurrency, $supportedCurrencies, true);

                                    $invoiceSymbol = $invoiceCurrencyIsSupported
                                        ? \App\Support\Currency::symbol($invoiceCurrency)
                                        : '';

                                    $outstanding = round(
                                        (float) $invoice->outstanding_amount,
                                        2
                                    );
                                @endphp

                                @if ($invoiceCurrencyIsSupported && $outstanding > 0)
                                    <option
                                        value="{{ $invoice->id }}"
                                        data-tenant-name="{{ $tenant?->full_name }}"
                                        data-phone="{{ $tenant?->phone }}"
                                        data-currency="{{ $invoiceCurrency }}"
                                        data-symbol="{{ $invoiceSymbol }}"
                                        data-outstanding="{{ $outstanding }}"
                                        data-invoice-number="{{ $invoice->invoice_number }}"
                                        data-property="{{ $property?->name }}"
                                        data-unit="{{ $unit?->unit_number }}"
                                        data-billing-period="{{ $invoice->formatted_billing_period }}"
                                        {{ old('rent_invoice_id') == $invoice->id ? 'selected' : '' }}
                                    >
                                        {{ $invoice->invoice_number }}
                                        — {{ $tenant?->full_name ?? 'Unknown tenant' }}
                                        — {{ $property?->name ?? 'Unknown property' }}
                                        / Unit {{ $unit?->unit_number ?? 'N/A' }}
                                        — {{ $invoice->formatted_billing_period }}
                                        — {{ $invoice->formatted_outstanding }}
                                    </option>
                                @endif

                            @empty
                                <option value="" disabled>
                                    No outstanding invoices available
                                </option>
                            @endforelse

                        </select>

                        @error('rent_invoice_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-hint mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            The payment currency is inherited from the invoice.
                            You cannot change the currency from this screen.
                        </div>

                        {{-- Selected Invoice Context --}}
                        <div id="invoice-context" class="invoice-context mt-4">

                            <div class="row g-3">

                                <div class="col-md-7">
                                    <div class="context-label">
                                        Tenant
                                    </div>

                                    <div class="context-value"
                                         id="tenant-name">
                                        —
                                    </div>

                                    <div class="small text-muted mt-1"
                                         id="tenant-phone">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="balance-panel">
                                        <div class="balance-label">
                                            Outstanding Balance
                                        </div>

                                        <div class="balance-value"
                                             id="tenant-outstanding">
                                            —
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="context-label">
                                        Invoice
                                    </div>

                                    <div class="context-value"
                                         id="context-invoice">
                                        —
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="context-label">
                                        Property / Unit
                                    </div>

                                    <div class="context-value"
                                         id="context-property">
                                        —
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="context-label">
                                        Billing Period
                                    </div>

                                    <div class="context-value"
                                         id="context-period">
                                        —
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="payment-card mb-4">

                    <div class="payment-card-header">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="section-icon teal">
                                    <i class="bi bi-wallet2"></i>
                                </span>

                                <div>
                                    <h2 class="section-title">
                                        Payment Details
                                    </h2>

                                    <p class="section-subtitle">
                                        Enter the amount, date and payment channel.
                                    </p>
                                </div>
                            </div>

                            <span class="step-badge">
                                Step 2
                            </span>
                        </div>
                    </div>

                    <div class="p-4">

                        {{-- Amount + Date --}}
                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label for="amount" class="form-label">
                                    Payment Amount
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="amount-wrapper d-flex">

                                    <span class="currency-addon d-flex align-items-center"
                                          id="currency-symbol">
                                        —
                                    </span>

                                    <input type="number"
                                           name="amount"
                                           id="amount"
                                           class="form-control"
                                           value="{{ old('amount') }}"
                                           min="0.01"
                                           step="0.01"
                                           placeholder="0.00"
                                           required>

                                </div>

                                @error('amount')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div id="amount-help" class="form-hint mt-2">
                                    Select an invoice to see its outstanding balance.
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="payment_date" class="form-label">
                                    Payment Date
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="payment_date"
                                       id="payment_date"
                                       class="form-control rounded-3"
                                       value="{{ old('payment_date', now()->toDateString()) }}"
                                       required>

                                @error('payment_date')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-4">

                            <label class="form-label">
                                Payment Channel
                                <span class="text-danger">*</span>
                            </label>

                            <div class="row g-2">

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_cash"
                                               value="{{ \App\Models\Payment::METHOD_CASH }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_CASH ? 'checked' : '' }}>

                                        <label for="method_cash"
                                               class="method-label">
                                            <span class="method-icon">💵</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    Cash
                                                </span>
                                                <span class="method-description d-block">
                                                    Physical cash received
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_bank"
                                               value="{{ \App\Models\Payment::METHOD_BANK_TRANSFER }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_BANK_TRANSFER ? 'checked' : '' }}>

                                        <label for="method_bank"
                                               class="method-label">
                                            <span class="method-icon">🏦</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    Bank Transfer
                                                </span>
                                                <span class="method-description d-block">
                                                    Direct bank payment
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_mpesa"
                                               value="{{ \App\Models\Payment::METHOD_MPESA }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_MPESA ? 'checked' : '' }}>

                                        <label for="method_mpesa"
                                               class="method-label">
                                            <span class="method-icon">📱</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    M-Pesa
                                                </span>
                                                <span class="method-description d-block">
                                                    Manual M-Pesa payment
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_airtel"
                                               value="{{ \App\Models\Payment::METHOD_AIRTEL_MONEY }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_AIRTEL_MONEY ? 'checked' : '' }}>

                                        <label for="method_airtel"
                                               class="method-label">
                                            <span class="method-icon">🔴</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    Airtel Money
                                                </span>
                                                <span class="method-description d-block">
                                                    Mobile money payment
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_card"
                                               value="{{ \App\Models\Payment::METHOD_CARD }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_CARD ? 'checked' : '' }}>

                                        <label for="method_card"
                                               class="method-label">
                                            <span class="method-icon">💳</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    Card
                                                </span>
                                                <span class="method-description d-block">
                                                    Credit or debit card
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="method-option">
                                        <input type="radio"
                                               name="payment_method"
                                               id="method_other"
                                               value="{{ \App\Models\Payment::METHOD_OTHER }}"
                                               {{ old('payment_method') === \App\Models\Payment::METHOD_OTHER ? 'checked' : '' }}>

                                        <label for="method_other"
                                               class="method-label">
                                            <span class="method-icon">📝</span>

                                            <span>
                                                <span class="method-name d-block">
                                                    Other
                                                </span>
                                                <span class="method-description d-block">
                                                    Other payment channel
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            @error('payment_method')
                                <div class="text-danger small mt-2">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Reference --}}
                        <div class="mb-4">

                            <label for="transaction_reference"
                                   class="form-label">
                                Transaction / Cheque Reference
                            </label>

                            <input type="text"
                                   name="transaction_reference"
                                   id="transaction_reference"
                                   class="form-control rounded-3"
                                   value="{{ old('transaction_reference') }}"
                                   maxlength="255"
                                   placeholder="e.g. QHX89234JK or Bank Slip #">

                            @error('transaction_reference')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-hint mt-2">
                                Optional reference used for audit and payment tracking.
                            </div>

                        </div>

                        {{-- Notes --}}
                        <div>

                            <label for="notes"
                                   class="form-label">
                                Administrative Notes
                            </label>

                            <textarea name="notes"
                                      id="notes"
                                      rows="3"
                                      class="form-control rounded-3"
                                      placeholder="Optional remarks about this payment...">{{ old('notes') }}</textarea>

                            @error('notes')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>
                </div>

            </div>

            {{-- =========================================================
                 RIGHT: PAYMENT SUMMARY
            ========================================================== --}}
            <div class="col-lg-4">

                <div class="summary-card">

                    <div class="payment-card mb-3">

                        <div class="summary-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-info"></i>

                                <div>
                                    <div class="fw-bold">
                                        Payment Summary
                                    </div>

                                    <div class="small text-white-50">
                                        Review before recording
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-body">

                            <div id="summary-empty"
                                 class="summary-empty">

                                <i class="bi bi-receipt-cutoff fs-2 d-block mb-2"></i>

                                <div class="fw-semibold">
                                    No invoice selected
                                </div>

                                <div class="small mt-1">
                                    Select an invoice to preview the payment.
                                </div>

                            </div>

                            <div id="summary-content" class="d-none">

                                <div class="summary-amount">

                                    <div class="summary-amount-label">
                                        Payment Amount
                                    </div>

                                    <div id="preview-payment"
                                         class="summary-amount-value">
                                        —
                                    </div>

                                </div>

                                <div class="summary-row">
                                    <span class="summary-row-label">
                                        Invoice
                                    </span>

                                    <span id="preview-invoice"
                                          class="summary-row-value">
                                        —
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-row-label">
                                        Tenant
                                    </span>

                                    <span id="preview-tenant"
                                          class="summary-row-value">
                                        —
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-row-label">
                                        Currency
                                    </span>

                                    <span id="preview-currency"
                                          class="summary-row-value">
                                        —
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-row-label">
                                        Outstanding
                                    </span>

                                    <span id="preview-outstanding"
                                          class="summary-row-value text-danger">
                                        —
                                    </span>
                                </div>

                                <div id="allocation-warning"
                                     class="allocation-warning mt-3 d-none">

                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>

                                    <strong>Overpayment:</strong>
                                    the amount exceeds this invoice's outstanding
                                    balance. The surplus will be allocated to other
                                    eligible invoices in the same currency.

                                </div>

                                <div class="border-top mt-3 pt-3">

                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle text-primary mt-1"></i>

                                        <div class="form-hint">
                                            Payment currency is inherited from the
                                            selected invoice and cannot be changed here.
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="payment-card action-card">

                        <button type="submit"
                                class="btn btn-primary record-button w-100 rounded-3 shadow-sm"
                                id="record-payment-button">

                            <i class="bi bi-check-circle-fill me-2"></i>
                            Save & Record Payment

                        </button>

                        <a href="{{ route('payments.index') }}"
                           class="btn btn-light border w-100 rounded-3 text-muted mt-2">

                            <i class="bi bi-x-lg me-1"></i>
                            Cancel

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

    {{-- ================================================================
         M-PESA STK PUSH
    ================================================================= --}}
    <div id="mpesa-card"
         class="mpesa-card mt-4 d-none">

        <div class="mpesa-header">

            <div class="d-flex align-items-center justify-content-between gap-3">

                <div class="d-flex align-items-center gap-3">

                    <div class="mpesa-icon">
                        <i class="bi bi-phone-vibrate fs-4"></i>
                    </div>

                    <div>
                        <h3 class="mpesa-title">
                            M-Pesa Express STK Push
                        </h3>

                        <p class="mpesa-subtitle">
                            Available only when the selected invoice is denominated in KES.
                            Send a payment prompt directly to the tenant's phone.
                        </p>
                    </div>

                </div>

                <span class="mpesa-badge">
                    KES ONLY
                </span>

            </div>

        </div>

        <div class="mpesa-body">

            <form method="POST"
                  action=""
                  id="mpesa-form">
                @csrf

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">

                        <label for="mpesa_amount"
                               class="form-label">
                            Amount (KES)
                            <span class="text-warning">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-white border-0 fw-bold text-success">
                                KSh
                            </span>

                            <input type="number"
                                   name="amount"
                                   id="mpesa_amount"
                                   class="form-control rounded-end fs-5 fw-bold"
                                   min="1"
                                   step="1"
                                   placeholder="1000"
                                   required>

                        </div>

                        <div id="mpesa-amount-help"
                             class="mpesa-help">
                            Whole number without decimals.
                        </div>

                    </div>

                    <div class="col-md-4">

                        <label for="mpesa_phone"
                               class="form-label">
                            Tenant Phone Number
                            <span class="text-warning">*</span>
                        </label>

                        <input type="text"
                               name="phone"
                               id="mpesa_phone"
                               class="form-control fs-5 rounded-3 fw-medium"
                               maxlength="30"
                               placeholder="0712345678 or 254712345678"
                               required>

                        <div class="mpesa-help">
                            Example: 0712345678 or 254712345678
                        </div>

                    </div>

                    <div class="col-md-4">

                        <button type="submit"
                                class="btn mpesa-button w-100 rounded-3 shadow"
                                id="mpesa-button">

                            <i class="bi bi-send-fill me-2"></i>
                            Send STK Prompt

                        </button>

                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const invoiceSelect = document.getElementById('rent_invoice_id');
    const amountInput = document.getElementById('amount');

    const invoiceContext = document.getElementById('invoice-context');

    const tenantName = document.getElementById('tenant-name');
    const tenantPhone = document.getElementById('tenant-phone');
    const tenantOutstanding = document.getElementById('tenant-outstanding');

    const contextInvoice = document.getElementById('context-invoice');
    const contextProperty = document.getElementById('context-property');
    const contextPeriod = document.getElementById('context-period');

    const currencySymbol = document.getElementById('currency-symbol');
    const amountHelp = document.getElementById('amount-help');

    const summaryEmpty = document.getElementById('summary-empty');
    const summaryContent = document.getElementById('summary-content');

    const previewPayment = document.getElementById('preview-payment');
    const previewInvoice = document.getElementById('preview-invoice');
    const previewTenant = document.getElementById('preview-tenant');
    const previewCurrency = document.getElementById('preview-currency');
    const previewOutstanding = document.getElementById('preview-outstanding');

    const allocationWarning = document.getElementById('allocation-warning');

    const mpesaCard = document.getElementById('mpesa-card');
    const mpesaForm = document.getElementById('mpesa-form');
    const mpesaAmount = document.getElementById('mpesa_amount');
    const mpesaPhone = document.getElementById('mpesa_phone');
    const mpesaAmountHelp = document.getElementById('mpesa-amount-help');
    const mpesaButton = document.getElementById('mpesa-button');

    const recordPaymentButton =
        document.getElementById('record-payment-button');

    const mpesaRouteTemplate = @json(
        route('rent-invoices.payments.mpesa', [
            'rentInvoice' => '__INVOICE__'
        ])
    );

    const currencySymbols = {
        USD: '$',
        KES: 'KSh',
        CDF: 'FC'
    };

    function getCurrencySymbol(currency) {
        const code = String(currency || '').trim().toUpperCase();

        if (!code) {
            return '—';
        }

        return currencySymbols[code] || code;
    }

    function formatAmount(amount, currency) {
        const value = Number(amount || 0);
        const code = String(currency || '').trim().toUpperCase();

        if (!code) {
            return value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        const symbol = getCurrencySymbol(code);

        return symbol + ' ' +
            value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) +
            ' ' +
            code;
    }

    function getSelectedOption() {
        if (!invoiceSelect || invoiceSelect.selectedIndex < 0) {
            return null;
        }

        const option =
            invoiceSelect.options[invoiceSelect.selectedIndex];

        if (!option || !option.value) {
            return null;
        }

        return option;
    }

    function resetMpesa() {
        mpesaCard.classList.add('d-none');

        mpesaForm.action = '';

        mpesaAmount.value = '';
        mpesaPhone.value = '';

        mpesaAmountHelp.textContent =
            'Whole number without decimals.';
    }

    function resetSummary() {
        invoiceContext.classList.remove('active');

        tenantName.textContent = '—';
        tenantPhone.textContent = '';

        tenantOutstanding.textContent = '—';

        contextInvoice.textContent = '—';
        contextProperty.textContent = '—';
        contextPeriod.textContent = '—';

        currencySymbol.textContent = '—';

        amountHelp.textContent =
            'Select an invoice to see its outstanding balance.';

        summaryEmpty.classList.remove('d-none');
        summaryContent.classList.add('d-none');

        previewPayment.textContent = '—';
        previewInvoice.textContent = '—';
        previewTenant.textContent = '—';
        previewCurrency.textContent = '—';
        previewOutstanding.textContent = '—';

        allocationWarning.classList.add('d-none');

        resetMpesa();
    }

    function updatePaymentPreview() {

        const selectedOption = getSelectedOption();

        if (!selectedOption) {
            previewPayment.textContent = '—';
            allocationWarning.classList.add('d-none');
            return;
        }

        const currency =
            String(selectedOption.dataset.currency || '')
                .toUpperCase();

        const outstanding =
            Number(selectedOption.dataset.outstanding || 0);

        const amount =
            Number(amountInput.value || 0);

        previewPayment.textContent =
            amount > 0
                ? formatAmount(amount, currency)
                : '—';

        if (amount > outstanding && outstanding > 0) {
            allocationWarning.classList.remove('d-none');
        } else {
            allocationWarning.classList.add('d-none');
        }
    }

    function updatePreview() {

        const selectedOption = getSelectedOption();

        if (!selectedOption) {
            resetSummary();
            return;
        }

        const tenant =
            selectedOption.dataset.tenantName || 'Unknown tenant';

        const phone =
            selectedOption.dataset.phone || '';

        const currency =
            String(selectedOption.dataset.currency || '')
                .toUpperCase();

        const symbol =
            selectedOption.dataset.symbol ||
            getCurrencySymbol(currency);

        const outstanding =
            Number(selectedOption.dataset.outstanding || 0);

        const invoiceNumber =
            selectedOption.dataset.invoiceNumber || '—';

        const property =
            selectedOption.dataset.property || '';

        const unit =
            selectedOption.dataset.unit || '';

        const billingPeriod =
            selectedOption.dataset.billingPeriod || '';

        invoiceContext.classList.add('active');

        tenantName.textContent = tenant;

        tenantPhone.textContent =
            phone
                ? phone
                : 'Phone number not available';

        tenantOutstanding.textContent =
            formatAmount(outstanding, currency);

        contextInvoice.textContent =
            invoiceNumber;

        contextProperty.textContent =
            property
                ? property + (unit ? ' / Unit ' + unit : '')
                : (unit ? 'Unit ' + unit : '—');

        contextPeriod.textContent =
            billingPeriod || '—';

        currencySymbol.textContent = symbol;

        amountHelp.textContent =
            'Outstanding balance: ' +
            formatAmount(outstanding, currency);

        summaryEmpty.classList.add('d-none');
        summaryContent.classList.remove('d-none');

        previewInvoice.textContent =
            invoiceNumber;

        previewTenant.textContent =
            tenant;

        previewCurrency.textContent =
            currency
                ? symbol + ' — ' + currency
                : '—';

        previewOutstanding.textContent =
            formatAmount(outstanding, currency);

        updatePaymentPreview();

        /*
         * M-Pesa STK Push is available only for KES invoices.
         */
        if (currency === 'KES' && outstanding > 0) {

            mpesaCard.classList.remove('d-none');

            mpesaForm.action =
                mpesaRouteTemplate.replace(
                    '__INVOICE__',
                    selectedOption.value
                );

            if (
                !mpesaAmount.value ||
                Number(mpesaAmount.value) <= 0
            ) {
                const wholeOutstanding =
                    Math.floor(outstanding);

                if (wholeOutstanding > 0) {
                    mpesaAmount.value =
                        wholeOutstanding;
                }
            }

            if (phone) {
                mpesaPhone.value = phone;
            }

            mpesaAmountHelp.textContent =
                'Outstanding balance: ' +
                formatAmount(outstanding, 'KES') +
                '. Amount must be a whole number.';

        } else {
            resetMpesa();
        }
    }

    /*
     * Changing invoices always resets the payment amount.
     * This prevents the amount from one invoice accidentally
     * being submitted against another invoice.
     */
    invoiceSelect.addEventListener('change', function () {

        const selectedOption =
            getSelectedOption();

        if (selectedOption) {

            const outstanding =
                Number(selectedOption.dataset.outstanding || 0);

            amountInput.value =
                outstanding > 0
                    ? outstanding.toFixed(2)
                    : '';

        } else {

            amountInput.value = '';
        }

        mpesaAmount.value = '';
        mpesaPhone.value = '';

        updatePreview();
    });

    amountInput.addEventListener('input', function () {

        updatePaymentPreview();

        if (!mpesaCard.classList.contains('d-none')) {

            const value =
                Number(amountInput.value || 0);

            if (
                value > 0 &&
                Number.isInteger(value)
            ) {
                mpesaAmount.value = value;
            }
        }
    });

    /*
     * Keep M-Pesa amount synchronized when the manual
     * payment amount changes.
     */
    mpesaAmount.addEventListener('input', function () {

        const value =
            Number(mpesaAmount.value || 0);

        if (
            value > 0 &&
            Number.isInteger(value)
        ) {
            amountInput.value = value.toFixed(2);
            updatePaymentPreview();
        }
    });

    /*
     * Prevent double submission of the manual payment.
     */
    document.getElementById('manual-payment-form')
        .addEventListener('submit', function () {

            if (recordPaymentButton.disabled) {
                return;
            }

            recordPaymentButton.disabled = true;

            recordPaymentButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                'Recording Payment...';
        });

    /*
     * Validate M-Pesa STK Push.
     */
    mpesaForm.addEventListener('submit', function (event) {

        const selectedOption =
            getSelectedOption();

        if (!selectedOption) {

            event.preventDefault();

            alert(
                'Please select a KES invoice before sending the M-Pesa STK Push.'
            );

            return;
        }

        const currency =
            String(selectedOption.dataset.currency || '')
                .toUpperCase();

        if (currency !== 'KES') {

            event.preventDefault();

            alert(
                'M-Pesa STK Push is only available for KES invoices.'
            );

            return;
        }

        const amount =
            Number(mpesaAmount.value || 0);

        if (
            amount <= 0 ||
            !Number.isInteger(amount)
        ) {

            event.preventDefault();

            alert(
                'M-Pesa payment amount must be a whole number greater than zero.'
            );

            return;
        }

        if (
            !mpesaForm.action ||
            mpesaForm.action.includes('__INVOICE__')
        ) {

            event.preventDefault();

            alert(
                'Please select an invoice before sending the M-Pesa STK Push.'
            );

            return;
        }

        if (!mpesaPhone.value.trim()) {

            event.preventDefault();

            alert(
                'Please enter the M-Pesa phone number.'
            );

            return;
        }

        mpesaButton.disabled = true;

        mpesaButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
            'Sending STK Push...';
    });

    /*
     * Restore the selected invoice after validation errors.
     */
    updatePreview();

});
</script>

@endpush