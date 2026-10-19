@extends('layouts.app')

@section('title', 'Add Lease')
@section('page-heading', 'Add Lease')

@section('content')
@php
use App\Support\Currency;
@endphp

<div class="container-fluid lease-form-page px-0">

{{-- Alerts --}}
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
            Tenancy / New Lease
        </div>

        <h1 class="lease-title mb-1">Create Lease Agreement</h1>

        <p class="text-muted mb-0">
            Create a lease for a tenant and assign it to a vacant unit.
        </p>
    </div>

    <a href="{{ route('leases.index') }}" class="btn lease-btn-outline">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Leases
    </a>
</div>

<form action="{{ route('leases.store') }}" method="POST" id="leaseForm">
    @csrf

    {{-- Parties & Unit --}}
    <div class="lease-card mb-4">
        <div class="lease-card-header">
            <div class="lease-section-icon">
                <i class="bi bi-building"></i>
            </div>

            <div>
                <h5 class="mb-1">Tenant & Property Unit</h5>
                <p class="mb-0 text-muted">
                    Select who will occupy the unit and which vacant unit they will lease.
                </p>
            </div>
        </div>

        <div class="lease-card-body">
            <div class="row g-4">

                {{-- Tenant --}}
                <div class="col-lg-6">
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
                                @selected(old('tenant_id') == $tenant->id)
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
                <div class="col-lg-6">
                    <label for="unit_id" class="lease-form-label">
                        Vacant Unit <span class="text-danger">*</span>
                    </label>

                    <select
                        name="unit_id"
                        id="unit_id"
                        class="form-select form-select-lg @error('unit_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Select vacant unit</option>

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
                                    data-rent="{{ number_format((float) $unit->monthly_rent, 2, '.', '') }}"
                                    data-currency="{{ $unitCurrency }}"
                                    data-symbol="{{ Currency::symbol($unitCurrency) }}"
                                    data-currency-name="{{ Currency::name($unitCurrency) }}"
                                    @selected(old('unit_id') == $unit->id)
                                >
                                    {{ $unit->property?->name ?? 'Unknown Property' }}
                                    — Unit {{ $unit->unit_number }}
                                    — {{ Currency::format((float) $unit->monthly_rent, $unitCurrency) }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        The selected unit determines the lease currency.
                    </div>
                </div>

            </div>

            <div class="lease-info-banner mt-4">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong>Currency inheritance</strong>
                    <span>
                        Lease currency comes from the selected unit. The currency is not selected separately on the lease.
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
                    Define the contractual start and end dates.
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
                        value="{{ old('start_date') }}"
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
                        value="{{ old('end_date') }}"
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
                    Review the inherited rent currency and define the security deposit.
                </p>
            </div>
        </div>

        <div class="lease-card-body">
            <div class="row g-4">

                {{-- Monthly Rent --}}
                <div class="col-lg-6">
                    <label class="lease-form-label">
                        Monthly Rent
                    </label>

                    <div id="monthly-rent-display" class="lease-financial-display">
                        <i class="bi bi-dash-circle me-2"></i>
                        Select a unit to see its rental amount.
                    </div>

                    <div id="currency-display" class="form-text mt-2">
                        Monthly rent and currency will be inherited from the selected unit.
                    </div>
                </div>

                {{-- Deposit --}}
                <div class="col-lg-6">
                    <label for="security_deposit" class="lease-form-label">
                        Security Deposit <span class="text-danger">*</span>
                    </label>

                    <div class="input-group input-group-lg">
                        <span class="input-group-text" id="deposit-currency-symbol">—</span>

                        <input
                            type="number"
                            name="security_deposit"
                            id="security_deposit"
                            class="form-control @error('security_deposit') is-invalid @enderror"
                            step="0.01"
                            min="0"
                            value="{{ old('security_deposit', 0) }}"
                            required
                        >

                        <span class="input-group-text" id="deposit-currency-code">—</span>
                    </div>

                    <div id="deposit-currency-help" class="form-text mt-2">
                        Select a unit to determine the deposit currency.
                    </div>

                    @error('security_deposit')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Due Day --}}
                <div class="col-md-6">
                    <label for="payment_due_day" class="lease-form-label">
                        Payment Due Day <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="payment_due_day"
                        id="payment_due_day"
                        class="form-control form-control-lg @error('payment_due_day') is-invalid @enderror"
                        min="1"
                        max="31"
                        value="{{ old('payment_due_day', 5) }}"
                        required
                    >

                    <div class="form-text">
                        Day of each month when rent is due.
                    </div>

                    @error('payment_due_day')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
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
                        <option value="pending" @selected(old('status', 'pending') === 'pending')>
                            Pending
                        </option>

                        <option value="active" @selected(old('status') === 'active')>
                            Active
                        </option>

                        <option value="expired" @selected(old('status') === 'expired')>
                            Expired
                        </option>

                        <option value="terminated" @selected(old('status') === 'terminated')>
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
                    Record special agreements, clauses, or other useful information.
                </p>
            </div>
        </div>

        <div class="lease-card-body">
            <textarea
                name="notes"
                id="notes"
                rows="5"
                class="form-control @error('notes') is-invalid @enderror"
                placeholder="Enter additional lease notes..."
            >{{ old('notes') }}</textarea>

            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Actions --}}
    <div class="lease-actions d-flex flex-column flex-sm-row justify-content-end gap-2 mb-5">
        <a href="{{ route('leases.index') }}" class="btn lease-btn-outline btn-lg">
            Cancel
        </a>

        <button type="submit" class="btn lease-btn-primary btn-lg" id="saveLeaseBtn">
            <i class="bi bi-check-lg me-1"></i>
            Save Lease
        </button>
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

