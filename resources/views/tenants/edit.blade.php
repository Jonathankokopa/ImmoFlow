@extends('layouts.app')

@section('title', 'Edit Tenant')

@section('page-heading', 'Edit Tenant')

@push('styles')

<style>
    .tenant-page {
        --tenant-primary: #2563eb;
        --tenant-border: #e5e7eb;
        --tenant-muted: #64748b;
        --tenant-surface: #ffffff;
        --tenant-soft: #f8fafc;
    }

    .tenant-header {
        padding: 1.5rem;
        border: 1px solid var(--tenant-border);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
    }

    .tenant-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        margin-bottom: .45rem;
        color: var(--tenant-primary);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .tenant-eyebrow::before {
        content: "";
        width: 22px;
        height: 2px;
        border-radius: 99px;
        background: currentColor;
    }

    .tenant-identity {
        display: inline-flex;
        align-items: center;
        gap: .7rem;
        margin-top: .9rem;
        padding: .5rem .7rem;
        border: 1px solid var(--tenant-border);
        border-radius: .75rem;
        background: rgba(248, 250, 252, .85);
    }

    .tenant-avatar {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .65rem;
        background: rgba(37, 99, 235, .1);
        color: var(--tenant-primary);
        font-size: .78rem;
        font-weight: 800;
    }

    .tenant-card {
        overflow: hidden;
        border: 1px solid var(--tenant-border);
        border-radius: 1rem;
        background: var(--tenant-surface);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .tenant-card-header {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--tenant-border);
        background: var(--tenant-soft);
    }

    .tenant-section-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        background: rgba(37, 99, 235, .1);
        color: var(--tenant-primary);
        font-size: 1.1rem;
    }

    .tenant-card-body {
        padding: 1.35rem;
    }

    .tenant-label {
        display: block;
        margin-bottom: .45rem;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
    }

    .tenant-label .required {
        color: #dc2626;
    }

    .tenant-page .form-control,
    .tenant-page .form-select {
        min-height: 44px;
        border-color: #dbe1e8;
        border-radius: .65rem;
        box-shadow: none;
    }

    .tenant-page textarea.form-control {
        min-height: 105px;
        resize: vertical;
    }

    .tenant-page .form-control:focus,
    .tenant-page .form-select:focus {
        border-color: rgba(37, 99, 235, .65);
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
    }

    .tenant-help {
        display: block;
        margin-top: .4rem;
        color: var(--tenant-muted);
        font-size: .76rem;
        line-height: 1.5;
    }

    .tenant-status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .6rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
    }

    .tenant-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .tenant-status.inactive {
        background: #f1f5f9;
        color: #475569;
    }

    .tenant-action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        padding: 1rem;
        border: 1px solid var(--tenant-border);
        border-radius: 1rem;
        background: var(--tenant-soft);
    }

    .tenant-btn-primary {
        border: 0;
        border-radius: .65rem;
        background: var(--tenant-primary);
        color: #fff;
        font-weight: 700;
        padding: .65rem 1rem;
        transition: .2s ease;
    }

    .tenant-btn-primary:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
    }

    .tenant-btn-outline {
        border: 1px solid #cbd5e1;
        border-radius: .65rem;
        background: #fff;
        color: #334155;
        font-weight: 600;
        padding: .65rem 1rem;
    }

    .tenant-btn-outline:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #0f172a;
    }

    @media (max-width: 767.98px) {
        .tenant-header {
            padding: 1.15rem;
        }

        .tenant-card-header,
        .tenant-card-body {
            padding: 1rem;
        }

        .tenant-action-bar .btn {
            width: 100%;
        }
    }

    html[data-theme="dark"] .tenant-header,
    html[data-theme="dark"] .tenant-card,
    html[data-theme="dark"] .tenant-action-bar,
    html[data-theme="dark"] .tenant-identity {
        --tenant-border: #334155;
        --tenant-surface: #111827;
        --tenant-soft: #1e293b;
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .tenant-header {
        background: linear-gradient(135deg, #111827 0%, #172033 100%);
    }

    html[data-theme="dark"] .tenant-card-header,
    html[data-theme="dark"] .tenant-action-bar {
        background: #1e293b;
    }

    html[data-theme="dark"] .tenant-identity {
        background: #1e293b;
    }

    html[data-theme="dark"] .tenant-label {
        color: #e2e8f0;
    }

    html[data-theme="dark"] .tenant-page .form-control,
    html[data-theme="dark"] .tenant-page .form-select {
        border-color: #475569;
        background-color: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme="dark"] .tenant-page .form-control::placeholder {
        color: #64748b;
    }

    html[data-theme="dark"] .tenant-btn-outline {
        border-color: #475569;
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme="dark"] .tenant-btn-outline:hover {
        background: #1e293b;
        color: #fff;
    }

    html[data-theme="dark"] .tenant-status.active {
        background: rgba(22, 101, 52, .25);
        color: #86efac;
    }

    html[data-theme="dark"] .tenant-status.inactive {
        background: #334155;
        color: #cbd5e1;
    }
</style>

@endpush

@section('content')

<div class="container-fluid tenant-page px-0">

@php
    $tenantName = trim($tenant->full_name ?? 'Tenant');

    $initials = collect(
        preg_split('/\s+/', $tenantName)
    )
    ->filter()
    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
    ->take(2)
    ->implode('');

    $initials = $initials ?: 'TN';

    $currentStatus = old('status', $tenant->status);
@endphp

{{-- Page Header --}}
<div class="tenant-header mb-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">

        <div>
            <div class="tenant-eyebrow">
                Tenancy / Edit #{{ $tenant->id }}
            </div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Edit Tenant
            </h1>

            <p class="text-muted mb-0">
                Update the tenant profile, contact details, emergency information, or status.
            </p>

            <div class="tenant-identity">
                <span class="tenant-avatar">{{ $initials }}</span>

                <div>
                    <div class="fw-semibold">{{ $tenant->full_name }}</div>

                    <small class="text-muted">
                        Tenant #{{ $tenant->id }}
                    </small>
                </div>

                <span class="tenant-status {{ $currentStatus === 'active' ? 'active' : 'inactive' }}">
                    <i class="bi bi-circle-fill" style="font-size:.42rem;"></i>
                    {{ ucfirst($currentStatus) }}
                </span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a
                href="{{ route('tenants.show', $tenant) }}"
                class="btn tenant-btn-outline"
            >
                <i class="bi bi-eye me-1"></i>
                View Tenant
            </a>

            <a
                href="{{ route('tenants.index') }}"
                class="btn tenant-btn-outline"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Tenants
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <div class="d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>

            <div>
                <strong>Please review the following:</strong>

                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('tenants.update', $tenant) }}" method="POST" id="tenantEditForm">
    @csrf
    @method('PUT')

    {{-- Personal Information --}}
    <div class="tenant-card mb-4">
        <div class="tenant-card-header">
            <div class="tenant-section-icon">
                <i class="bi bi-person"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-1">Personal Information</h5>
                <small class="text-muted">
                    Tenant identity and primary contact channels
                </small>
            </div>
        </div>

        <div class="tenant-card-body">
            <div class="row g-3">

                {{-- Full Name --}}
                <div class="col-md-6">
                    <label for="full_name" class="tenant-label">
                        Full Name <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        id="full_name"
                        class="form-control @error('full_name') is-invalid @enderror"
                        value="{{ old('full_name', $tenant->full_name) }}"
                        autocomplete="name"
                        required
                    >

                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- National ID --}}
                <div class="col-md-6">
                    <label for="national_id" class="tenant-label">
                        National ID / Passport
                    </label>

                    <input
                        type="text"
                        name="national_id"
                        id="national_id"
                        class="form-control @error('national_id') is-invalid @enderror"
                        value="{{ old('national_id', $tenant->national_id) }}"
                    >

                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <label for="phone" class="tenant-label">
                        Phone Number <span class="required">*</span>
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $tenant->phone) }}"
                        autocomplete="tel"
                        required
                    >

                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="email" class="tenant-label">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $tenant->email) }}"
                        autocomplete="email"
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <span class="tenant-help">
                        Keep this address current if the tenant has or will receive portal access.
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- Contact & Emergency Details --}}
    <div class="tenant-card mb-4">
        <div class="tenant-card-header">
            <div class="tenant-section-icon">
                <i class="bi bi-geo-alt"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-1">Contact & Emergency Details</h5>
                <small class="text-muted">
                    Address, emergency contact, and tenant status
                </small>
            </div>
        </div>

        <div class="tenant-card-body">
            <div class="row g-3">

                {{-- Address --}}
                <div class="col-12">
                    <label for="address" class="tenant-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        id="address"
                        class="form-control @error('address') is-invalid @enderror"
                        rows="3"
                        placeholder="Residential or postal address"
                    >{{ old('address', $tenant->address) }}</textarea>

                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Emergency Contact Name --}}
                <div class="col-md-6">
                    <label for="emergency_contact_name" class="tenant-label">
                        Emergency Contact Name
                    </label>

                    <input
                        type="text"
                        name="emergency_contact_name"
                        id="emergency_contact_name"
                        class="form-control @error('emergency_contact_name') is-invalid @enderror"
                        value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                        autocomplete="off"
                    >

                    @error('emergency_contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Emergency Contact Phone --}}
                <div class="col-md-6">
                    <label for="emergency_contact_phone" class="tenant-label">
                        Emergency Contact Phone
                    </label>

                    <input
                        type="tel"
                        name="emergency_contact_phone"
                        id="emergency_contact_phone"
                        class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                        value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}"
                        autocomplete="off"
                    >

                    @error('emergency_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label for="status" class="tenant-label">
                        Status <span class="required">*</span>
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="active" @selected($currentStatus === 'active')}>
                            Active
                        </option>

                        <option value="inactive" @selected($currentStatus === 'inactive')}>
                            Inactive
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <span class="tenant-help">
                        This controls whether the tenant is treated as an active tenant profile.
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="tenant-action-bar mb-4">
        <button type="submit" class="btn tenant-btn-primary" id="updateTenantBtn">
            <i class="bi bi-check-lg me-1"></i>
            <span>Update Tenant</span>
        </button>

        <a
            href="{{ route('tenants.show', $tenant) }}"
            class="btn tenant-btn-outline"
        >
            Cancel
        </a>
    </div>

</form>

</div>
@endsection

@push('scripts')

<script>
    document.getElementById('tenantEditForm')?.addEventListener('submit', function () {
        const button = document.getElementById('updateTenantBtn');

        if (!button) {
            return;
        }

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Updating Tenant...';
    });
</script>

@endpush
