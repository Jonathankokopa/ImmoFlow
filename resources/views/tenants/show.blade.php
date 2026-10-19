@extends('layouts.app')

@section('title', 'Tenant Details')
@section('page-heading', 'Tenant Details')

@push('styles')

<style>
    .tenant-page {
        max-width: 1400px;
    }

    .tenant-eyebrow {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.4rem;
    }

    .tenant-header {
        padding: 0.25rem 0;
    }

    .tenant-avatar-large {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 1rem;
        background: linear-gradient(135deg, #0f172a, #334155);
        color: #fff;
        font-size: 1.1rem;
        font-weight: 750;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
    }

    .tenant-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.045);
        overflow: hidden;
    }

    .tenant-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.3rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .tenant-card-body {
        padding: 1.3rem;
    }

    .tenant-detail-label {
        color: #64748b;
        font-size: 0.74rem;
        font-weight: 650;
        margin-bottom: 0.25rem;
    }

    .tenant-detail-value {
        color: #0f172a;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .tenant-detail-value a {
        color: inherit;
        text-decoration: none;
    }

    .tenant-detail-value a:hover {
        color: #0d9488;
    }

    .tenant-detail-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.7rem;
        background: #e6fffb;
        color: #0f766e;
        flex: 0 0 auto;
    }

    .tenant-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.7rem;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .tenant-status-active {
        background: #e9f8ef;
        color: #198754;
    }

    .tenant-status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .tenant-portal-success {
        border: 1px solid #bbf7d0;
        border-radius: 0.85rem;
        background: #f0fdf4;
        color: #166534;
    }

    .tenant-portal-warning {
        border: 1px solid #fde68a;
        border-radius: 0.85rem;
        background: #fffbeb;
    }

    .tenant-portal-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        flex: 0 0 auto;
    }

    .tenant-portal-icon-success {
        background: #dcfce7;
        color: #15803d;
    }

    .tenant-portal-icon-warning {
        background: #fef3c7;
        color: #a16207;
    }

    .tenant-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.38rem 0.65rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .tenant-badge-success {
        background: #e9f8ef;
        color: #198754;
    }

    .tenant-badge-warning {
        background: #fff4df;
        color: #a16207;
    }

    .tenant-lease {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .tenant-lease:hover {
        border-color: #cbd5e1;
        box-shadow: 0 5px 16px rgba(15, 23, 42, 0.04);
    }

    .tenant-lease-label {
        color: #64748b;
        font-size: 0.73rem;
        font-weight: 650;
        margin-bottom: 0.25rem;
    }

    .tenant-lease-value {
        color: #0f172a;
        font-weight: 650;
    }

    .tenant-btn {
        min-height: 42px;
        border-radius: 0.7rem;
        font-weight: 650;
    }

    .tenant-btn-primary {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border: none;
    }

    .tenant-btn-primary:hover {
        background: linear-gradient(135deg, #0f766e, #115e59);
    }

    .tenant-empty {
        padding: 2.5rem 1rem;
        text-align: center;
        color: #64748b;
    }

    .tenant-empty-icon {
        width: 58px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        border-radius: 1rem;
        background: #f1f5f9;
        color: #64748b;
        font-size: 1.4rem;
    }

    @media (max-width: 767.98px) {
        .tenant-header-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .tenant-header-actions .btn {
            flex: 1 1 auto;
        }

        .tenant-card-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 1rem;
        }

        .tenant-card-body {
            padding: 1rem;
        }
    }

    html[data-theme="dark"] .tenant-eyebrow {
        color: #94a3b8;
    }

    html[data-theme="dark"] .tenant-card,
    html[data-theme="dark"] .tenant-lease {
        background: #111827;
        border-color: #273449;
        box-shadow: none;
    }

    html[data-theme="dark"] .tenant-card-header {
        background: #172033;
        border-color: #273449;
    }

    html[data-theme="dark"] .tenant-detail-value,
    html[data-theme="dark"] .tenant-lease-value {
        color: #f8fafc;
    }

    html[data-theme="dark"] .tenant-detail-icon {
        background: rgba(13, 148, 136, 0.14);
        color: #5eead4;
    }

    html[data-theme="dark"] .tenant-status-active {
        background: rgba(25, 135, 84, 0.16);
        color: #75d9a1;
    }

    html[data-theme="dark"] .tenant-status-inactive {
        background: #1e293b;
        color: #94a3b8;
    }

    html[data-theme="dark"] .tenant-portal-success {
        background: rgba(22, 101, 52, 0.16);
        border-color: rgba(74, 222, 128, 0.2);
        color: #bbf7d0;
    }

    html[data-theme="dark"] .tenant-portal-warning {
        background: rgba(161, 98, 7, 0.14);
        border-color: rgba(245, 158, 11, 0.2);
    }

    html[data-theme="dark"] .tenant-portal-icon-success {
        background: rgba(34, 197, 94, 0.14);
        color: #86efac;
    }

    html[data-theme="dark"] .tenant-portal-icon-warning {
        background: rgba(245, 158, 11, 0.14);
        color: #fbbf24;
    }

    html[data-theme="dark"] .tenant-badge-success {
        background: rgba(25, 135, 84, 0.16);
        color: #75d9a1;
    }

    html[data-theme="dark"] .tenant-badge-warning {
        background: rgba(161, 98, 7, 0.16);
        color: #fbbf24;
    }

    html[data-theme="dark"] .tenant-empty-icon {
        background: #1e293b;
        color: #94a3b8;
    }
</style>

@endpush

@section('content')
@php
$tenantName = $tenant->full_name ?? $tenant->name ?? 'Tenant';

$initials = collect(
    preg_split('/\s+/', trim($tenantName))
)
->filter()
->map(fn ($part) => strtoupper(substr($part, 0, 1)))
->take(2)
->implode('');

@endphp

<div class="container-fluid tenant-page px-0">

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>
            <i class="bi bi-exclamation-circle me-1"></i>
            Please fix the following:
        </strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 tenant-header">

    <div class="d-flex align-items-center gap-3">

        <div class="tenant-avatar-large">
            {{ $initials ?: 'T' }}
        </div>

        <div>
            <div class="tenant-eyebrow">
                Property Management / Tenants / Details
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <h1 class="h3 mb-0 fw-bold">
                    {{ $tenantName }}
                </h1>

                @if($tenant->status === 'active')
                    <span class="tenant-status tenant-status-active">
                        <i class="bi bi-check-circle"></i>
                        Active Lease
                    </span>
                @else
                    <span class="tenant-status tenant-status-inactive">
                        <i class="bi bi-pause-circle"></i>
                        Inactive
                    </span>
                @endif
            </div>

            <p class="text-muted mb-0 mt-1">
                Tenant profile, portal access, and tenancy records.
            </p>
        </div>

    </div>

    <div class="d-flex gap-2 tenant-header-actions">

        <a
            href="{{ route('tenants.index') }}"
            class="btn btn-outline-secondary tenant-btn"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

        <a
            href="{{ route('tenants.edit', $tenant) }}"
            class="btn btn-primary tenant-btn tenant-btn-primary"
        >
            <i class="bi bi-pencil me-1"></i>
            Edit Tenant
        </a>

    </div>

</div>

{{-- Tenant Information --}}
<div class="tenant-card mb-4">

    <div class="tenant-card-header">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-person-vcard me-2 text-primary"></i>
                Tenant Information
            </h5>

            <small class="text-muted">
                Contact and identification details
            </small>
        </div>
    </div>

    <div class="tenant-card-body">

        <div class="row g-4">

            {{-- Full Name --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-person"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Full Name
                        </div>

                        <div class="tenant-detail-value">
                            {{ $tenantName }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- National ID --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-card-text"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            National ID / Passport Number
                        </div>

                        <div class="tenant-detail-value">
                            {{ $tenant->national_id ?: 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ID / Passport Document --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-file-earmark-person"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Identity Document / Photo
                        </div>

                        <div class="tenant-detail-value mt-1">
                            @if($tenant->id_document_path)
                                @php
                                    $isImage = in_array(strtolower(pathinfo($tenant->id_document_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                @if($isImage)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($tenant->id_document_path) }}" target="_blank">
                                            <img src="{{ Storage::url($tenant->id_document_path) }}" alt="ID Photo" class="img-thumbnail" style="max-height: 120px; border-radius: 0.5rem;">
                                        </a>
                                    </div>
                                @endif
                                <a href="{{ Storage::url($tenant->id_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2">
                                    <i class="bi bi-eye me-1"></i> View / Download Document
                                </a>
                            @else
                                <span class="text-muted">No document uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-telephone"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Phone
                        </div>

                        <div class="tenant-detail-value">
                            @if($tenant->phone)
                                <a href="tel:{{ $tenant->phone }}">
                                    {{ $tenant->phone }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-envelope"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Email
                        </div>

                        <div class="tenant-detail-value">
                            @if($tenant->email)
                                <a href="mailto:{{ $tenant->email }}">
                                    {{ $tenant->email }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-geo-alt"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Address
                        </div>

                        <div class="tenant-detail-value">
                            {{ $tenant->address ?: 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-activity"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Tenancy Status
                        </div>

                        @if($tenant->status === 'active')
                            <span class="tenant-status tenant-status-active">
                                <i class="bi bi-check-circle"></i>
                                Active
                            </span>
                        @else
                            <span class="tenant-status tenant-status-inactive">
                                <i class="bi bi-pause-circle"></i>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-person-heart"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Emergency Contact
                        </div>

                        <div class="tenant-detail-value">
                            {{ $tenant->emergency_contact_name ?: 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Phone --}}
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <span class="tenant-detail-icon">
                        <i class="bi bi-telephone-forward"></i>
                    </span>

                    <div>
                        <div class="tenant-detail-label">
                            Emergency Phone
                        </div>

                        <div class="tenant-detail-value">
                            @if($tenant->emergency_contact_phone)
                                <a href="tel:{{ $tenant->emergency_contact_phone }}">
                                    {{ $tenant->emergency_contact_phone }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Portal Access --}}
<div class="tenant-card mb-4">

    <div class="tenant-card-header">

        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-person-lock me-2 text-primary"></i>
                Tenant Portal Access
            </h5>

            <small class="text-muted">
                Portal login and account status
            </small>
        </div>

        @if($tenant->user)
            <span class="tenant-badge tenant-badge-success">
                <i class="bi bi-check-circle"></i>
                Account Created
            </span>
        @else
            <span class="tenant-badge tenant-badge-warning">
                <i class="bi bi-exclamation-circle"></i>
                No Portal Account
            </span>
        @endif

    </div>

    <div class="tenant-card-body">

        @if($tenant->user)

            <div class="tenant-portal-success p-3">

                <div class="d-flex gap-3">

                    <div class="tenant-portal-icon tenant-portal-icon-success">
                        <i class="bi bi-person-check-fill fs-5"></i>
                    </div>

                    <div class="flex-grow-1">

                        <h6 class="fw-bold mb-1">
                            Portal Account Active
                        </h6>

                        <p class="small mb-3">
                            This tenant has a portal login account.
                        </p>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <div class="tenant-detail-label">
                                    Login Email
                                </div>

                                <div class="fw-semibold">
                                    {{ $tenant->user->email }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="tenant-detail-label">
                                    Account Role
                                </div>

                                <div class="fw-semibold">
                                    {{ ucwords(str_replace('_', ' ', $tenant->user->role)) }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="tenant-detail-label">
                                    Account Status
                                </div>

                                @if($tenant->user->is_active)
                                    <span class="tenant-status tenant-status-active mb-2 d-inline-block">
                                        <i class="bi bi-check-circle"></i>
                                        Active
                                    </span>
                                    @if(auth()->user()->hasRole(['administrator', 'owner', 'property_manager']))
                                        <form method="POST" action="{{ route('tenants.toggle-portal', $tenant) }}" class="mt-1 d-block">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-person-x me-1"></i> Deactivate Access
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="tenant-status tenant-status-inactive mb-2 d-inline-block">
                                        <i class="bi bi-slash-circle"></i>
                                        Disabled
                                    </span>
                                    @if(auth()->user()->hasRole(['administrator', 'owner', 'property_manager']))
                                        <form method="POST" action="{{ route('tenants.toggle-portal', $tenant) }}" class="mt-1 d-block">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-person-check me-1"></i> Activate Access
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @else

            <div class="tenant-portal-warning p-3">

                <div class="row align-items-center g-4">

                    <div class="col-lg-8">

                        <div class="d-flex gap-3">

                            <div class="tenant-portal-icon tenant-portal-icon-warning">
                                <i class="bi bi-person-lock fs-5"></i>
                            </div>

                            <div>

                                <h6 class="fw-bold mb-1">
                                    No portal account has been created.
                                </h6>

                                <p class="text-muted small mb-0">
                                    Create a portal account for this tenant.
                                    A secure password setup link will be sent
                                    to the tenant's email address.
                                </p>

                                @if(!$tenant->email)
                                    <div class="alert alert-warning mt-3 mb-0 py-2 small">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        This tenant does not have an email address.
                                        Add an email address before creating the portal account.
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 text-lg-end">

                        @if(auth()->user()->hasRole(['administrator', 'owner', 'property_manager', 'reception_staff']))

                            @if($tenant->email)

                                <form
                                    method="POST"
                                    action="{{ route('tenants.portal-account', $tenant) }}"
                                    class="portal-account-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-success tenant-btn"
                                    >
                                        <i class="bi bi-person-check me-1"></i>
                                        Create Portal Account
                                    </button>
                                </form>

                            @else

                                <button
                                    type="button"
                                    class="btn btn-secondary tenant-btn"
                                    disabled
                                >
                                    <i class="bi bi-envelope-x me-1"></i>
                                    Email Required
                                </button>

                            @endif

                        @else

                            <div class="text-muted small">
                                <i class="bi bi-lock me-1"></i>
                                Authorized staff only can create portal accounts.
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endif

    </div>
</div>

{{-- Tenancies --}}
<div class="tenant-card mb-4">

    <div class="tenant-card-header">

        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-house-door me-2 text-primary"></i>
                Tenancy Records
            </h5>

            <small class="text-muted">
                Properties and units associated with this tenant
            </small>
        </div>

        <span class="tenant-badge tenant-badge-success">
            {{ $tenant->leases->count() }}
            {{ $tenant->leases->count() === 1 ? 'Lease' : 'Leases' }}
        </span>

    </div>

    <div class="tenant-card-body">

        @forelse($tenant->leases as $lease)

            <div class="tenant-lease mb-3">

                <div class="row g-4">

                    {{-- Property --}}
                    <div class="col-md-6">

                        <div class="tenant-lease-label">
                            Property
                        </div>

                        <div class="tenant-lease-value">
                            {{ $lease->unit?->property?->name ?? 'N/A' }}
                        </div>

                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6">

                        <div class="tenant-lease-label">
                            Unit
                        </div>

                        <div class="tenant-lease-value">
                            {{ $lease->unit?->name ?? $lease->unit?->unit_number ?? 'N/A' }}
                        </div>

                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-6">

                        <div class="tenant-lease-label">
                            Start Date
                        </div>

                        <div class="tenant-lease-value">
                            @if($lease->start_date)
                                {{ \Illuminate\Support\Carbon::parse($lease->start_date)->format('d M Y') }}
                            @else
                                N/A
                            @endif
                        </div>

                    </div>

                    {{-- End Date --}}
                    <div class="col-md-6">

                        <div class="tenant-lease-label">
                            End Date
                        </div>

                        <div class="tenant-lease-value">
                            @if($lease->end_date)
                                {{ \Illuminate\Support\Carbon::parse($lease->end_date)->format('d M Y') }}
                            @else
                                N/A
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="tenant-empty">

                <div class="tenant-empty-icon">
                    <i class="bi bi-house-x"></i>
                </div>

                <div class="fw-semibold mb-1">
                    No Tenancy Records
                </div>

                <div class="small">
                    This tenant currently has no tenancy records.
                </div>

            </div>

        @endforelse

    </div>
</div>

</div>
@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.portal-account-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const confirmed = window.confirm(
                    'Create a portal account for this tenant and send the password setup link to their email address?'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                const button = form.querySelector('button[type="submit"]');

                if (button) {
                    button.disabled = true;
                    button.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Creating Account...';
                }
            });
        });
    });
</script>

@endpush
