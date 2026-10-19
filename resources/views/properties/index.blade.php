@extends('layouts.app')

@section('title', 'Properties')
@section('page-heading', 'Properties')

@section('content')

<div class="container-fluid pm-page px-0">

```
{{-- Alerts --}}
@if(session('success'))
    <div class="alert pm-alert pm-alert-success alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <span class="pm-alert-icon">
                <i class="bi bi-check-lg"></i>
            </span>

            <div>
                <div class="fw-semibold">Success</div>
                <div>{{ session('success') }}</div>
            </div>
        </div>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert pm-alert pm-alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <span class="pm-alert-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </span>

            <div>
                <div class="fw-semibold">Action Required</div>
                <div>{{ session('error') }}</div>
            </div>
        </div>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
@endif

{{-- Page Header --}}
<div class="pm-header mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">

        <div>
            <div class="pm-eyebrow">
                <i class="bi bi-buildings me-1"></i>
                Property Management
            </div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Properties
            </h1>

            <p class="text-muted mb-0">
                Manage and monitor all properties across your portfolio.
            </p>
        </div>

        @if(!auth()->user()->isOwner())
            <a href="{{ route('properties.create') }}"
               class="btn pm-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Property
            </a>
        @endif

    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

    {{-- Total Properties --}}
    <div class="col-6 col-xl-3">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-navy">
                <i class="bi bi-buildings"></i>
            </div>

            <div class="pm-stat-content">
                <div class="pm-stat-value">
                    {{ $properties->count() }}
                </div>

                <div class="pm-stat-label">
                    Total Properties
                </div>
            </div>

        </div>
    </div>

    {{-- Active --}}
    <div class="col-6 col-xl-3">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-teal">
                <i class="bi bi-check-circle"></i>
            </div>

            <div class="pm-stat-content">
                <div class="pm-stat-value">
                    {{ $properties->where('status', 'active')->count() }}
                </div>

                <div class="pm-stat-label">
                    Active
                </div>
            </div>

        </div>
    </div>

    {{-- Maintenance --}}
    <div class="col-6 col-xl-3">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-warning">
                <i class="bi bi-tools"></i>
            </div>

            <div class="pm-stat-content">
                <div class="pm-stat-value">
                    {{ $properties->where('status', 'maintenance')->count() }}
                </div>

                <div class="pm-stat-label">
                    Maintenance
                </div>
            </div>

        </div>
    </div>

    {{-- Units --}}
    <div class="col-6 col-xl-3">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-info">
                <i class="bi bi-door-open"></i>
            </div>

            <div class="pm-stat-content">
                <div class="pm-stat-value">
                    {{ $properties->sum('total_units') }}
                </div>

                <div class="pm-stat-label">
                    Total Units
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Property Directory --}}
<div class="pm-card">

    <div class="pm-card-header">

        <div class="d-flex align-items-center gap-3">

            <div class="pm-section-icon">
                <i class="bi bi-building"></i>
            </div>

            <div>
                <h5 class="mb-0 fw-bold">
                    Property Directory
                </h5>

                <small class="text-muted">
                    All registered properties in your portfolio
                </small>
            </div>

        </div>

        <span class="pm-tag pm-tag-navy">
            <i class="bi bi-buildings me-1"></i>

            {{ $properties->count() }}

            {{ $properties->count() === 1 ? 'Property' : 'Properties' }}
        </span>

    </div>

    <div class="pm-card-body p-0">

        @if($properties->count())

            <div class="table-responsive">

                <table class="table pm-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Property</th>
                            <th>Owner</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Units</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($properties as $property)

                            <tr>

                                {{-- Row Number --}}
                                <td class="ps-4">
                                    <span class="pm-row-number">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                {{-- Property --}}
                                <td>

                                    <div class="d-flex align-items-center property-identity">

                                        @if($property->coverPhoto)

                                            <img
                                                src="{{ asset('storage/' . $property->coverPhoto->path) }}"
                                                alt="{{ $property->name }}"
                                                class="property-thumb"
                                                loading="lazy"
                                            >

                                        @else

                                            <div class="property-thumb property-thumb-placeholder">
                                                <i class="bi bi-building"></i>
                                            </div>

                                        @endif

                                        <div class="min-w-0">

                                            <a href="{{ route('properties.show', $property) }}"
                                               class="property-name text-decoration-none">
                                                {{ $property->name }}
                                            </a>

                                            <div class="small text-muted">
                                                Property #{{ $property->id }}
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                {{-- Owner --}}
                                <td>

                                    @if($property->owner)

                                        @php
                                            $ownerInitials = collect(
                                                preg_split('/\s+/', trim($property->owner->full_name))
                                            )
                                            ->filter()
                                            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                        @endphp

                                        <div class="d-flex align-items-center gap-2">

                                            <span class="owner-mini-avatar">
                                                {{ $ownerInitials }}
                                            </span>

                                            <span class="fw-semibold text-slate-900">
                                                {{ $property->owner->full_name }}
                                            </span>

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            Not assigned
                                        </span>

                                    @endif

                                </td>

                                {{-- Type --}}
                                <td>

                                    @if($property->propertyType)

                                        <span class="property-type">
                                            <i class="bi bi-tag me-1"></i>
                                            {{ $property->propertyType->name }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            N/A
                                        </span>

                                    @endif

                                </td>

                                {{-- Location --}}
                                <td>

                                    <div class="location-cell">

                                        <i class="bi bi-geo-alt"></i>

                                        <span>
                                            {{ $property->city ?: 'N/A' }}
                                        </span>

                                    </div>

                                </td>

                                {{-- Units --}}
                                <td>

                                    <span class="units-badge">
                                        <i class="bi bi-door-open me-1"></i>
                                        {{ $property->total_units }}
                                    </span>

                                </td>

                                {{-- Status --}}
                                <td>

                                    @if($property->status === 'active')

                                        <span class="pm-tag pm-tag-good">
                                            <i class="bi bi-check-circle"></i>
                                            Active
                                        </span>

                                    @elseif($property->status === 'maintenance')

                                        <span class="pm-tag pm-tag-warn">
                                            <i class="bi bi-tools"></i>
                                            Maintenance
                                        </span>

                                    @else

                                        <span class="pm-tag pm-tag-navy">
                                            <i class="bi bi-pause-circle"></i>
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="text-end pe-4">

                                    <div class="d-flex justify-content-end gap-1">

                                        <a href="{{ route('properties.show', $property) }}"
                                           class="btn btn-sm pm-action-btn pm-action-view"
                                           title="View Property"
                                           aria-label="View {{ $property->name }}">
                                            <i class="bi bi-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">View</span>
                                        </a>

                                        <a href="{{ route('properties.edit', $property) }}"
                                           class="btn btn-sm pm-action-btn pm-action-edit"
                                           title="Edit Property"
                                           aria-label="Edit {{ $property->name }}">
                                            <i class="bi bi-pencil"></i>
                                            <span class="d-none d-xl-inline ms-1">Edit</span>
                                        </a>

                                        <form action="{{ route('properties.destroy', $property) }}"
                                              method="POST"
                                              class="property-delete-form d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm pm-action-btn pm-action-delete"
                                                    title="Delete Property"
                                                    aria-label="Delete {{ $property->name }}">
                                                <i class="bi bi-trash"></i>
                                                <span class="d-none d-xl-inline ms-1">Delete</span>
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            {{-- Empty State --}}
            <div class="property-empty-state">

                <div class="property-empty-icon">
                    <i class="bi bi-buildings"></i>
                </div>

                <h4 class="fw-bold text-slate-900 mb-2">
                    No Properties Yet
                </h4>

                <p class="text-muted mb-4">
                    Your property portfolio is currently empty.
                    Add your first property to start managing units,
                    leases and tenants.
                </p>

                @if(!auth()->user()->isOwner())
                    <a href="{{ route('properties.create') }}"
                       class="btn pm-btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Your First Property
                    </a>
                @endif

            </div>

        @endif

    </div>
</div>
```

