@extends('layouts.app')

@section('title', 'Owner Details')
@section('page-heading', 'Owner Details')

@push('styles')
<style>
    .owner-details-page {
        --owner-border: #e9ecef;
        --owner-primary: var(--bs-primary, #0d6efd);
    }

    .owner-profile-header {
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .owner-profile-header::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -90px;
        top: -110px;
        border-radius: 50%;
        background: rgba(13, 110, 253, .06);
        pointer-events: none;
    }

    .owner-avatar {
        width: 64px;
        height: 64px;
        flex: 0 0 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #4dabf7);
        color: #fff;
        font-size: 1.25rem;
        font-weight: 700;
        box-shadow: 0 .5rem 1.2rem rgba(13, 110, 253, .18);
    }

    .owner-profile-name {
        color: #172033;
        letter-spacing: -.025em;
    }

    .owner-profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .owner-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .38rem .7rem;
        border: 1px solid #dfe5ec;
        border-radius: 999px;
        background: rgba(255,255,255,.8);
        color: #667085;
        font-size: .75rem;
        font-weight: 600;
    }

    .owner-card {
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 .25rem 1rem rgba(20, 35, 55, .045);
        overflow: hidden;
    }

    .owner-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--owner-border);
    }

    .owner-section-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        background: rgba(13, 110, 253, .09);
        color: var(--owner-primary);
        font-size: 1.05rem;
    }

    .owner-card-body {
        padding: 1.35rem;
    }

    .owner-summary-card {
        height: 100%;
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 .25rem 1rem rgba(20, 35, 55, .035);
        overflow: hidden;
    }

    .owner-summary-card.primary {
        border-top: 3px solid #0d6efd;
    }

    .owner-summary-card.success {
        border-top: 3px solid #198754;
    }

    .owner-summary-card.info {
        border-top: 3px solid #0dcaf0;
    }

    .owner-summary-body {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1.15rem;
    }

    .owner-summary-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .8rem;
        font-size: 1.1rem;
    }

    .owner-summary-icon.primary {
        background: rgba(13, 110, 253, .1);
        color: #0d6efd;
    }

    .owner-summary-icon.success {
        background: rgba(25, 135, 84, .1);
        color: #198754;
    }

    .owner-summary-icon.info {
        background: rgba(13, 202, 240, .1);
        color: #087990;
    }

    .owner-summary-value {
        color: #172033;
        font-weight: 700;
        line-height: 1.2;
    }

    .owner-summary-label {
        margin-top: .2rem;
        color: #8a94a6;
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .owner-detail-item {
        height: 100%;
        padding: .9rem;
        border: 1px solid #edf0f3;
        border-radius: .75rem;
        background: #fafbfc;
    }

    .owner-detail-label {
        margin-bottom: .3rem;
        color: #8a94a6;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .owner-detail-value {
        color: #344054;
        font-size: .88rem;
        font-weight: 600;
        word-break: break-word;
    }

    .owner-detail-value a {
        color: inherit;
    }

    .owner-detail-value a:hover {
        color: var(--owner-primary);
    }

    .owner-notes {
        padding: 1rem;
        border-radius: .75rem;
        background: #f8f9fa;
        color: #667085;
        white-space: pre-line;
        font-size: .87rem;
        line-height: 1.6;
    }

    .owner-table-wrap {
        overflow-x: auto;
    }

    .owner-table {
        min-width: 900px;
        margin-bottom: 0;
    }

    .owner-table thead th {
        padding-top: .9rem;
        padding-bottom: .9rem;
        border-bottom: 1px solid #e9ecef;
        background: #fafbfc;
        color: #7a8594;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .owner-table tbody td {
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-color: #f0f2f4;
    }

    .property-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .7rem;
        background: rgba(13, 110, 253, .08);
        color: var(--owner-primary);
    }

    .property-name {
        color: #172033;
        font-weight: 650;
    }

    .property-name:hover {
        color: var(--owner-primary);
    }

    .property-address {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .65rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
    }

    .status-active {
        background: rgba(25, 135, 84, .1);
        color: #198754;
    }

    .status-maintenance {
        background: rgba(255, 193, 7, .14);
        color: #997404;
    }

    .status-inactive {
        background: #f0f2f4;
        color: #667085;
    }

    .owner-empty {
        padding: 4rem 1.5rem;
        text-align: center;
    }

    .owner-empty-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border-radius: 1rem;
        background: rgba(13, 110, 253, .08);
        color: var(--owner-primary);
        font-size: 1.6rem;
    }

    .owner-btn {
        min-height: 42px;
        border-radius: .7rem;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .owner-profile-header {
            padding: 1.1rem;
        }

        .owner-card-body {
            padding: 1rem;
        }

        .owner-card-header {
            padding: 1rem;
        }

        .owner-profile-actions {
            width: 100%;
        }

        .owner-profile-actions .btn {
            flex: 1;
        }
    }

    html[data-theme="dark"] .owner-profile-header,
    html[data-theme="dark"] .owner-card,
    html[data-theme="dark"] .owner-summary-card {
        background: #151922;
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-profile-header {
        background: linear-gradient(135deg, #151922 0%, #111827 100%);
    }

    html[data-theme="dark"] .owner-profile-name,
    html[data-theme="dark"] .owner-summary-value,
    html[data-theme="dark"] .property-name,
    html[data-theme="dark"] .owner-detail-value {
        color: #f1f5f9;
    }

    html[data-theme="dark"] .owner-meta-badge {
        background: #111827;
        border-color: #343c4a;
        color: #aeb8c6;
    }

    html[data-theme="dark"] .owner-detail-item,
    html[data-theme="dark"] .owner-notes,
    html[data-theme="dark"] .owner-table thead th {
        background: #11161f;
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-table thead th {
        color: #8f9bad;
    }

    html[data-theme="dark"] .owner-table tbody td {
        border-color: #252c38;
    }

    html[data-theme="dark"] .status-inactive {
        background: #29303d;
        color: #aeb8c6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid owner-details-page px-0">

    {{-- Profile Header --}}
    <div class="owner-profile-header mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 position-relative">

            <div class="d-flex align-items-center gap-3">
                @php
                    $initials = collect(
                        preg_split('/\s+/', trim($owner->full_name))
                    )
                    ->filter()
                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                    ->take(2)
                    ->implode('');
                @endphp

                <div class="owner-avatar">
                    {{ $initials ?: '?' }}
                </div>

                <div>
                    <div class="small text-primary fw-semibold mb-1">
                        <i class="bi bi-buildings me-1"></i>
                        Property Management / Owner Profile
                    </div>

                    <h1 class="h3 mb-1 fw-bold owner-profile-name">
                        {{ $owner->full_name }}
                    </h1>

                    <div class="owner-profile-meta">
                        <span class="owner-meta-badge">
                            <i class="bi bi-hash"></i>
                            Owner #{{ $owner->id }}
                        </span>

                        <span class="owner-meta-badge">
                            <i class="bi bi-buildings"></i>
                            {{ $owner->properties->count() }}
                            {{ $owner->properties->count() === 1 ? 'Property' : 'Properties' }}
                        </span>

                        @if($owner->created_at)
                            <span class="owner-meta-badge">
                                <i class="bi bi-calendar3"></i>
                                Since {{ $owner->created_at->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 owner-profile-actions">
                <a href="{{ route('owners.edit', $owner) }}"
                   class="btn btn-warning owner-btn">
                    <i class="bi bi-pencil me-1"></i>
                    Edit Owner
                </a>

                <a href="{{ route('owners.index') }}"
                   class="btn btn-outline-secondary owner-btn">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Owners
                </a>
            </div>

        </div>
    </div>

    {{-- Portfolio Summary --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="owner-summary-card primary">
                <div class="owner-summary-body">
                    <div class="owner-summary-icon primary">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="owner-summary-value text-truncate">
                            {{ $owner->full_name }}
                        </div>
                        <div class="owner-summary-label">
                            Property Owner
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="owner-summary-card success">
                <div class="owner-summary-body">
                    <div class="owner-summary-icon success">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <div>
                        <div class="owner-summary-value fs-4">
                            {{ $owner->properties->count() }}
                        </div>
                        <div class="owner-summary-label">
                            Properties Owned
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="owner-summary-card info">
                <div class="owner-summary-body">
                    <div class="owner-summary-icon info">
                        <i class="bi bi-door-open"></i>
                    </div>

                    <div>
                        <div class="owner-summary-value fs-4">
                            {{ $owner->properties->sum('total_units') }}
                        </div>
                        <div class="owner-summary-label">
                            Total Portfolio Units
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Owner Information --}}
    <div class="owner-card mb-4">
        <div class="owner-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="owner-section-icon">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        Owner Information
                    </h5>
                    <small class="text-muted">
                        Identification and direct contact information
                    </small>
                </div>
            </div>

            <span class="badge rounded-pill text-bg-light border">
                <i class="bi bi-person-check me-1"></i>
                Owner Profile
            </span>
        </div>

        <div class="owner-card-body">
            <div class="row g-3">

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">Full Name</div>
                        <div class="owner-detail-value">
                            {{ $owner->full_name }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">
                            National ID / Passport Number
                        </div>
                        <div class="owner-detail-value">
                            {{ $owner->national_id ?: 'Not provided' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">
                            Identity Document / Photo
                        </div>
                        <div class="owner-detail-value mt-1">
                            @if($owner->id_document_path)
                                @php
                                    $isImage = in_array(strtolower(pathinfo($owner->id_document_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                @if($isImage)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($owner->id_document_path) }}" target="_blank">
                                            <img src="{{ Storage::url($owner->id_document_path) }}" alt="ID Photo" class="img-thumbnail" style="max-height: 100px; border-radius: 0.5rem;">
                                        </a>
                                    </div>
                                @endif
                                <a href="{{ Storage::url($owner->id_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2">
                                    <i class="bi bi-eye me-1"></i> View / Download Document
                                </a>
                            @else
                                <span class="text-muted">No document uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">Phone Number</div>
                        <div class="owner-detail-value">
                            @if($owner->phone)
                                <a href="tel:{{ $owner->phone }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-telephone me-1 text-primary"></i>
                                    {{ $owner->phone }}
                                </a>
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">Email Address</div>
                        <div class="owner-detail-value">
                            @if($owner->email)
                                <a href="mailto:{{ $owner->email }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-envelope me-1 text-primary"></i>
                                    {{ $owner->email }}
                                </a>
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">Address</div>
                        <div class="owner-detail-value">
                            {{ $owner->address ?: 'Not provided' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="owner-detail-item">
                        <div class="owner-detail-label">Member Since</div>
                        <div class="owner-detail-value">
                            {{ $owner->created_at ? $owner->created_at->format('d M Y') : '—' }}
                        </div>
                    </div>
                </div>

                @if($owner->notes)
                    <div class="col-12">
                        <div class="owner-detail-item">
                            <div class="owner-detail-label">Management Notes</div>
                            <div class="owner-notes">
                                {{ $owner->notes }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Owner Portal Access --}}
    <div class="owner-card mb-4">
        <div class="owner-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="owner-section-icon">
                    <i class="bi bi-person-lock"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        Owner Portal Access
                    </h5>
                    <small class="text-muted">
                        Portal login account and security status
                    </small>
                </div>
            </div>

            @if($owner->user)
                @if($owner->user->is_active)
                    <span class="badge rounded-pill text-bg-success border">
                        <i class="bi bi-check-circle me-1"></i>
                        Portal Active
                    </span>
                @else
                    <span class="badge rounded-pill text-bg-danger border">
                        <i class="bi bi-x-circle me-1"></i>
                        Portal Deactivated
                    </span>
                @endif
            @else
                <span class="badge rounded-pill text-bg-warning border">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    No Portal Account
                </span>
            @endif
        </div>

        <div class="owner-card-body">
            @if($owner->user)
                <div class="p-3 border rounded-3 bg-light-subtle">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <div class="owner-detail-label">Login Email</div>
                            <div class="owner-detail-value">{{ $owner->user->email }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="owner-detail-label">Account Status</div>
                            <div class="owner-detail-value">
                                @if($owner->user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Deactivated</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="owner-detail-label">Account Created</div>
                            <div class="owner-detail-value">
                                {{ $owner->user->created_at ? $owner->user->created_at->format('d M Y, H:i') : '—' }}
                            </div>
                        </div>
                        <div class="col-md-3 text-md-end">
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('owners.toggle-portal', $owner) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $owner->user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} owner-btn"
                                            onclick="return confirm('Are you sure you want to {{ $owner->user->is_active ? 'deactivate' : 'activate' }} portal access for {{ $owner->full_name }}?')">
                                        <i class="bi {{ $owner->user->is_active ? 'bi-person-x' : 'bi-person-check' }} me-1"></i>
                                        {{ $owner->user->is_active ? 'Deactivate Portal' : 'Activate Portal' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 p-3 border rounded-3 bg-light-subtle">
                    <div>
                        <h6 class="fw-bold mb-1">Create Owner Portal Account</h6>
                        <p class="small text-muted mb-0">
                            Create a login account for this owner so they can log into their dedicated Owner Portal.
                        </p>
                    </div>

                    @if(auth()->user()->isAdmin())
                        @if($owner->email)
                            <form action="{{ route('owners.portal-account', $owner) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary owner-btn text-nowrap">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Create Portal Account
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-secondary owner-btn text-nowrap" disabled title="Add an email address to this owner to enable portal account creation">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                Email Required
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Properties --}}
    <div class="owner-card mb-4">
        <div class="owner-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="owner-section-icon">
                    <i class="bi bi-buildings"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        Properties Owned
                    </h5>
                    <small class="text-muted">
                        Properties registered under this owner
                    </small>
                </div>
            </div>

            <span class="badge rounded-pill text-bg-light border">
                {{ $owner->properties->count() }}
                {{ $owner->properties->count() === 1 ? 'Property' : 'Properties' }}
            </span>
        </div>

        <div class="owner-table-wrap">
            @if($owner->properties->count())

                <table class="table align-middle owner-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Property</th>
                            <th>Type</th>
                            <th>City</th>
                            <th>Units</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($owner->properties as $property)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="property-icon">
                                            <i class="bi bi-building"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <a href="{{ route('properties.show', $property) }}"
                                               class="property-name text-decoration-none">
                                                {{ $property->name }}
                                            </a>

                                            @if($property->address)
                                                <div class="small text-muted property-address">
                                                    {{ $property->address }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $property->propertyType->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $property->city ?: '—' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $property->total_units }}
                                    </span>
                                </td>

                                <td>
                                    @if($property->status === 'active')
                                        <span class="status-badge status-active">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Active
                                        </span>
                                    @elseif($property->status === 'maintenance')
                                        <span class="status-badge status-maintenance">
                                            <i class="bi bi-tools"></i>
                                            Maintenance
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bi bi-pause-circle"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('properties.show', $property) }}"
                                       class="btn btn-sm btn-outline-secondary owner-btn">
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else

                <div class="owner-empty">
                    <div class="owner-empty-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <h5 class="fw-bold mb-1">
                        No Properties Yet
                    </h5>

                    <p class="text-muted mb-0">
                        No properties have been linked to this owner yet.
                    </p>
                </div>

            @endif
        </div>
    </div>

</div>
@endsection