.lease-card {
    background: var(--bs-body-bg);
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    overflow: hidden;
}

.lease-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 1.15rem 1.35rem;
    border-bottom: 1px solid rgba(15, 23, 42, .07);
}

.lease-card-header h5 {
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

.lease-financial-display {
    min-height: 56px;
    display: flex;
    align-items: center;
    padding: .8rem 1rem;
    border: 1px solid var(--bs-border-color);
    border-radius: 10px;
    background: var(--bs-tertiary-bg);
    color: #344054;
    font-weight: 700;
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

.lease-card .input-group > .input-group-text:last-child {
    border-radius: 0 10px 10px 0;
}

.lease-card textarea {
    resize: vertical;
}

html[data-theme="dark"] .lease-title,
html[data-theme="dark"] .lease-card-header h5,
html[data-theme="dark"] .lease-form-label {
    color: #f1f5f9;
}

html[data-theme="dark"] .lease-card {
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 8px 28px rgba(0,0,0,.16);
}

html[data-theme="dark"] .lease-card-header {
    border-bottom-color: rgba(255,255,255,.08);
}

html[data-theme="dark"] .lease-financial-display {
    color: #e2e8f0;
}

html[data-theme="dark"] .lease-info-banner {
    color: #cbd5e1;
}

html[data-theme="dark"] .lease-btn-outline {
    color: #e2e8f0;
    border-color: rgba(255,255,255,.14);
}

html[data-theme="dark"] .lease-btn-outline:hover {
    color: #fff;
}

@media (max-width: 767.98px) {
    .lease-form-page {
        padding-inline: .75rem;
    }

    .lease-title {
        font-size: 1.5rem;
    }

    .lease-card-header,
    .lease-card-body {
        padding: 1rem;
    }

    .lease-actions .btn {
        width: 100%;
    }
}
</style>

@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitSelect = document.getElementById('unit_id');
    const monthlyRentDisplay = document.getElementById('monthly-rent-display');
    const currencyDisplay = document.getElementById('currency-display');
    const depositCurrencySymbol = document.getElementById('deposit-currency-symbol');
    const depositCurrencyCode = document.getElementById('deposit-currency-code');
    const depositCurrencyHelp = document.getElementById('deposit-currency-help');
    const saveLeaseBtn = document.getElementById('saveLeaseBtn');

    function updateUnitFinancialInformation() {
        const selectedOption = unitSelect.options[unitSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            monthlyRentDisplay.innerHTML =
                '<i class="bi bi-dash-circle me-2"></i>Select a unit to see its rental amount.';

            currencyDisplay.textContent =
                'Monthly rent and currency will be inherited from the selected unit.';

            depositCurrencySymbol.textContent = '—';
            depositCurrencyCode.textContent = '—';
            depositCurrencyHelp.textContent =
                'Select a unit to determine the deposit currency.';

            return;
        }

        const rent = selectedOption.dataset.rent || '';
        const currency = (selectedOption.dataset.currency || '').toUpperCase();
        const symbol = selectedOption.dataset.symbol || '';
        const currencyName = selectedOption.dataset.currencyName || '';

        monthlyRentDisplay.innerHTML =
            '<i class="bi bi-cash-stack me-2 text-primary"></i>' +
            symbol + ' ' + rent + ' ' + currency;

        currencyDisplay.textContent =
            'Currency: ' + currency + ' — ' + currencyName;

        depositCurrencySymbol.textContent = symbol;
        depositCurrencyCode.textContent = currency;

        depositCurrencyHelp.textContent =
            'Enter the security deposit in ' +
            currencyName + ' (' + currency + ').';
    }

    unitSelect.addEventListener('change', updateUnitFinancialInformation);

    document.getElementById('leaseForm')?.addEventListener('submit', function () {
        if (!saveLeaseBtn) return;

        saveLeaseBtn.disabled = true;
        saveLeaseBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving Lease...';
    });

    updateUnitFinancialInformation();
});
</script>

@endpush
