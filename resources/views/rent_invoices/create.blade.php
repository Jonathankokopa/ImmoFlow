@extends('layouts.app')

@section('title', 'Add Rent Invoice')

@section('page-heading', 'Add Rent Invoice')

@php
$leaseData = [];

foreach ($leases as $lease) {
    $currency = strtoupper((string) $lease->currency);

    $isSupportedCurrency = in_array(
        $currency,
        \App\Support\Currency::SUPPORTED,
        true
    );

    $leaseData[$lease->id] = [
        'rent' => number_format(
            (float) $lease->monthly_rent,
            2,
            '.',
            ''
        ),
        'currency' => $currency,
        'symbol' => $isSupportedCurrency
            ? \App\Support\Currency::symbol($currency)
            : '—',
        'supported' => $isSupportedCurrency,
    ];
}

@endphp

@push('styles')

<style>
    .invoice-page {
        max-width: 1440px;
        margin: 0 auto;
    }

    .invoice-page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 28px 30px;
        color: #fff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .10);
    }

    .invoice-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #5eead4;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .11em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .invoice-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    }

    .invoice-section-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #fbfdff;
    }

    .invoice-section-header h5 {
        color: #0f172a;
        font-weight: 750;
    }

    .invoice-section-subtitle {
        color: #64748b;
        font-size: .84rem;
    }

    .invoice-section-body {
        padding: 24px;
    }

    .invoice-icon-chip {
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

    .invoice-info-box {
        border: 1px solid #dbeafe;
        background: #f8fbff;
        border-radius: 14px;
        padding: 16px 18px;
    }

    .invoice-financial-preview {
        border: 1px solid #dbeafe;
        background: linear-gradient(135deg, #f8fbff, #f0fdfa);
        border-radius: 16px;
        padding: 20px;
    }

    .invoice-financial-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
    }

    .invoice-form-label {
        font-size: .82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 7px;
    }

    .invoice-required {
        color: #dc2626;
    }

    .invoice-page .form-control,
    .invoice-page .form-select {
        border-color: #dbe2ea;
        border-radius: 10px;
        min-height: 44px;
    }

    .invoice-page textarea.form-control {
        min-height: 120px;
    }

    .invoice-page .form-control:focus,
    .invoice-page .form-select:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .10);
    }

    .invoice-readonly {
        background: #f8fafc !important;
        color: #334155;
        cursor: not-allowed;
    }

    .invoice-action-bar {
        position: sticky;
        bottom: 16px;
        z-index: 20;
        background: rgba(255, 255, 255, .94);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .10);
    }

    .invoice-btn-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .invoice-btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    .invoice-btn-secondary {
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
    }

    .invoice-alert {
        border-radius: 14px;
    }

    .invoice-auto-number {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #dbe2ea;
        border-radius: 10px;
        color: #334155;
    }

    .invoice-auto-number i {
        color: #2563eb;
    }

    .invoice-auto-number strong {
        color: #0f172a;
    }

    @media (max-width: 767.98px) {
        .invoice-page-header {
            padding: 22px 20px;
            border-radius: 16px;
        }

        .invoice-section-body {
            padding: 18px;
        }

        .invoice-section-header {
            padding: 18px;
        }

        .invoice-action-bar {
            position: static;
            margin-bottom: 20px;
        }
    }

    html[data-theme="dark"] .invoice-page-header {
        background: linear-gradient(135deg, #020617, #172033);
    }

    html[data-theme="dark"] .invoice-section,
    html[data-theme="dark"] .invoice-section-header {
        background: #111827;
        border-color: #263244;
    }

    html[data-theme="dark"] .invoice-section-header {
        border-bottom-color: #263244;
    }

    html[data-theme="dark"] .invoice-section-header h5,
    html[data-theme="dark"] .invoice-form-label,
    html[data-theme="dark"] .invoice-financial-value {
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-section-subtitle,
    html[data-theme="dark"] .form-text {
        color: #94a3b8 !important;
    }

    html[data-theme="dark"] .invoice-info-box,
    html[data-theme="dark"] .invoice-financial-preview {
        background: #0f172a;
        border-color: #263244;
    }

    html[data-theme="dark"] .invoice-readonly,
    html[data-theme="dark"] .invoice-auto-number {
        background: #0f172a !important;
        color: #e2e8f0;
        border-color: #334155;
    }

    html[data-theme="dark"] .invoice-auto-number strong {
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-action-bar {
        background: rgba(17, 24, 39, .94);
        border-color: #263244;
    }

    html[data-theme="dark"] .invoice-page .form-control,
    html[data-theme="dark"] .invoice-page .form-select {
        background-color: #111827;
        border-color: #334155;
        color: #f8fafc;
    }

    html[data-theme="dark"] .invoice-page .form-control::placeholder {
        color: #64748b;
    }

    html[data-theme="dark"] .invoice-page .input-group-text {
        background-color: #0f172a;
        border-color: #334155;
        color: #e2e8f0;
    }
</style>

@endpush

@section('content')

<div class="container-fluid px-3 px-lg-4 py-3 invoice-page">

```
{{-- Page Header --}}
<div class="invoice-page-header mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <div class="invoice-eyebrow">
                <i class="bi bi-receipt"></i>
                Billing &amp; Invoicing
            </div>

            <h1 class="h3 fw-bold mb-2">
                Create Rent Invoice
            </h1>

            <p class="mb-0 text-white-50">
                Generate a rental invoice from an active lease.
                The invoice number is generated automatically, while
                the amount and currency are inherited from the lease.
            </p>
        </div>

        <a
            href="{{ route('rent-invoices.index') }}"
            class="btn btn-light rounded-3 px-3 fw-semibold"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Invoices
        </a>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger invoice-alert mb-4">
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

<form
    action="{{ route('rent-invoices.store') }}"
    method="POST"
    id="invoice-form"
>
    @csrf

    {{-- Invoice Information --}}
    <div class="invoice-section mb-4">
        <div class="invoice-section-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-icon-chip">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Invoice Information
                    </h5>

                    <div class="invoice-section-subtitle">
                        Select the lease and configure the invoice schedule.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-section-body">
            <div class="row g-4">

                {{-- Lease --}}
                <div class="col-lg-6">
                    <label
                        for="lease_id"
                        class="invoice-form-label"
                    >
                        Lease Agreement
                        <span class="invoice-required">*</span>
                    </label>

                    <select
                        name="lease_id"
                        id="lease_id"
                        class="form-select @error('lease_id') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            — Select Lease —
                        </option>

                        @foreach($leases as $lease)
                            @php
                                $leaseCurrency = strtoupper(
                                    (string) $lease->currency
                                );

                                $leaseCurrencySupported = in_array(
                                    $leaseCurrency,
                                    \App\Support\Currency::SUPPORTED,
                                    true
                                );
                            @endphp

                            <option
                                value="{{ $lease->id }}"
                                @selected(old('lease_id') == $lease->id)
                            >
                                {{ $lease->tenant?->full_name ?? $lease->tenant?->name ?? 'Unknown Tenant' }}
                                —
                                {{ $lease->unit?->property?->name ?? 'Unknown Property' }}
                                —
                                Unit {{ $lease->unit?->unit_number ?? $lease->unit?->name ?? '-' }}
                                —

                                @if($leaseCurrencySupported)
                                    {{ \App\Support\Currency::format(
                                        (float) $lease->monthly_rent,
                                        $leaseCurrency
                                    ) }}
                                @else
                                    {{ number_format((float) $lease->monthly_rent, 2) }}
                                    — Currency not set
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('lease_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Only active leases with a supported currency can receive rent invoices.
                    </div>
                </div>

                {{-- Automatically Generated Invoice Number --}}
                <div class="col-lg-6">
                    <label class="invoice-form-label">
                        Invoice Number
                    </label>

                    <div class="invoice-auto-number">
                        <i class="bi bi-shield-check"></i>

                        <div>
                            <strong>Generated automatically</strong>

                            <div class="small text-muted">
                                Format: INV-{{ now()->year }}-000001
                            </div>
                        </div>
                    </div>

                    <div class="form-text">
                        The system assigns the next unique invoice number when this invoice is created.
                    </div>
                </div>

                {{-- Billing Period --}}
                <div class="col-lg-4">
                    <label
                        for="billing_period"
                        class="invoice-form-label"
                    >
                        Billing Period
                        <span class="invoice-required">*</span>
                    </label>

                    <input
                        type="month"
                        name="billing_period"
                        id="billing_period"
                        class="form-control @error('billing_period') is-invalid @enderror"
                        value="{{ old('billing_period', now()->addMonth()->format('Y-m')) }}"
                        required
                    >

                    @error('billing_period')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        One invoice per lease per month.
                    </div>
                </div>

                {{-- Invoice Date --}}
                <div class="col-lg-4">
                    <label
                        for="invoice_date"
                        class="invoice-form-label"
                    >
                        Invoice Date
                        <span class="invoice-required">*</span>
                    </label>

                    <input
                        type="date"
                        name="invoice_date"
                        id="invoice_date"
                        class="form-control @error('invoice_date') is-invalid @enderror"
                        value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                        required
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
                        class="invoice-form-label"
                    >
                        Due Date
                        <span class="invoice-required">*</span>
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        id="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date') }}"
                        required
                    >

                    @error('due_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Payment deadline for this invoice.
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Financial Terms --}}
    <div class="invoice-section mb-4">
        <div class="invoice-section-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-icon-chip">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Financial Terms
                    </h5>

                    <div class="invoice-section-subtitle">
                        Financial values are inherited from the selected lease.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-section-body">

            <div class="invoice-info-box mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-primary fs-5"></i>

                    <div>
                        <div class="fw-bold mb-1">
                            Invoice Amount &amp; Currency
                        </div>

                        <div class="text-muted small">
                            The invoice amount and currency are automatically
                            taken from the selected lease. They cannot be entered
                            manually on this form.
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- Monthly Rent --}}
                <div class="col-lg-6">
                    <label
                        for="lease-rent"
                        class="invoice-form-label"
                    >
                        Lease Monthly Rent
                    </label>

                    <div class="input-group">
                        <span
                            class="input-group-text"
                            id="rent-symbol"
                        >
                            —
                        </span>

                        <input
                            type="text"
                            id="lease-rent"
                            class="form-control invoice-readonly"
                            value=""
                            placeholder="Select a lease"
                            readonly
                        >

                        <span
                            class="input-group-text"
                            id="rent-currency"
                        >
                            —
                        </span>
                    </div>

                    <div class="form-text">
                        This becomes the invoice amount.
                    </div>
                </div>

                {{-- Currency --}}
                <div class="col-lg-6">
                    <label
                        for="lease-currency"
                        class="invoice-form-label"
                    >
                        Invoice Currency
                    </label>

                    <input
                        type="text"
                        id="lease-currency"
                        class="form-control invoice-readonly"
                        value=""
                        placeholder="Select a lease"
                        readonly
                    >

                    <div class="form-text">
                        Currency is inherited from the lease.
                        No exchange-rate conversion is performed.
                    </div>
                </div>

            </div>

            <div class="invoice-financial-preview mt-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="invoice-icon-chip">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <div>
                        <div class="small text-muted mb-1">
                            Initial Outstanding Balance
                        </div>

                        <div
                            id="initial-balance"
                            class="invoice-financial-value"
                        >
                            —
                        </div>

                        <div class="small text-muted mt-1">
                            New invoices start with no payments recorded.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Payment Information --}}
    <div class="invoice-section mb-4">
        <div class="invoice-section-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-icon-chip">
                    <i class="bi bi-wallet2"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Payment Information
                    </h5>

                    <div class="invoice-section-subtitle">
                        Payments are recorded separately after invoice creation.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-section-body">
            <div class="invoice-info-box">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-cash-coin text-primary fs-5"></i>

                    <div>
                        <div class="fw-bold mb-1">
                            No payment is required here
                        </div>

                        <div class="text-muted mb-2">
                            Create the invoice first, then use
                            <strong>Record Payment</strong> to record
                            payments against it.
                        </div>

                        <div class="text-muted small">
                            Payments must use the same currency as the invoice.
                            No automatic currency conversion is performed.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="invoice-section mb-4">
        <div class="invoice-section-header">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-icon-chip">
                    <i class="bi bi-journal-text"></i>
                </div>

                <div>
                    <h5 class="mb-1">
                        Invoice Notes
                    </h5>

                    <div class="invoice-section-subtitle">
                        Add optional remarks or billing information.
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-section-body">
            <label
                for="notes"
                class="invoice-form-label"
            >
                Notes
            </label>

            <textarea
                name="notes"
                id="notes"
                class="form-control @error('notes') is-invalid @enderror"
                rows="4"
                placeholder="Enter any optional notes regarding this rent invoice..."
            >{{ old('notes') }}</textarea>

            @error('notes')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- Actions --}}
    <div class="invoice-action-bar d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a
            href="{{ route('rent-invoices.index') }}"
            class="btn btn-outline-secondary invoice-btn-secondary"
        >
            <i class="bi bi-x-circle me-1"></i>
            Cancel
        </a>

        <button
            type="submit"
            class="btn invoice-btn-primary"
            id="save-invoice-btn"
        >
            <i class="bi bi-check-circle me-1"></i>
            Create Invoice
        </button>
    </div>

</form>

</div>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaseSelect = document.getElementById('lease_id');
    const rentInput = document.getElementById('lease-rent');
    const currencyInput = document.getElementById('lease-currency');
    const rentSymbol = document.getElementById('rent-symbol');
    const rentCurrency = document.getElementById('rent-currency');
    const initialBalance = document.getElementById('initial-balance');
    const invoiceForm = document.getElementById('invoice-form');
    const saveButton = document.getElementById('save-invoice-btn');

    if (!leaseSelect) {
        return;
    }

    const leases = @json($leaseData);

    function resetFinancialInformation() {
        rentInput.value = '';
        currencyInput.value = '';

        rentSymbol.textContent = '—';
        rentCurrency.textContent = '—';

        rentInput.placeholder = 'Select a lease';
        currencyInput.placeholder = 'Select a lease';

        initialBalance.textContent = '—';
    }

    function updateLeaseFinancialInformation() {
        const leaseId = leaseSelect.value;

        if (!leaseId || !leases[leaseId]) {
            resetFinancialInformation();
            return;
        }

        const lease = leases[leaseId];

        rentInput.value = lease.rent;

        if (!lease.supported) {
            currencyInput.value = 'Currency not set';

            rentSymbol.textContent = '—';
            rentCurrency.textContent = '—';

            initialBalance.textContent =
                lease.rent + ' — Currency not set';

            return;
        }

        currencyInput.value = lease.currency;

        rentSymbol.textContent = lease.symbol;
        rentCurrency.textContent = lease.currency;

        initialBalance.textContent =
            lease.symbol + ' ' + lease.rent + ' ' + lease.currency;
    }

    leaseSelect.addEventListener(
        'change',
        updateLeaseFinancialInformation
    );

    if (invoiceForm && saveButton) {
        invoiceForm.addEventListener('submit', function () {
            if (saveButton.disabled) {
                return;
            }

            saveButton.disabled = true;

            saveButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" ' +
                'role="status" aria-hidden="true"></span>' +
                'Creating Invoice...';
        });
    }

    updateLeaseFinancialInformation();
});
</script>

@endpush
