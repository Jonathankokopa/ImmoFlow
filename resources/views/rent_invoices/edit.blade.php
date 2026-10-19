@extends('layouts.app')

@section('title', 'Edit Rent Invoice')
@section('page-heading', 'Edit Rent Invoice')

@php
use App\Support\Currency;
use App\Models\RentInvoice;

$currency = strtoupper((string) ($rentInvoice->currency ?? ''));
$isSupportedCurrency = in_array($currency, Currency::SUPPORTED, true);

$currencySymbol = $isSupportedCurrency
    ? Currency::symbol($currency)
    : $currency;

$currencyName = $isSupportedCurrency
    ? Currency::name($currency)
    : 'Currency not configured';

$invoiceAmount = (float) ($rentInvoice->amount ?? 0);
$amountPaid = (float) $rentInvoice->getTotalPaid();
$currentBalance = (float) ($rentInvoice->outstanding_amount ?? 0);

$hasPayments = $rentInvoice->hasPayments();
$isCancelled = $rentInvoice->status === RentInvoice::STATUS_CANCELLED;

@endphp

@push('styles')

<style>
    .invoice-edit-page {
        max-width: 1440px;
        margin: 0 auto;
    }

    .invoice-edit-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 28px 30px;
        color: #fff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .10);
    }

    .invoice-edit-eyebrow {
        color: #5eead4;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .11em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .invoice-edit-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    }

    .invoice-edit-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #fbfdff;
    }

    .invoice-edit-card-header h5 {
        color: #0f172a;
        font-weight: 750;
    }

    .invoice-edit-subtitle {
        color: #64748b;
        font-size: .84rem;
    }

    .invoice-edit-card-body {
        padding: 24px;
    }

    .invoice-edit-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.05rem;
    }

    .invoice-edit-label {
        font-size: .82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 7px;
    }

    .invoice-edit-page .form-control {
        border-color: #dbe2ea;
        border-radius: 10px;
        min-height: 44px;
    }

    .invoice-edit-page textarea.form-control {
        min-height: 120px;
    }

    .invoice-edit-page .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .10);
    }

    .invoice-readonly-box {
        min-height: 44px;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        padding: 9px 12px;
    }

    .invoice-summary-card {
        height: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        background: #f8fafc;
    }

    .invoice-summary-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
    }

    .invoice-notice {
        border-radius: 14px;
    }

    .invoice-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: .78rem;
        font-weight: 750;
    }

    .invoice-status-paid {
        background: #dcfce7;
        color: #166534;
    }

    .invoice-status-partial {
        background: #fef3c7;
        color: #92400e;
    }

    .invoice-status-overdue {
        background: #fee2e2;
        color: #991b1b;
    }

    .invoice-status-cancelled {
        background: #e2e8f0;
        color: #475569;
    }

    .invoice-status-unpaid {
        background: #e0e7ff;
        color: #3730a3;
    }

    .invoice-edit-actions {
        position: sticky;
        bottom: 16px;
        z-index: 20;
        background: rgba(255,255,255,.94);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .10);
    }

    .invoice-primary-btn {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .invoice-primary-btn:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .invoice-edit-header {
            padding: 22px 20px;
            border-radius: 16px;
        }

        .invoice-edit-card-body {
            padding: 18px;
        }

        .invoice-edit-card-header {
            padding: 18px;
        }

        .invoice-edit-actions {
            position: static;
            margin-bottom: 20px;
        }
    }

    html[data-theme="dark"] .invoice-edit-header {
        background: linear-gradient(135deg, #020617, #172033);
    }

    html[data-theme="dark"] .invoice-edit-card,
    html[data-theme="dark"] .invoice-edit-card-header {
        background: #111827;
        border-color: #263244;
    }

    html[data-theme="dark"] .invoice-edit-card-header {
        border-bottom-color: #263244;
    }

    html[data-theme="dark"] .invoice-edit-card-header h5,
    html[data-theme="dark"] .invoice-edit-label,
    html[data-theme="dark"] .invoice-summary-value {
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-edit-subtitle,
    html[data-theme="dark"] .form-text {
        color: #94a3b8 !important;
    }

    html[data-theme="dark"] .invoice-readonly-box,
    html[data-theme="dark"] .invoice-summary-card {
        background: #0f172a;
        border-color: #263244;
    }

    html[data-theme="dark"] .invoice-edit-page .form-control {
        background: #111827;
        border-color: #334155;
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-edit-page .form-control::placeholder {
        color: #64748b;
    }

    html[data-theme="dark"] .invoice-edit-actions {
        background: rgba(17,24,39,.94);
        border-color: #263244;
    }
</style>

@endpush

@section('content')

<div class="container-fluid px-3 px-lg-4 py-3 invoice-edit-page">

{{-- Page Header --}}
<div class="invoice-edit-header mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <div class="invoice-edit-eyebrow">
                <i class="bi bi-receipt me-1"></i>
                Billing &amp; Invoicing
            </div>

            <h1 class="h3 fw-bold mb-2">
                Edit Invoice
                <span class="text-white-50">
                    #{{ $rentInvoice->invoice_number }}
                </span>
            </h1>

            <p class="mb-0 text-white-50">
                Update invoice information while preserving its recorded
                financial values and currency.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a
                href="{{ route('rent-invoices.show', $rentInvoice) }}"
                class="btn btn-light rounded-3 px-3 fw-semibold"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Invoice
            </a>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger invoice-notice mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>

            <div>
                <div class="fw-bold mb-2">
                    Please correct the following errors:
                </div>

                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

{{-- Cancelled Warning --}}
@if($isCancelled)
    <div class="alert alert-danger invoice-notice mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-x-octagon-fill fs-4"></i>

            <div>
                <strong>This invoice is cancelled.</strong>

                <div class="mt-1">
                    Cancelled invoices cannot be edited.
                </div>
            </div>
        </div>
    </div>
@endif

<form
    action="{{ route('rent-invoices.update', $rentInvoice) }}"
    method="POST"
    id="edit-invoice-form"
>
    @csrf
    @method('PUT')

    {{-- Invoice Information --}}
    <div class="invoice-edit-card mb-4">
        <div class="invoice-edit-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-edit-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Invoice Information
                    </h5>

                    <div class="invoice-edit-subtitle">
                        Update the invoice number and billing schedule.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-edit-card-body">
            <div class="row g-4">

                {{-- Lease --}}
                <div class="col-lg-6">
                    <label class="invoice-edit-label">
                        Lease Agreement
                    </label>

                    <div class="invoice-readonly-box">
                        <div>
                            <div class="fw-semibold">
                                {{ $rentInvoice->lease?->tenant?->full_name
                                    ?? $rentInvoice->lease?->tenant?->name
                                    ?? 'Unknown Tenant' }}
                            </div>

                            <div class="text-muted small mt-1">
                                {{ $rentInvoice->lease?->unit?->property?->name
                                    ?? 'Unknown Property' }}

                                &mdash;

                                Unit
                                {{ $rentInvoice->lease?->unit?->unit_number
                                    ?? $rentInvoice->lease?->unit?->name
                                    ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="form-text">
                        The lease attached to an invoice cannot be changed.
                    </div>
                </div>

                {{-- Invoice Number --}}
                <div class="col-lg-6">
                    <label
                        for="invoice_number"
                        class="invoice-edit-label"
                    >
                        Invoice Number
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="invoice_number"
                        id="invoice_number"
                        class="form-control @error('invoice_number') is-invalid @enderror"
                        value="{{ old('invoice_number', $rentInvoice->invoice_number) }}"
                        maxlength="255"
                        required
                        @disabled($isCancelled)
                    >

                    @error('invoice_number')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Billing Period --}}
                <div class="col-lg-4">
                    <label
                        for="billing_period"
                        class="invoice-edit-label"
                    >
                        Billing Period
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="month"
                        name="billing_period"
                        id="billing_period"
                        class="form-control @error('billing_period') is-invalid @enderror"
                        value="{{ old(
                            'billing_period',
                            $rentInvoice->billing_period?->format('Y-m')
                        ) }}"
                        required
                        @readonly($hasPayments || $isCancelled)
                    >

                    @error('billing_period')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    @if($hasPayments)
                        <div class="form-text">
                            Locked because payment activity has already
                            been recorded.
                        </div>
                    @elseif($isCancelled)
                        <div class="form-text text-danger">
                            Cancelled invoices cannot be edited.
                        </div>
                    @else
                        <div class="form-text">
                            Select the month covered by this invoice.
                        </div>
                    @endif
                </div>

                {{-- Invoice Date --}}
                <div class="col-lg-4">
                    <label
                        for="invoice_date"
                        class="invoice-edit-label"
                    >
                        Invoice Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="invoice_date"
                        id="invoice_date"
                        class="form-control @error('invoice_date') is-invalid @enderror"
                        value="{{ old(
                            'invoice_date',
                            $rentInvoice->invoice_date?->format('Y-m-d')
                        ) }}"
                        required
                        @disabled($isCancelled)
                    >

                    @error('invoice_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Due Date --}}
                <div class="col-lg-4">
                    <label
                        for="due_date"
                        class="invoice-edit-label"
                    >
                        Due Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        id="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old(
                            'due_date',
                            $rentInvoice->due_date?->format('Y-m-d')
                        ) }}"
                        required
                        @disabled($isCancelled)
                    >

                    @error('due_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- Financial Details --}}
    <div class="invoice-edit-card mb-4">
        <div class="invoice-edit-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-edit-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Financial Details
                    </h5>

                    <div class="invoice-edit-subtitle">
                        Financial values are fixed on this invoice.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-edit-card-body">

            <div class="row g-4">

                {{-- Amount --}}
                <div class="col-lg-6">
                    <label class="invoice-edit-label">
                        Invoice Amount
                    </label>

                    <div class="invoice-readonly-box">
                        @if($isSupportedCurrency)
                            <span class="fw-bold">
                                {{ Currency::format($invoiceAmount, $currency) }}
                            </span>
                        @else
                            <span class="fw-bold">
                                {{ number_format($invoiceAmount, 2) }}
                            </span>

                            <span class="badge bg-warning-subtle text-warning-emphasis ms-2">
                                Currency not configured
                            </span>
                        @endif
                    </div>

                    <div class="form-text">
                        The recorded invoice amount cannot be edited here.
                    </div>
                </div>

                {{-- Currency --}}
                <div class="col-lg-6">
                    <label class="invoice-edit-label">
                        Invoice Currency
                    </label>

                    <div class="invoice-readonly-box">
                        @if($isSupportedCurrency)
                            <span class="fw-bold">
                                {{ $currencySymbol }} {{ $currency }}
                            </span>

                            <span class="text-muted ms-2">
                                &mdash; {{ $currencyName }}
                            </span>
                        @else
                            <span class="fw-bold text-warning">
                                {{ $currency ?: 'Not configured' }}
                            </span>
                        @endif
                    </div>

                    <div class="form-text">
                        Currency is fixed for this invoice and its payments.
                    </div>
                </div>

            </div>

            {{-- Payment Summary --}}
            <div class="row g-3 mt-1">

                <div class="col-lg-6">
                    <div class="invoice-summary-card">
                        <div class="text-muted small mb-1">
                            Amount Already Paid
                        </div>

                        <div class="invoice-summary-value text-success">
                            @if($isSupportedCurrency)
                                {{ Currency::format($amountPaid, $currency) }}
                            @else
                                {{ number_format($amountPaid, 2) }}
                            @endif
                        </div>

                        <div class="text-muted small mt-1">
                            Calculated from recorded payment activity.
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="invoice-summary-card">
                        <div class="text-muted small mb-1">
                            Current Outstanding Balance
                        </div>

                        <div class="invoice-summary-value {{ $currentBalance > 0 ? 'text-danger' : 'text-success' }}">
                            @if($isSupportedCurrency)
                                {{ Currency::format($currentBalance, $currency) }}
                            @else
                                {{ number_format($currentBalance, 2) }}
                            @endif
                        </div>

                        <div class="text-muted small mt-1">
                            Calculated from the invoice and financial activity.
                        </div>
                    </div>
                </div>

            </div>

            {{-- Financial Activity Warning --}}
            @if($hasPayments)
                <div class="alert alert-warning invoice-notice mt-4 mb-0">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                        <div>
                            <strong>
                                This invoice already has payment activity.
                            </strong>

                            <div class="mt-1">
                                The invoice amount and currency remain fixed.
                                Payment records remain the source of truth
                                for the amount paid.
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info invoice-notice mt-4 mb-0">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle-fill fs-5"></i>

                        <div>
                            <strong>
                                No payment activity recorded.
                            </strong>

                            <div class="mt-1">
                                The invoice amount and currency remain fixed.
                                The billing period can still be corrected
                                while no financial activity exists.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Currency Information --}}
    <div class="invoice-edit-card mb-4">
        <div class="invoice-edit-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-edit-icon">
                    <i class="bi bi-currency-exchange"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Currency Information
                    </h5>

                    <div class="invoice-edit-subtitle">
                        Historical invoice currency is preserved.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-edit-card-body">
            <div class="alert alert-light border invoice-notice mb-0">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-currency-exchange text-primary fs-4"></i>

                    <div>
                        <h6 class="fw-bold mb-1">
                            @if($isSupportedCurrency)
                                {{ $currencySymbol }}
                                {{ $currency }}
                                &mdash;
                                {{ $currencyName }}
                            @else
                                {{ $currency ?: 'Currency not configured' }}
                            @endif
                        </h6>

                        <p class="text-muted mb-2">
                            This invoice retains the currency recorded
                            when it was created.
                        </p>

                        <p class="text-muted small mb-0">
                            Payments recorded against this invoice must use
                            the same currency. No automatic currency
                            conversion is performed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Status --}}
    <div class="invoice-edit-card mb-4">
        <div class="invoice-edit-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-edit-icon">
                    <i class="bi bi-info-circle"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Invoice Status
                    </h5>

                    <div class="invoice-edit-subtitle">
                        Status is synchronized from the invoice's financial activity.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-edit-card-body">
            <div class="d-flex align-items-center gap-3 flex-wrap">

                @switch($rentInvoice->status)

                    @case(RentInvoice::STATUS_PAID)
                        <span class="invoice-status-pill invoice-status-paid">
                            <i class="bi bi-check-circle-fill"></i>
                            Paid
                        </span>
                        @break

                    @case(RentInvoice::STATUS_PARTIALLY_PAID)
                        <span class="invoice-status-pill invoice-status-partial">
                            <i class="bi bi-circle-half"></i>
                            Partially Paid
                        </span>
                        @break

                    @case(RentInvoice::STATUS_OVERDUE)
                        <span class="invoice-status-pill invoice-status-overdue">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            Overdue
                        </span>
                        @break

                    @case(RentInvoice::STATUS_CANCELLED)
                        <span class="invoice-status-pill invoice-status-cancelled">
                            <i class="bi bi-x-circle-fill"></i>
                            Cancelled
                        </span>
                        @break

                    @default
                        <span class="invoice-status-pill invoice-status-unpaid">
                            <i class="bi bi-clock-fill"></i>
                            Unpaid
                        </span>

                @endswitch

                <span class="text-muted small">
                    This value is synchronized from the invoice
                    and financial records.
                </span>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="invoice-edit-card mb-4">
        <div class="invoice-edit-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-edit-icon">
                    <i class="bi bi-journal-text"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Invoice Notes
                    </h5>

                    <div class="invoice-edit-subtitle">
                        Remarks, utility charge breakdowns, or special billing information.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-edit-card-body">
            <label
                for="notes"
                class="invoice-edit-label"
            >
                Notes
            </label>

            <textarea
                name="notes"
                id="notes"
                class="form-control @error('notes') is-invalid @enderror"
                rows="4"
                placeholder="Enter any optional notes regarding this rent invoice..."
                @disabled($isCancelled)
            >{{ old('notes', $rentInvoice->notes) }}</textarea>

            @error('notes')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- Actions --}}
    <div class="invoice-edit-actions d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a
            href="{{ route('rent-invoices.show', $rentInvoice) }}"
            class="btn btn-outline-secondary rounded-3 px-4 fw-semibold"
        >
            <i class="bi bi-x-circle me-1"></i>
            Cancel
        </a>

        @unless($isCancelled)
            <button
                type="submit"
                class="btn invoice-primary-btn px-4"
                id="update-invoice-btn"
            >
                <i class="bi bi-check-circle me-1"></i>
                Update Invoice
            </button>
        @endunless
    </div>

</form>

</div>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('edit-invoice-form');
    const button = document.getElementById('update-invoice-btn');

    if (!form || !button) {
        return;
    }

    form.addEventListener('submit', function () {
        if (button.disabled) {
            return;
        }

        button.disabled = true;

        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-1"
                role="status"
                aria-hidden="true"
            ></span>
            Updating Invoice...
        `;
    });
});
</script>

@endpush
