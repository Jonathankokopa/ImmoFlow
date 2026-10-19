@extends('layouts.app')

@section('title', 'Edit Unit')
@section('page-heading', 'Edit Unit')

@section('content')

@php
    use App\Support\Currency;

    $currentCurrency = old('currency', $unit->currency);
    $currentStatus = old('status', $unit->status);
@endphp

@push('styles')
    <style>
        .unit-edit-page {
            --ue-border: #e7ebf0;
            --ue-dark: #172033;
            --ue-muted: #64748b;
        }

        .ue-eyebrow {
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

        .ue-eyebrow::before {
            content: '';
            width: 24px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .ue-title {
            color: var(--ue-dark);
            letter-spacing: -.025em;
        }

        .ue-card {
            border: 1px solid var(--ue-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .ue-card-header {
            padding: 1.2rem 1.35rem;
            border-bottom: 1px solid var(--ue-border);
            background: linear-gradient(to right, #fff, #f8fafc);
        }

        .ue-section {
            padding: 1.35rem;
        }

        .ue-section + .ue-section {
            border-top: 1px solid var(--ue-border);
        }

        .ue-section-heading {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: 1.15rem;
        }

        .ue-section-icon {
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

        .ue-section-title {
            color: var(--ue-dark);
            font-size: .95rem;
            font-weight: 800;
            margin: 0;
        }

        .ue-section-description {
            color: var(--ue-muted);
            font-size: .78rem;
            margin: .15rem 0 0;
        }

        .ue-label {
            color: #334155;
            font-size: .82rem;
            font-weight: 700;
            margin-bottom: .45rem;
        }

        .ue-control {
            min-height: 44px;
            border-radius: 10px;
            border-color: #dbe2ea;
        }

        .ue-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .1);
        }

        .ue-required {
            color: #dc3545;
        }

        .ue-help {
            color: #94a3b8;
            font-size: .74rem;
            margin-top: .35rem;
        }

        .ue-currency-alert {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .9rem 1rem;
            margin-bottom: 1.15rem;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            background: #f0f9ff;
            color: #0c4a6e;
        }

        .ue-currency-icon {
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

        .ue-currency-preview {
            display: none;
            margin-top: .65rem;
            padding: .65rem .8rem;
            border-radius: 9px;
            background: #f8fafc;
            color: #64748b;
            font-size: .75rem;
        }

        .ue-actions {
            padding: 1.2rem 1.35rem;
            border-top: 1px solid var(--ue-border);
            background: #fafbfc;
        }

        .ue-btn {
            border-radius: 10px;
            font-weight: 700;
            padding: .62rem 1rem;
        }

        .ue-submit {
            min-width: 140px;
        }

        .ue-side-card {
            border: 1px solid var(--ue-border);
            border-radius: 14px;
            background: #fff;
            padding: 1rem;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
        }

        .ue-side-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #f1f5f9;
            color: #475569;
        }

        .ue-side-title {
            color: var(--ue-dark);
            font-size: .82rem;
            font-weight: 800;
        }

        .ue-side-text {
            color: #64748b;
            font-size: .76rem;
            line-height: 1.55;
        }

        html[data-theme="dark"] .ue-title,
        html[data-theme="dark"] .ue-section-title,
        html[data-theme="dark"] .ue-side-title {
            color: #f8fafc !important;
        }

        html[data-theme="dark"] .ue-card,
        html[data-theme="dark"] .ue-side-card {
            background: #111827;
            border-color: #263244;
            box-shadow: none;
        }

        html[data-theme="dark"] .ue-card-header,
        html[data-theme="dark"] .ue-actions {
            background: #172033;
            border-color: #263244;
        }

        html[data-theme="dark"] .ue-section + .ue-section {
            border-color: #263244;
        }

        html[data-theme="dark"] .ue-label {
            color: #cbd5e1;
        }

        html[data-theme="dark"] .ue-control {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        html[data-theme="dark"] .ue-control::placeholder {
            color: #64748b;
        }

        html[data-theme="dark"] .ue-help,
        html[data-theme="dark"] .ue-side-text {
            color: #94a3b8;
        }

        html[data-theme="dark"] .ue-section-icon,
        html[data-theme="dark"] .ue-side-icon {
            background: #1e293b;
            color: #93c5fd;
        }

        html[data-theme="dark"] .ue-currency-alert {
            background: #082f49;
            border-color: #075985;
            color: #bae6fd;
        }

        html[data-theme="dark"] .ue-currency-icon {
            background: #0c4a6e;
            color: #bae6fd;
        }

        html[data-theme="dark"] .ue-currency-preview {
            background: #172033;
            color: #94a3b8;
        }
    </style>
@endpush

<div class="container-fluid unit-edit-page px-0">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">

        <div>
            <div class="ue-eyebrow">
                Units / Edit #{{ $unit->id }}
            </div>

            <h1 class="h3 mb-1 fw-bold ue-title">
                Edit Unit
            </h1>

            <p class="text-muted mb-0">
                Update the information for unit
                <strong>{{ $unit->unit_number }}</strong>.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a
                href="{{ route('units.show', $unit) }}"
                class="btn btn-outline-secondary ue-btn"
            >
                <i class="bi bi-eye me-1"></i>
                View Unit
            </a>

            <a
                href="{{ route('units.index') }}"
                class="btn btn-outline-secondary ue-btn"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Units
            </a>

        </div>

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

            <div class="ue-card">

                <div class="ue-card-header">

                    <div class="d-flex align-items-center gap-2">

                        <span class="ue-section-icon">
                            <i class="bi bi-pencil-square"></i>
                        </span>

                        <div>
                            <h5 class="mb-1 fw-bold ue-title">
                                Unit Information
                            </h5>

                            <small class="text-muted">
                                Update the property's unit, rental, and occupancy details.
                            </small>
                        </div>

                    </div>

                </div>

                <form
                    action="{{ route('units.update', $unit) }}"
                    method="POST"
                    id="unitEditForm"
                >
                    @csrf
                    @method('PUT')

                    {{-- Property & Unit --}}
                    <div class="ue-section">

                        <div class="ue-section-heading">

                            <span class="ue-section-icon">
                                <i class="bi bi-building"></i>
                            </span>

                            <div>
                                <h6 class="ue-section-title">
                                    Property & Unit Details
                                </h6>

                                <p class="ue-section-description">
                                    Update the unit's property assignment and physical specifications.
                                </p>
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-8">
                                <label for="property_id" class="form-label ue-label">
                                    Property
                                    <span class="ue-required">*</span>
                                </label>

                                <select
                                    name="property_id"
                                    id="property_id"
                                    class="form-select ue-control @error('property_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Select Property --</option>

                                    @foreach($properties as $property)
                                        <option
                                            value="{{ $property->id }}"
                                            @selected(old('property_id', $unit->property_id) == $property->id)
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

                            <div class="col-md-4">
                                <label for="unit_number" class="form-label ue-label">
                                    Unit Number
                                    <span class="ue-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="unit_number"
                                    id="unit_number"
                                    class="form-control ue-control @error('unit_number') is-invalid @enderror"
                                    value="{{ old('unit_number', $unit->unit_number) }}"
                                    placeholder="e.g. A101"
                                    required
                                >

                                @error('unit_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="floor" class="form-label ue-label">
                                    Floor
                                </label>

                                <input
                                    type="number"
                                    name="floor"
                                    id="floor"
                                    class="form-control ue-control @error('floor') is-invalid @enderror"
                                    value="{{ old('floor', $unit->floor) }}"
                                    min="0"
                                    placeholder="e.g. 1"
                                >

                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="bedrooms" class="form-label ue-label">
                                    Bedrooms
                                    <span class="ue-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="bedrooms"
                                    id="bedrooms"
                                    class="form-control ue-control @error('bedrooms') is-invalid @enderror"
                                    value="{{ old('bedrooms', $unit->bedrooms) }}"
                                    min="0"
                                    required
                                >

                                @error('bedrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="bathrooms" class="form-label ue-label">
                                    Bathrooms
                                    <span class="ue-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="bathrooms"
                                    id="bathrooms"
                                    class="form-control ue-control @error('bathrooms') is-invalid @enderror"
                                    value="{{ old('bathrooms', $unit->bathrooms) }}"
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
                    <div class="ue-section">

                        <div class="ue-section-heading">

                            <span class="ue-section-icon">
                                <i class="bi bi-cash-stack"></i>
                            </span>

                            <div>
                                <h6 class="ue-section-title">
                                    Rental Information
                                </h6>

                                <p class="ue-section-description">
                                    Manage the unit's standard monthly rent and currency.
                                </p>
                            </div>

                        </div>

                        <div class="ue-currency-alert">

                            <span class="ue-currency-icon">
                                <i class="bi bi-info-circle"></i>
                            </span>

                            <div>
                                <strong>Unit Currency</strong>

                                <div class="small mt-1">
                                    This currency belongs to the unit and is inherited
                                    by new leases and rent invoices created from this unit.
                                </div>
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="monthly_rent" class="form-label ue-label">
                                    Monthly Rent
                                    <span class="ue-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="monthly_rent"
                                    id="monthly_rent"
                                    class="form-control ue-control @error('monthly_rent') is-invalid @enderror"
                                    value="{{ old('monthly_rent', $unit->monthly_rent) }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="e.g. 500.00"
                                    required
                                >

                                <div class="ue-help">
                                    Enter the standard monthly rental amount.
                                </div>

                                @error('monthly_rent')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="currency" class="form-label ue-label">
                                    Currency
                                    <span class="ue-required">*</span>
                                </label>

                                <select
                                    name="currency"
                                    id="currency"
                                    class="form-select ue-control @error('currency') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Select Currency --</option>

                                    @foreach(Currency::SUPPORTED as $currency)
                                        <option
                                            value="{{ $currency }}"
                                            @selected($currentCurrency === $currency)
                                        >
                                            {{ $currency }} — {{ Currency::name($currency) }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="ue-help">
                                    Supported currencies: USD, KES, and CDF.
                                </div>

                                <div id="currencyPreview" class="ue-currency-preview">
                                    <i class="bi bi-wallet2 me-1"></i>
                                    <span id="currencyPreviewText"></span>
                                </div>

                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label ue-label">
                                    Status
                                    <span class="ue-required">*</span>
                                </label>

                                <select
                                    name="status"
                                    id="status"
                                    class="form-select ue-control @error('status') is-invalid @enderror"
                                    required
                                >
                                    <option value="vacant" @selected($currentStatus === 'vacant')>
                                        Vacant
                                    </option>

                                    <option value="occupied" @selected($currentStatus === 'occupied')>
                                        Occupied
                                    </option>

                                    <option value="reserved" @selected($currentStatus === 'reserved')>
                                        Reserved
                                    </option>

                                    <option value="maintenance" @selected($currentStatus === 'maintenance')>
                                        Maintenance
                                    </option>
                                </select>

                                <div class="ue-help">
                                    Choose the unit's current occupancy or availability status.
                                </div>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="ue-actions d-flex flex-wrap justify-content-between align-items-center gap-2">

                        <div class="small text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Changes will be saved to this unit.
                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <a
                                href="{{ route('units.show', $unit) }}"
                                class="btn btn-outline-secondary ue-btn"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary ue-btn ue-submit"
                                id="updateUnitBtn"
                            >
                                <i class="bi bi-check-lg me-1"></i>
                                Update Unit
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-xl-3">

            <div class="ue-side-card mb-3">

                <div class="d-flex gap-3">

                    <span class="ue-side-icon">
                        <i class="bi bi-door-open"></i>
                    </span>

                    <div>
                        <div class="ue-side-title mb-1">
                            Current Unit
                        </div>

                        <div class="ue-side-text">
                            <strong>{{ $unit->unit_number }}</strong>
                            is assigned to
                            <strong>{{ $unit->property->name ?? 'No Property' }}</strong>.
                        </div>
                    </div>

                </div>

            </div>

            <div class="ue-side-card mb-3">

                <div class="d-flex gap-3">

                    <span class="ue-side-icon">
                        <i class="bi bi-currency-exchange"></i>
                    </span>

                    <div>
                        <div class="ue-side-title mb-1">
                            Currency
                        </div>

                        <div class="ue-side-text">
                            Current unit currency:
                            <strong>{{ $unit->currency }}</strong>
                            — {{ Currency::name($unit->currency) }}.
                        </div>
                    </div>

                </div>

            </div>

            <div class="ue-side-card">

                <div class="d-flex gap-3">

                    <span class="ue-side-icon">
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <div>
                        <div class="ue-side-title mb-1">
                            Historical Records
                        </div>

                        <div class="ue-side-text">
                            Existing invoices and payments retain their
                            recorded currency even if the unit is updated.
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
const form = document.getElementById('unitEditForm');
const updateButton = document.getElementById('updateUnitBtn');
const currencySelect = document.getElementById('currency');
const rentInput = document.getElementById('monthly_rent');
const preview = document.getElementById('currencyPreview');
const previewText = document.getElementById('currencyPreviewText');

```
        const currencySymbols = {
            USD: '$',
            KES: 'KSh',
            CDF: 'FC'
        };

        function updatePreview() {
            const currency = currencySelect.value;

            if (!currency) {
                preview.style.display = 'none';
                return;
            }

            const amount = Number(rentInput.value || 0);
            const symbol = currencySymbols[currency] ?? currency;

            const formattedAmount = amount.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            previewText.textContent =
                `Current rental: ${symbol} ${formattedAmount} ${currency}`;

            preview.style.display = 'block';
        }

        currencySelect.addEventListener('change', updatePreview);
        rentInput.addEventListener('input', updatePreview);

        updatePreview();

        if (form && updateButton) {
            form.addEventListener('submit', function () {
                updateButton.disabled = true;

                updateButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                    'Updating...';
            });
        }
    });
</script>

@endpush
