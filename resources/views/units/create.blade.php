@extends('layouts.app')

@section('title', 'Add Unit')
@section('page-heading', 'Add Unit')

@section('content')

@php
    use App\Support\Currency;
@endphp

@push('styles')
    <style>
        .unit-create-page {
            --unit-border: #e7ebf0;
            --unit-dark: #172033;
            --unit-muted: #64748b;
        }

        .unit-create-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #64748b;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: .45rem;
        }

        .unit-create-eyebrow::before {
            content: '';
            width: 24px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .unit-create-title {
            color: var(--unit-dark);
            letter-spacing: -.025em;
        }

        .unit-form-card {
            border: 1px solid var(--unit-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .unit-form-header {
            padding: 1.2rem 1.35rem;
            border-bottom: 1px solid var(--unit-border);
            background: linear-gradient(to right, #fff, #f8fafc);
        }

        .unit-section {
            padding: 1.35rem;
        }

        .unit-section + .unit-section {
            border-top: 1px solid var(--unit-border);
        }

        .unit-section-heading {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: 1.15rem;
        }

        .unit-section-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #eff6ff;
            color: #2563eb;
        }

        .unit-section-title {
            color: var(--unit-dark);
            font-size: .95rem;
            font-weight: 800;
            margin: 0;
        }

        .unit-section-description {
            color: var(--unit-muted);
            font-size: .78rem;
            margin: .15rem 0 0;
        }

        .unit-form-label {
            color: #334155;
            font-size: .82rem;
            font-weight: 700;
            margin-bottom: .45rem;
        }

        .unit-form-control {
            min-height: 44px;
            border-radius: 10px;
            border-color: #dbe2ea;
        }

        .unit-form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .1);
        }

        textarea.unit-form-control {
            min-height: 100px;
        }

        .unit-required {
            color: #dc3545;
        }

        .currency-info {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            background: #f0f9ff;
            color: #0c4a6e;
            padding: .9rem 1rem;
            margin-bottom: 1.15rem;
        }

        .currency-info-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #e0f2fe;
            color: #0369a1;
        }

        .currency-preview {
            margin-top: .65rem;
            padding: .65rem .8rem;
            border-radius: 9px;
            background: #f8fafc;
            color: #64748b;
            font-size: .75rem;
        }

        .form-help {
            color: #94a3b8;
            font-size: .74rem;
            margin-top: .35rem;
        }

        .unit-form-actions {
            padding: 1.2rem 1.35rem;
            border-top: 1px solid var(--unit-border);
            background: #fafbfc;
        }

        .unit-action-btn {
            border-radius: 10px;
            font-weight: 700;
            padding: .62rem 1rem;
        }

        .unit-submit-btn {
            min-width: 130px;
        }

        .unit-side-note {
            border: 1px solid var(--unit-border);
            border-radius: 14px;
            background: #fff;
            padding: 1rem;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
        }

        .unit-side-note-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
        }

        .unit-side-note-title {
            color: var(--unit-dark);
            font-size: .82rem;
            font-weight: 800;
        }

        .unit-side-note-text {
            color: #64748b;
            font-size: .76rem;
            line-height: 1.55;
        }

        html[data-theme="dark"] .unit-create-title,
        html[data-theme="dark"] .unit-section-title,
        html[data-theme="dark"] .unit-side-note-title {
            color: #f8fafc !important;
        }

        html[data-theme="dark"] .unit-form-card,
        html[data-theme="dark"] .unit-side-note {
            background: #111827;
            border-color: #263244;
            box-shadow: none;
        }

        html[data-theme="dark"] .unit-form-header,
        html[data-theme="dark"] .unit-form-actions {
            background: #172033;
            border-color: #263244;
        }

        html[data-theme="dark"] .unit-section + .unit-section {
            border-color: #263244;
        }

        html[data-theme="dark"] .unit-form-label {
            color: #cbd5e1;
        }

        html[data-theme="dark"] .unit-form-control {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        html[data-theme="dark"] .unit-form-control::placeholder {
            color: #64748b;
        }

        html[data-theme="dark"] .form-help,
        html[data-theme="dark"] .unit-side-note-text {
            color: #94a3b8;
        }

        html[data-theme="dark"] .currency-preview {
            background: #172033;
            color: #94a3b8;
        }

        html[data-theme="dark"] .unit-section-icon,
        html[data-theme="dark"] .unit-side-note-icon {
            background: #1e293b;
            color: #93c5fd;
        }

        html[data-theme="dark"] .currency-info {
            background: #082f49;
            border-color: #075985;
            color: #bae6fd;
        }

        html[data-theme="dark"] .currency-info-icon {
            background: #0c4a6e;
            color: #bae6fd;
        }
    </style>
@endpush

<div class="container-fluid unit-create-page px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">

        <div>
            <div class="unit-create-eyebrow">Units / New</div>

            <h1 class="h3 mb-1 fw-bold unit-create-title">
                Add Unit
            </h1>

            <p class="text-muted mb-0">
                Create and register a new individual unit under a property.
            </p>
        </div>

        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary unit-action-btn">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Units
        </a>

    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                <div>
                    <strong>Please review the form.</strong>

                    <ul class="mb-0 mt-1 ps-3">
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

    <div class="row g-4">

        {{-- Main Form --}}
        <div class="col-12 col-xl-9">

            <div class="unit-form-card">

                <div class="unit-form-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="unit-section-icon">
                            <i class="bi bi-door-open"></i>
                        </span>

                        <div>
                            <h5 class="mb-1 fw-bold unit-create-title">
                                Unit Information
                            </h5>

                            <small class="text-muted">
                                Enter the property, layout, rental, and occupancy details.
                            </small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('units.store') }}" method="POST" id="unitForm">
                    @csrf

                    {{-- Property & Unit Details --}}
                    <div class="unit-section">

                        <div class="unit-section-heading">
                            <span class="unit-section-icon">
                                <i class="bi bi-building"></i>
                            </span>

                            <div>
                                <h6 class="unit-section-title">
                                    Property & Unit Details
                                </h6>

                                <p class="unit-section-description">
                                    Identify the property and describe the physical unit.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">

                            {{-- Property --}}
                            <div class="col-md-8">
                                <label for="property_id" class="form-label unit-form-label">
                                    Property
                                    <span class="unit-required">*</span>
                                </label>

                                <select
                                    name="property_id"
                                    id="property_id"
                                    class="form-select unit-form-control @error('property_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Select Property --</option>

                                    @foreach($properties as $property)
                                        <option
                                            value="{{ $property->id }}"
                                            @selected(old('property_id') == $property->id)
                                        >
                                            {{ $property->name }}
                                            @if($property->city)
                                                — {{ $property->city }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('property_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Unit Number --}}
                            <div class="col-md-4">
                                <label for="unit_number" class="form-label unit-form-label">
                                    Unit Number
                                    <span class="unit-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="unit_number"
                                    id="unit_number"
                                    class="form-control unit-form-control @error('unit_number') is-invalid @enderror"
                                    value="{{ old('unit_number') }}"
                                    placeholder="e.g. A101"
                                    required
                                >

                                @error('unit_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Floor --}}
                            <div class="col-md-4">
                                <label for="floor" class="form-label unit-form-label">
                                    Floor
                                </label>

                                <input
                                    type="number"
                                    name="floor"
                                    id="floor"
                                    class="form-control unit-form-control @error('floor') is-invalid @enderror"
                                    value="{{ old('floor') }}"
                                    min="0"
                                    placeholder="e.g. 1"
                                >

                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bedrooms --}}
                            <div class="col-md-4">
                                <label for="bedrooms" class="form-label unit-form-label">
                                    Bedrooms
                                    <span class="unit-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="bedrooms"
                                    id="bedrooms"
                                    class="form-control unit-form-control @error('bedrooms') is-invalid @enderror"
                                    value="{{ old('bedrooms', 0) }}"
                                    min="0"
                                    required
                                >

                                @error('bedrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bathrooms --}}
                            <div class="col-md-4">
                                <label for="bathrooms" class="form-label unit-form-label">
                                    Bathrooms
                                    <span class="unit-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="bathrooms"
                                    id="bathrooms"
                                    class="form-control unit-form-control @error('bathrooms') is-invalid @enderror"
                                    value="{{ old('bathrooms', 0) }}"
                                    min="0"
                                    required
                                >

                                @error('bathrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Rental Information --}}
                    <div class="unit-section">

                        <div class="unit-section-heading">
                            <span class="unit-section-icon">
                                <i class="bi bi-cash-stack"></i>
                            </span>

                            <div>
                                <h6 class="unit-section-title">
                                    Rental Information
                                </h6>

                                <p class="unit-section-description">
                                    Set the rental amount and currency used by this unit.
                                </p>
                            </div>
                        </div>

                        <div class="currency-info">
                            <span class="currency-info-icon">
                                <i class="bi bi-info-circle"></i>
                            </span>

                            <div>
                                <div class="fw-bold mb-1">
                                    Unit Currency
                                </div>

                                <div class="small">
                                    The currency selected here belongs to this unit.
                                    Leases and rent invoices created for the unit will
                                    inherit this currency.
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">

                            {{-- Monthly Rent --}}
                            <div class="col-md-6">
                                <label for="monthly_rent" class="form-label unit-form-label">
                                    Monthly Rent
                                    <span class="unit-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="monthly_rent"
                                    id="monthly_rent"
                                    class="form-control unit-form-control @error('monthly_rent') is-invalid @enderror"
                                    value="{{ old('monthly_rent') }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="e.g. 500.00"
                                    required
                                >

                                <div class="form-help">
                                    Enter the standard monthly rental amount.
                                </div>

                                @error('monthly_rent')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Currency --}}
                            <div class="col-md-6">
                                <label for="currency" class="form-label unit-form-label">
                                    Currency
                                    <span class="unit-required">*</span>
                                </label>

                                <select
                                    name="currency"
                                    id="currency"
                                    class="form-select unit-form-control @error('currency') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Select Currency --</option>

                                    @foreach(Currency::SUPPORTED as $currency)
                                        <option
                                            value="{{ $currency }}"
                                            @selected(old('currency') === $currency)
                                        >
                                            {{ $currency }} — {{ Currency::name($currency) }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="form-help">
                                    Supported currencies: USD, KES, and CDF.
                                </div>

                                <div id="currencyPreview" class="currency-preview d-none">
                                    <i class="bi bi-wallet2 me-1"></i>
                                    <span id="currencyPreviewText"></span>
                                </div>

                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label for="status" class="form-label unit-form-label">
                                    Status
                                    <span class="unit-required">*</span>
                                </label>

                                <select
                                    name="status"
                                    id="status"
                                    class="form-select unit-form-control @error('status') is-invalid @enderror"
                                    required
                                >
                                    <option
                                        value="vacant"
                                        @selected(old('status', 'vacant') === 'vacant')
                                    >
                                        Vacant
                                    </option>

                                    <option
                                        value="occupied"
                                        @selected(old('status') === 'occupied')
                                    >
                                        Occupied
                                    </option>

                                    <option
                                        value="reserved"
                                        @selected(old('status') === 'reserved')
                                    >
                                        Reserved
                                    </option>

                                    <option
                                        value="maintenance"
                                        @selected(old('status') === 'maintenance')
                                    >
                                        Maintenance
                                    </option>
                                </select>

                                <div class="form-help">
                                    Choose the unit's current availability status.
                                </div>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="unit-form-actions d-flex flex-wrap justify-content-between align-items-center gap-2">

                        <div class="small text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Required fields are marked with <span class="text-danger">*</span>.
                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <a
                                href="{{ route('units.index') }}"
                                class="btn btn-outline-secondary unit-action-btn"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary unit-action-btn unit-submit-btn"
                                id="saveUnitBtn"
                            >
                                <i class="bi bi-check-lg me-1"></i>
                                Save Unit
                            </button>

                        </div>
                    </div>

                </form>

            </div>

        </div>

        {{-- Side Information --}}
        <div class="col-12 col-xl-3">

            <div class="unit-side-note mb-3">
                <div class="d-flex gap-3">
                    <span class="unit-side-note-icon">
                        <i class="bi bi-building"></i>
                    </span>

                    <div>
                        <div class="unit-side-note-title mb-1">
                            Property Assignment
                        </div>

                        <div class="unit-side-note-text">
                            Every unit must belong to an existing property.
                            Select the correct property before saving.
                        </div>
                    </div>
                </div>
            </div>

            <div class="unit-side-note mb-3">
                <div class="d-flex gap-3">
                    <span class="unit-side-note-icon">
                        <i class="bi bi-currency-exchange"></i>
                    </span>

                    <div>
                        <div class="unit-side-note-title mb-1">
                            Currency Tracking
                        </div>

                        <div class="unit-side-note-text">
                            The unit's currency becomes the source currency for
                            its lease and subsequent rent invoices.
                        </div>
                    </div>
                </div>
            </div>

            <div class="unit-side-note">
                <div class="d-flex gap-3">
                    <span class="unit-side-note-icon">
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <div>
                        <div class="unit-side-note-title mb-1">
                            Historical Records
                        </div>

                        <div class="unit-side-note-text">
                            Once financial records are created, their recorded
                            currency should remain unchanged for historical accuracy.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts') <script>
document.addEventListener('DOMContentLoaded', function () {
const form = document.getElementById('unitForm');
const saveButton = document.getElementById('saveUnitBtn');
const currencySelect = document.getElementById('currency');
const rentInput = document.getElementById('monthly_rent');
const currencyPreview = document.getElementById('currencyPreview');
const currencyPreviewText = document.getElementById('currencyPreviewText');

```
        const currencySymbols = {
            USD: '$',
            KES: 'KSh',
            CDF: 'FC'
        };

        function updateCurrencyPreview() {
            const currency = currencySelect.value;
            const rent = rentInput.value;

            if (!currency) {
                currencyPreview.classList.add('d-none');
                return;
            }

            const symbol = currencySymbols[currency] ?? currency;

            if (rent !== '') {
                const formattedRent = Number(rent).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                currencyPreviewText.textContent =
                    `Monthly rent: ${symbol} ${formattedRent} ${currency}`;
            } else {
                currencyPreviewText.textContent =
                    `Monthly rent currency: ${symbol} ${currency}`;
            }

            currencyPreview.classList.remove('d-none');
        }

        currencySelect.addEventListener('change', updateCurrencyPreview);
        rentInput.addEventListener('input', updateCurrencyPreview);

        updateCurrencyPreview();

        if (form && saveButton) {
            form.addEventListener('submit', function () {
                saveButton.disabled = true;

                saveButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                    'Saving...';
            });
        }
    });
</script>

@endpush
