@extends('layouts.app')

@section('title', 'Edit Lease')
@section('page-heading', 'Edit Lease')

@section('content')
@php
use App\Support\Currency;

$leaseCurrency = strtoupper((string) (
    $lease->currency
    ?? $lease->unit?->currency
    ?? ''
));

$leaseCurrencyValid = in_array(
    $leaseCurrency,
    Currency::SUPPORTED,
    true
);

if (!$leaseCurrencyValid) {
    $leaseCurrency = '';
}

$hasFinancialHistory = $lease->rentInvoices()->exists();

$selectedStatus = old('status', $lease->status);

@endphp

<div class="container-fluid lease-form-page px-0">

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex gap-3">
            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
            <div>
                <div class="fw-semibold mb-1">Please review the form.</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

{{-- Header --}}
<div class="lease-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
    <div>
        <div class="lease-eyebrow">
            <i class="bi bi-file-earmark-text me-1"></i>
            Tenancy / Lease #{{ $lease->id }}
        </div>

        <h1 class="lease-title mb-1">Edit Lease Agreement</h1>

        <p class="text-muted mb-0">
            Update the contractual, financial, and payment terms for this lease.
        </p>
    </div>

    <a href="{{ route('leases.show', $lease) }}" class="btn lease-btn-outline">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Lease
    </a>
</div>

<form
    action="{{ route('leases.update', $lease) }}"
    method="POST"
    id="leaseForm"