</div>

@endsection

@push('styles')

<style>
    .pm-alert {
        border: 0;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
    }

    .pm-alert-success {
        background: #ecfdf5;
        color: #065f46;
    }

    .pm-alert-danger {
        background: #fef2f2;
        color: #991b1b;
    }

    .pm-alert-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .65);
    }

    .pm-stat-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    .pm-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
    }

    .pm-stat-card::after {
        content: "";
        position: absolute;
        right: -25px;
        bottom: -35px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(15, 23, 42, .025);
    }

    .pm-stat-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .pm-stat-icon-navy {
        background: #eef2ff;
        color: #3730a3;
    }

    .pm-stat-icon-teal {
        background: #ecfdf5;
        color: #047857;
    }

    .pm-stat-icon-warning {
        background: #fffbeb;
        color: #b45309;
    }

    .pm-stat-icon-info {
        background: #eff6ff;
        color: #2563eb;
    }

    .pm-stat-content {
        min-width: 0;
    }

    .pm-stat-value {
        color: #0f172a;
        font-size: 1.55rem;
        line-height: 1.15;
        font-weight: 800;
    }

    .pm-stat-label {
        color: #64748b;
        font-size: .82rem;
        margin-top: 3px;
    }

    .pm-section-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
        font-size: 1.05rem;
    }

    .pm-row-number {
        color: #94a3b8;
        font-size: .85rem;
        font-weight: 600;
    }

    .property-identity {
        min-width: 220px;
    }

    .property-thumb {
        width: 48px;
        height: 48px;
        min-width: 48px;
        margin-right: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        object-fit: cover;
        display: block;
    }

    .property-thumb-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
        font-size: 1.1rem;
    }

    .property-name {
        display: block;
        max-width: 230px;
        overflow: hidden;
        color: #0f172a;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .property-name:hover {
        color: #2563eb;
    }

    .owner-mini-avatar {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
        font-size: .68rem;
        font-weight: 800;
    }

    .property-type {
        color: #475569;
        font-size: .9rem;
        white-space: nowrap;
    }

    .location-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        white-space: nowrap;
    }

    .location-cell > i {
        color: #94a3b8;
    }

    .units-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
    }

    .pm-action-btn {
        border-radius: 8px;
        transition: all .15s ease;
    }

    .pm-action-btn:hover {
        transform: translateY(-1px);
    }

    .pm-action-edit {
        color: #b45309;
        border-color: #fcd34d;
    }

    .pm-action-edit:hover {
        background: #fffbeb;
        color: #92400e;
    }

    .pm-action-delete {
        color: #dc2626;
        border-color: #fecaca;
    }

    .pm-action-delete:hover {
        background: #fef2f2;
        color: #b91c1c;
    }

    .property-empty-state {
        max-width: 600px;
        margin: 0 auto;
        padding: 70px 24px;
        text-align: center;
    }

    .property-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
        font-size: 1.9rem;
    }

    @media (max-width: 767.98px) {
        .pm-stat-card {
            padding: 16px;
        }

        .pm-stat-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
        }

        .pm-stat-value {
            font-size: 1.3rem;
        }

        .property-name {
            max-width: 170px;
        }

        .pm-card-header {
            align-items: flex-start;
            gap: 12px;
        }
    }

    html[data-theme="dark"] .pm-stat-card,
    html[data-theme="dark"] .pm-card {
        background: #111827;
        border-color: #1f2937;
    }

    html[data-theme="dark"] .pm-stat-value,
    html[data-theme="dark"] .property-name,
    html[data-theme="dark"] .text-slate-900 {
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .property-thumb {
        border-color: #374151;
    }

    html[data-theme="dark"] .property-thumb-placeholder,
    html[data-theme="dark"] .units-badge {
        background: #1f2937;
        border-color: #374151;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .property-name:hover {
        color: #93c5fd !important;
    }

    html[data-theme="dark"] .owner-mini-avatar {
        background: #312e81;
        color: #e0e7ff;
    }

    html[data-theme="dark"] .pm-section-icon,
    html[data-theme="dark"] .property-empty-icon {
        background: #312e81;
        color: #c7d2fe;
    }

    html[data-theme="dark"] .property-type,
    html[data-theme="dark"] .location-cell {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-alert-success {
        background: #052e25;
        color: #a7f3d0;
    }

    html[data-theme="dark"] .pm-alert-danger {
        background: #450a0a;
        color: #fecaca;
    }

    html[data-theme="dark"] .pm-table thead th {
        color: #94a3b8;
        border-color: #1f2937;
    }

    html[data-theme="dark"] .pm-table tbody td {
        border-color: #1f2937;
    }

    html[data-theme="dark"] .pm-action-edit:hover {
        background: #422006;
        color: #fde68a;
    }

    html[data-theme="dark"] .pm-action-delete:hover {
        background: #450a0a;
        color: #fecaca;
    }
</style>

@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.property-delete-form').forEach(function (form) {

        form.addEventListener('submit', function (event) {

            const confirmed = window.confirm(
                'Are you sure you want to delete this property? This action cannot be undone.'
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    });

});
</script>

@endpush
