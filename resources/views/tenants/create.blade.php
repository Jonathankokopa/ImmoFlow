@extends('layouts.app')

@section('title', 'Add Tenant')

@section('page-heading', 'Add Tenant')

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

    .tenant-info {
        border: 1px solid #bfdbfe;
        border-radius: .8rem;
        background: #eff6ff;
        color: #1e3a8a;
    }

    .tenant-info-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .6rem;
        background: rgba(37, 99, 235, .1);
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
    html[data-theme="dark"] .tenant-action-bar {
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

    html[data-theme="dark"] .tenant-info {
        border-color: #1e40af;
        background: rgba(30, 64, 175, .18);
        color: #bfdbfe;
    }
</style>

@endpush

@section('content')

<div class="container-fluid tenant-page px-0">

{{-- Page Header --}}
<div class="tenant-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="tenant-eyebrow">Tenancy / New</div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Add Tenant
            </h1>

            <p class="text-muted mb-0">
                Register a new tenant profile and maintain their contact information.
            </p>
        </div>

        <a href="{{ route('tenants.index') }}" class="btn tenant-btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Tenants
        </a>
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

<form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data" id="tenantCreateForm">
    @csrf

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
                        value="{{ old('full_name') }}"
                        placeholder="e.g. John Doe"
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
                        National ID / Passport Number
                    </label>

                    <input
                        type="text"
                        name="national_id"
                        id="national_id"
                        class="form-control @error('national_id') is-invalid @enderror"
                        value="{{ old('national_id') }}"
                        placeholder="e.g. ID12345678"
                    >

                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ID / Passport Document or Photo --}}
                <div class="col-md-6">
                    <label for="id_document" class="tenant-label">
                        ID / Passport Document or Photo
                    </label>

                    <input
                        type="file"
                        name="id_document"
                        id="id_document"
                        class="form-control @error('id_document') is-invalid @enderror"
                        accept="image/*,.pdf,.doc,.docx"
                    >

                    @error('id_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <span class="tenant-help">
                        Upload photo or document scan of National ID/Passport (JPG, PNG, PDF, max 10MB).
                    </span>
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
                        value="{{ old('phone') }}"
                        placeholder="e.g. +254 700 123456"
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
                        value="{{ old('email') }}"
                        placeholder="e.g. john.doe@example.com"
                        autocomplete="email"
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <span class="tenant-help">
                        An email address is required if this tenant will later receive portal access.
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
                    Address and emergency contact information
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
                    >{{ old('address') }}</textarea>

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
                        value="{{ old('emergency_contact_name') }}"
                        placeholder="e.g. Jane Doe"
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
                        value="{{ old('emergency_contact_phone') }}"
                        placeholder="e.g. +254 700 987654"
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
                        <option value="active" @selected(old('status', 'active') === 'active')}>
                            Active
                        </option>

                        <option value="inactive" @selected(old('status') === 'inactive')}>
                            Inactive
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- Portal Access --}}
    <div class="tenant-card mb-4">
        <div class="tenant-card-header">
            <div class="tenant-section-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-1">Portal Access</h5>
                <small class="text-muted">
                    Tenant login access is created separately
                </small>
            </div>
        </div>

        <div class="tenant-card-body">
            <div class="tenant-info p-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="tenant-info-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <div>
                        <strong>No password is required here.</strong>

                        <div class="small mt-1">
                            Save the tenant profile first. An administrator can then create
                            the tenant's portal account. The tenant can set their password
                            through the secure account setup process.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="tenant-action-bar mb-4">
        <button type="submit" class="btn tenant-btn-primary" id="saveTenantBtn">
            <i class="bi bi-check-lg me-1"></i>
            <span>Save Tenant</span>
        </button>

        <a href="{{ route('tenants.index') }}" class="btn tenant-btn-outline">
            Cancel
        </a>
    </div>

</form>

</div>
@endsection

@push('scripts')

<script>
    document.getElementById('tenantCreateForm')?.addEventListener('submit', function () {
        const button = document.getElementById('saveTenantBtn');

        if (!button) {
            return;
        }

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving Tenant...';
    });
</script>

@endpush