>
    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- Main Form --}}
        <div class="col-lg-8">

            {{-- Tenant & Unit --}}
            <div class="lease-card mb-4">
                <div class="lease-card-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-building"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Tenant & Property Unit</h5>
                        <p class="mb-0 text-muted">
                            Update the tenant or unit assigned to this lease.
                        </p>
                    </div>
                </div>

                <div class="lease-card-body">
                    <div class="row g-4">

                        {{-- Tenant --}}
                        <div class="col-md-6">
                            <label for="tenant_id" class="lease-form-label">
                                Tenant <span class="text-danger">*</span>
                            </label>

                            <select
                                name="tenant_id"
                                id="tenant_id"
                                class="form-select form-select-lg @error('tenant_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select tenant</option>

                                @foreach($tenants as $tenant)
                                    <option
                                        value="{{ $tenant->id }}"
                                        @selected(old('tenant_id', $lease->tenant_id) == $tenant->id)
                                    >
                                        {{ $tenant->full_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div class="col-md-6">
                            <label for="unit_id" class="lease-form-label">
                                Unit <span class="text-danger">*</span>
                            </label>

                            <select
                                name="unit_id"
                                id="unit_id"
                                class="form-select form-select-lg @error('unit_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select unit</option>

                                @foreach($units as $unit)
                                    @php
                                        $unitCurrency = strtoupper((string) $unit->currency);
                                        $unitCurrencyValid = in_array(
                                            $unitCurrency,
                                            Currency::SUPPORTED,
                                            true
                                        );
                                    @endphp

                                    @if($unitCurrencyValid)
                                        <option
                                            value="{{ $unit->id }}"
                                            data-currency="{{ $unitCurrency }}"
                                            data-rent="{{ number_format((float) $unit->monthly_rent, 2, '.', '') }}"
                                            data-symbol="{{ Currency::symbol($unitCurrency) }}"
                                            data-currency-name="{{ Currency::name($unitCurrency) }}"
                                            @selected(old('unit_id', $lease->unit_id) == $unit->id)
                                        >
                                            {{ $unit->property?->name ?? 'Property' }}
                                            — Unit {{ $unit->unit_number }}
                                            @if($unit->floor !== null)
                                                — Floor {{ $unit->floor }}
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                The unit determines the lease currency.
                            </div>
                        </div>

                    </div>

                    <div class="lease-info-banner mt-4">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <strong>Currency is inherited from the unit</strong>
                            <span>
                                There is no separate lease currency selector. Existing invoices retain their recorded currency.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lease Period --}}
            <div class="lease-card mb-4">
                <div class="lease-card-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-calendar-range"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Lease Period</h5>
                        <p class="mb-0 text-muted">
                            Define when this agreement starts and ends.
                        </p>
                    </div>
                </div>

                <div class="lease-card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label for="start_date" class="lease-form-label">
                                Start Date <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                class="form-control form-control-lg @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', optional($lease->start_date)->format('Y-m-d')) }}"
                                required
                            >

                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="lease-form-label">
                                End Date <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                class="form-control form-control-lg @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date', optional($lease->end_date)->format('Y-m-d')) }}"
                                required
                            >

                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Financial Terms --}}
            <div class="lease-card mb-4">
                <div class="lease-card-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Financial Terms</h5>
                        <p class="mb-0 text-muted">
                            Update the rent and security deposit for this lease.
                        </p>
                    </div>
                </div>

                <div class="lease-card-body">
                    <div class="row g-4">

                        {{-- Monthly Rent --}}
                        <div class="col-md-6">
                            <label for="monthly_rent" class="lease-form-label">
                                Monthly Rent <span class="text-danger">*</span>
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text" id="rentCurrencySymbol">—</span>

                                <input
                                    type="number"
                                    name="monthly_rent"
                                    id="monthly_rent"
                                    class="form-control @error('monthly_rent') is-invalid @enderror"
                                    value="{{ old('monthly_rent', $lease->monthly_rent) }}"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </div>

                            <div class="form-text" id="rentCurrencyText"></div>

                            @error('monthly_rent')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Security Deposit --}}
                        <div class="col-md-6">
                            <label for="security_deposit" class="lease-form-label">
                                Security Deposit <span class="text-danger">*</span>
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text" id="depositCurrencySymbol">—</span>

                                <input
                                    type="number"
                                    name="security_deposit"
                                    id="security_deposit"
                                    class="form-control @error('security_deposit') is-invalid @enderror"
                                    value="{{ old('security_deposit', $lease->security_deposit) }}"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </div>

                            <div class="form-text" id="depositCurrencyText"></div>

                            @error('security_deposit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    @if($hasFinancialHistory)
                        <div class="lease-warning mt-4">
                            <div class="lease-warning-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>

                            <div>
                                <strong>This lease has financial history.</strong>
                                <p class="mb-0">
                                    Existing invoices keep their original amounts and currency.
                                    Changing the monthly rent affects future invoices only.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Settings --}}
            <div class="lease-card mb-4">
                <div class="lease-card-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Payment Settings</h5>
                        <p class="mb-0 text-muted">
                            Configure the monthly payment due date and lease status.
                        </p>
                    </div>
                </div>

                <div class="lease-card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label for="payment_due_day" class="lease-form-label">
                                Payment Due Day <span class="text-danger">*</span>
                            </label>

                            <input
                                type="number"
                                name="payment_due_day"
                                id="payment_due_day"
                                class="form-control form-control-lg @error('payment_due_day') is-invalid @enderror"
                                value="{{ old('payment_due_day', $lease->payment_due_day) }}"
                                min="1"
                                max="31"
                                required
                            >

                            <div class="form-text">
                                Day of each month when rent is due.
                            </div>

                            @error('payment_due_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="lease-form-label">
                                Lease Status <span class="text-danger">*</span>
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select form-select-lg @error('status') is-invalid @enderror"
                                required
                            >
                                <option value="active" @selected($selectedStatus === 'active')>
                                    Active
                                </option>

                                <option value="pending" @selected($selectedStatus === 'pending')>
                                    Pending
                                </option>

                                <option value="expired" @selected($selectedStatus === 'expired')>
                                    Expired
                                </option>

                                <option value="terminated" @selected($selectedStatus === 'terminated')>
                                    Terminated
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="lease-card mb-4">
                <div class="lease-card-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-journal-text"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Additional Notes</h5>
                        <p class="mb-0 text-muted">
                            Record special agreements or other relevant information.
                        </p>
                    </div>
                </div>

                <div class="lease-card-body">
                    <textarea
                        name="notes"
                        id="notes"
                        rows="5"
                        class="form-control @error('notes') is-invalid @enderror"
                        placeholder="Add any additional lease notes..."
                    >{{ old('notes', $lease->notes) }}</textarea>

                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-5">
                <a
                    href="{{ route('leases.show', $lease) }}"
                    class="btn lease-btn-outline btn-lg"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn lease-btn-primary btn-lg"
                    id="submitButton"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Update Lease
                </button>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="lease-summary-card sticky-lg-top">

                <div class="lease-summary-header">
                    <div class="lease-section-icon">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">Lease Summary</h5>
                        <small class="text-muted">Live preview</small>
                    </div>
                </div>

                <div class="lease-summary-body">

                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="bi bi-person me-1"></i>
                            Tenant
                        </div>
                        <div class="summary-value" id="summaryTenant">
                            {{ $lease->tenant?->full_name ?? '—' }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="bi bi-door-open me-1"></i>
                            Unit
                        </div>
                        <div class="summary-value" id="summaryUnit">
                            {{ $lease->unit?->unit_number ?? '—' }}
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-financial">
                        <div>
                            <div class="summary-label">Monthly Rent</div>
                            <div class="summary-money" id="summaryRent">—</div>
                        </div>

                        <div>
                            <div class="summary-label">Security Deposit</div>
                            <div class="summary-money" id="summaryDeposit">—</div>
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="bi bi-calendar-event me-1"></i>
                            Payment Due
                        </div>
                        <div class="summary-value" id="summaryDueDay">—</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="bi bi-circle-fill me-1"></i>
                            Status
                        </div>

                        <span class="badge lease-status-badge" id="summaryStatus">
                            —
                        </span>
                    </div>

                    @if($leaseCurrencyValid)
                        <div class="summary-currency">
                            <i class="bi bi-currency-exchange me-2"></i>
                            <div>
                                <strong>{{ $leaseCurrency }}</strong>
                                <span>{{ Currency::name($leaseCurrency) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="summary-currency summary-currency-warning">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <div>
                                <strong>Currency not set</strong>
                                <span>Select a unit with a supported currency.</span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</form>

</div>
@endsection

@push('styles')

<style>
.lease-form-page {
    max-width: 1180px;
    margin: 0 auto;
}

.lease-page-header {
    padding-top: .5rem;
}

.lease-eyebrow {
    color: var(--bs-primary);
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: .45rem;
}

.lease-title {
    color: #172033;
    font-size: 1.8rem;
    font-weight: 750;
    letter-spacing: -.025em;
}

.lease-card,
.lease-summary-card {
    background: var(--bs-body-bg);
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    overflow: hidden;
}

.lease-card-header,
.lease-summary-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 1.15rem 1.35rem;
    border-bottom: 1px solid rgba(15, 23, 42, .07);
}

.lease-card-header h5,
.lease-summary-header h5 {
    color: #172033;
    font-size: 1rem;
    font-weight: 750;
}

.lease-section-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: rgba(var(--bs-primary-rgb), .1);
    color: var(--bs-primary);
    font-size: 1.1rem;
}

.lease-card-body {
    padding: 1.35rem;
}

.lease-form-label {
    display: block;
    color: #344054;
    font-size: .86rem;
    font-weight: 700;
    margin-bottom: .5rem;
}

.lease-info-banner {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .9rem 1rem;
    border: 1px solid rgba(var(--bs-primary-rgb), .14);
    border-radius: 12px;
    background: rgba(var(--bs-primary-rgb), .055);
    color: #344054;
    font-size: .84rem;
}

.lease-info-banner > i {
    color: var(--bs-primary);
    margin-top: .1rem;
}

.lease-info-banner strong,
.lease-info-banner span {
    display: block;
}

.lease-info-banner strong {
    margin-bottom: .1rem;
}

.lease-warning {
    display: flex;
    gap: .8rem;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid rgba(245, 158, 11, .22);
    background: rgba(245, 158, 11, .08);
    color: #7c4a03;
    font-size: .84rem;
}

.lease-warning-icon {
    flex: 0 0 auto;
    color: #d97706;
}

.lease-warning strong {
    display: block;
    margin-bottom: .15rem;
}

.lease-btn-primary {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #fff;
    font-weight: 700;
    padding-inline: 1.25rem;
}

.lease-btn-primary:hover {
    color: #fff;
    filter: brightness(.94);
}

.lease-btn-outline {
    border: 1px solid var(--bs-border-color);
    background: var(--bs-body-bg);
    color: #344054;
    font-weight: 650;
}

.lease-btn-outline:hover {
    background: var(--bs-tertiary-bg);
    color: #172033;
}

.lease-card .form-control,
.lease-card .form-select,
.lease-card .input-group-text {
    border-radius: 10px;
}

.lease-card .input-group > .form-control {
    border-radius: 0;
}

.lease-card .input-group > .input-group-text:first-child {
    border-radius: 10px 0 0 10px;
}

.lease-summary-card {
    top: 1rem;
}

.lease-summary-body {
    padding: 1.25rem;
}

.summary-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-label {
    color: #667085;
    font-size: .76rem;
    font-weight: 650;
}

.summary-value {
    max-width: 65%;
    color: #172033;
    font-size: .9rem;
    font-weight: 700;
    text-align: right;
    word-break: break-word;
}

.summary-divider {
    height: 1px;
    background: rgba(15, 23, 42, .08);
    margin: 1rem 0;
}

.summary-financial {
    display: grid;
    gap: 1rem;
}

.summary-money {
    color: #172033;
    font-size: 1.12rem;
    font-weight: 800;
    margin-top: .15rem;
}

.lease-status-badge {
    background: #e2e8f0;
    color: #334155;
    font-weight: 700;
    padding: .4rem .65rem;
}

.summary-currency {
    display: flex;
    align-items: center;
    margin-top: 1.2rem;
    padding: .8rem;
    border-radius: 11px;
    background: rgba(var(--bs-primary-rgb), .07);
    color: var(--bs-primary);
}

.summary-currency strong,
.summary-currency span {
    display: block;
}

.summary-currency strong {
    font-size: .82rem;
}

.summary-currency span {
    color: #667085;
    font-size: .73rem;
}

.summary-currency-warning {
    background: rgba(245, 158, 11, .08);
    color: #b45309;
}

html[data-theme="dark"] .lease-title,
html[data-theme="dark"] .lease-card-header h5,
html[data-theme="dark"] .lease-summary-header h5,
html[data-theme="dark"] .lease-form-label,
html[data-theme="dark"] .summary-value,
html[data-theme="dark"] .summary-money {
    color: #f1f5f9;
}

html[data-theme="dark"] .lease-card,
html[data-theme="dark"] .lease-summary-card {
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 8px 28px rgba(0,0,0,.16);
}

html[data-theme="dark"] .lease-card-header,
html[data-theme="dark"] .lease-summary-header {
    border-bottom-color: rgba(255,255,255,.08);
}

html[data-theme="dark"] .lease-info-banner {
    color: #cbd5e1;
}

html[data-theme="dark"] .lease-warning {
    color: #fcd34d;
}

html[data-theme="dark"] .summary-divider {
    background: rgba(255,255,255,.08);
}

html[data-theme="dark"] .summary-currency span,
html[data-theme="dark"] .summary-label {
    color: #94a3b8;
}

html[data-theme="dark"] .lease-btn-outline {
    color: #e2e8f0;
    border-color: rgba(255,255,255,.14);
}

html[data-theme="dark"] .lease-btn-outline:hover {
    color: #fff;
}

@media (max-width: 991.98px) {
    .lease-summary-card {
        position: static !important;
    }
}

@media (max-width: 767.98px) {
    .lease-form-page {
        padding-inline: .75rem;
    }

    .lease-title {
        font-size: 1.5rem;
    }

    .lease-card-header,
    .lease-card-body,
    .lease-summary-header,
    .lease-summary-body {
        padding: 1rem;
    }

    .summary-value {
        max-width: 58%;
    }

    .lease-btn-outline,
    .lease-btn-primary {
        width: 100%;
    }
}
</style>

@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tenantSelect = document.getElementById('tenant_id');
    const unitSelect = document.getElementById('unit_id');
    const monthlyRentInput = document.getElementById('monthly_rent');
    const securityDepositInput = document.getElementById('security_deposit');
    const paymentDueDayInput = document.getElementById('payment_due_day');
    const statusSelect = document.getElementById('status');

    const rentCurrencySymbol = document.getElementById('rentCurrencySymbol');
    const depositCurrencySymbol = document.getElementById('depositCurrencySymbol');
    const rentCurrencyText = document.getElementById('rentCurrencyText');
    const depositCurrencyText = document.getElementById('depositCurrencyText');

    const summaryTenant = document.getElementById('summaryTenant');
    const summaryUnit = document.getElementById('summaryUnit');
    const summaryRent = document.getElementById('summaryRent');
    const summaryDeposit = document.getElementById('summaryDeposit');
    const summaryDueDay = document.getElementById('summaryDueDay');
    const summaryStatus = document.getElementById('summaryStatus');
    const submitButton = document.getElementById('submitButton');

    const currencySymbols = {
        USD: '$',
        KES: 'KSh',
        CDF: 'FC'
    };

    const currencyNames = {
        USD: 'US Dollar',
        KES: 'Kenyan Shilling',
        CDF: 'Congolese Franc'
    };

    function getSelectedCurrency() {
        const selectedOption = unitSelect.options[unitSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            return '';
        }

        return String(
            selectedOption.dataset.currency || ''
        ).toUpperCase();
    }

    function getCurrencySymbol(currency) {
        return currencySymbols[currency] || '';
    }

    function getCurrencyName(currency) {
        return currencyNames[currency] || '';
    }

    function formatAmount(amount, currency) {
        const value = Number(amount || 0);

        if (!currency) {
            return value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        return getCurrencySymbol(currency) +
            ' ' +
            value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) +
            ' ' +
            currency;
    }

    function updateCurrencyInformation() {
        const currency = getSelectedCurrency();

        if (!currency) {
            rentCurrencySymbol.textContent = '—';
            depositCurrencySymbol.textContent = '—';
            rentCurrencyText.textContent = 'Currency not set.';
            depositCurrencyText.textContent = 'Currency not set.';
            updateSummary();
            return;
        }

        const symbol = getCurrencySymbol(currency);
        const name = getCurrencyName(currency);

        rentCurrencySymbol.textContent = symbol;
        depositCurrencySymbol.textContent = symbol;

        rentCurrencyText.textContent =
            name + ' (' + currency + ')';

        depositCurrencyText.textContent =
            name + ' (' + currency + ')';

        updateSummary();
    }

    function updateSummary() {
        const currency = getSelectedCurrency();

        const selectedTenant =
            tenantSelect.options[tenantSelect.selectedIndex];

        const selectedUnit =
            unitSelect.options[unitSelect.selectedIndex];

        summaryTenant.textContent =
            selectedTenant?.value
                ? selectedTenant.textContent.trim()
                : '—';

        summaryUnit.textContent =
            selectedUnit?.value
                ? selectedUnit.textContent.trim()
                : '—';

        summaryRent.textContent =
            formatAmount(monthlyRentInput.value, currency);

        summaryDeposit.textContent =
            formatAmount(securityDepositInput.value, currency);

        summaryDueDay.textContent =
            paymentDueDayInput.value
                ? 'Day ' + paymentDueDayInput.value
                : '—';

        const status = statusSelect.value;

        summaryStatus.textContent =
            status
                ? status.charAt(0).toUpperCase() + status.slice(1)
                : '—';

        summaryStatus.className =
            'badge lease-status-badge ' + getStatusBadgeClass(status);
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'active':
                return 'bg-success-subtle text-success';
            case 'pending':
                return 'bg-warning-subtle text-warning-emphasis';
            case 'expired':
                return 'bg-secondary-subtle text-secondary';
            case 'terminated':
                return 'bg-danger-subtle text-danger';
            default:
                return '';
        }
    }

    tenantSelect.addEventListener('change', updateSummary);

    unitSelect.addEventListener('change', function () {
        /*
         * The unit controls the lease currency.
         * Monthly rent remains independently editable at lease level.
         */
        updateCurrencyInformation();
    });

    monthlyRentInput.addEventListener('input', updateSummary);
    securityDepositInput.addEventListener('input', updateSummary);
    paymentDueDayInput.addEventListener('input', updateSummary);
    statusSelect.addEventListener('change', updateSummary);

    document.getElementById('leaseForm')?.addEventListener('submit', function () {
        if (!submitButton) return;

        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Updating Lease...';
    });

    updateCurrencyInformation();
    updateSummary();
});
</script>

@endpush
