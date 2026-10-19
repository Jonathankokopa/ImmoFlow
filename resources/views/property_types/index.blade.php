@extends('layouts.app')

@section('title', 'Property Types')
@section('page-heading', 'Property Types')

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="pm-header mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">

        <div>
            <div class="pm-eyebrow">
                <i class="bi bi-tags me-1"></i>
                Property Management
            </div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Property Types
            </h1>

            <p class="text-muted mb-0">
                Manage property classifications, categories, and operational status.
            </p>
        </div>

        <a href="{{ route('property-types.create') }}"
           class="btn pm-btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Property Type
        </a>

    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

    {{-- Total --}}
    <div class="col-6 col-xl-4">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-navy">
                <i class="bi bi-tags"></i>
            </div>

            <div>
                <div class="pm-stat-value">
                    {{ $propertyTypes->count() }}
                </div>

                <div class="pm-stat-label">
                    Total Types
                </div>
            </div>

        </div>
    </div>

    {{-- Active --}}
    <div class="col-6 col-xl-4">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-teal">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <div class="pm-stat-value">
                    {{ $propertyTypes->where('is_active', true)->count() }}
                </div>

                <div class="pm-stat-label">
                    Active Types
                </div>
            </div>

        </div>
    </div>

    {{-- Inactive --}}
    <div class="col-12 col-xl-4">
        <div class="pm-stat-card h-100">

            <div class="pm-stat-icon pm-stat-icon-warning">
                <i class="bi bi-pause-circle"></i>
            </div>

            <div>
                <div class="pm-stat-value">
                    {{ $propertyTypes->where('is_active', false)->count() }}
                </div>

                <div class="pm-stat-label">
                    Inactive Types
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Directory --}}
<div class="pm-card">

    <div class="pm-card-header">

        <div class="d-flex align-items-center gap-3">

            <div class="pm-section-icon">
                <i class="bi bi-tags"></i>
            </div>

            <div>
                <h5 class="mb-0 fw-bold">
                    Property Types Directory
                </h5>

                <small class="text-muted">
                    Registered classification categories
                </small>
            </div>

        </div>

        <span class="pm-tag pm-tag-navy">
            <i class="bi bi-tags me-1"></i>
            {{ $propertyTypes->count() }}
            {{ $propertyTypes->count() === 1 ? 'Type' : 'Types' }}
        </span>

    </div>

    <div class="pm-card-body p-0">

        @if($propertyTypes->count())

            <div class="table-responsive">

                <table class="table pm-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Property Type</th>
                            <th>Description</th>
                            <th>Properties</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($propertyTypes as $propertyType)

                            <tr>

                                {{-- Row Number --}}
                                <td class="ps-4">
                                    <span class="pm-row-number">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                {{-- Property Type --}}
                                <td>

                                    <div class="d-flex align-items-center type-identity">

                                        <div class="type-icon">
                                            <i class="bi bi-tag"></i>
                                        </div>

                                        <div class="min-w-0">

                                            <a href="{{ route('property-types.show', $propertyType) }}"
                                               class="type-name text-decoration-none">
                                                {{ $propertyType->name }}
                                            </a>

                                            <div class="small text-muted">
                                                Type #{{ $propertyType->id }}
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                {{-- Description --}}
                                <td>

                                    @if($propertyType->description)

                                        <div class="description-cell"
                                             title="{{ $propertyType->description }}">
                                            {{ $propertyType->description }}
                                        </div>

                                    @else

                                        <span class="text-muted">
                                            No description
                                        </span>

                                    @endif

                                </td>

                                {{-- Properties --}}
                                <td>

                                    <span class="property-count-badge">
                                        <i class="bi bi-buildings me-1"></i>
                                        {{ $propertyType->properties->count() }}
                                    </span>

                                </td>

                                {{-- Status --}}
                                <td>

                                    @if($propertyType->is_active)

                                        <span class="pm-tag pm-tag-good">
                                            <i class="bi bi-check-circle"></i>
                                            Active
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

                                        <a href="{{ route('property-types.show', $propertyType) }}"
                                           class="btn btn-sm pm-action-btn pm-action-view"
                                           title="View Property Type"
                                           aria-label="View {{ $propertyType->name }}">
                                            <i class="bi bi-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">View</span>
                                        </a>

                                        <a href="{{ route('property-types.edit', $propertyType) }}"
                                           class="btn btn-sm pm-action-btn pm-action-edit"
                                           title="Edit Property Type"
                                           aria-label="Edit {{ $propertyType->name }}">
                                            <i class="bi bi-pencil"></i>
                                            <span class="d-none d-xl-inline ms-1">Edit</span>
                                        </a>

                                        <form action="{{ route('property-types.destroy', $propertyType) }}"
                                              method="POST"
                                              class="property-type-delete-form d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm pm-action-btn pm-action-delete"
                                                    title="Delete Property Type"
                                                    aria-label="Delete {{ $propertyType->name }}">
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
            <div class="property-type-empty">

                <div class="property-type-empty-icon">
                    <i class="bi bi-tags"></i>
                </div>

                <h4 class="fw-bold text-slate-900 mb-2">
                    No Property Types Found
                </h4>

                <p class="text-muted mb-4">
                    No property categories have been established yet.
                    Create your first property type to organize your portfolio.
                </p>

                <a href="{{ route('property-types.create') }}"
                   class="btn pm-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Your First Property Type
                </a>

            </div>

        @endif

    </div>
</div>

</div>

@endsection

@push('styles')

<style>
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

    .pm-stat-value {
        color: #0f172a;
        font-size: 1.55rem;
        line-height: 1.15;
        font-weight: 800;
    }

    .pm-stat-label {
        margin-top: 3px;
        color: #64748b;
        font-size: .82rem;
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

    .type-identity {
        min-width: 190px;
    }

    .type-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        margin-right: 12px;
        border: 1px solid #e0e7ff;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
        font-size: 1rem;
    }

    .type-name {
        display: block;
        max-width: 220px;
        overflow: hidden;
        color: #0f172a;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .type-name:hover {
        color: #2563eb;
    }

    .description-cell {
        max-width: 320px;
        overflow: hidden;
        color: #64748b;
        font-size: .9rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .property-count-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #f8fafc;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
        white-space: nowrap;
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

    .property-type-empty {
        max-width: 600px;
        margin: 0 auto;
        padding: 70px 24px;
        text-align: center;
    }

    .property-type-empty-icon {
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

        .type-name {
            max-width: 170px;
        }
    }

    html[data-theme="dark"] .pm-stat-card,
    html[data-theme="dark"] .pm-card {
        background: #111827;
        border-color: #1f2937;
    }

    html[data-theme="dark"] .pm-stat-value,
    html[data-theme="dark"] .type-name,
    html[data-theme="dark"] .text-slate-900 {
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .pm-section-icon,
    html[data-theme="dark"] .type-icon,
    html[data-theme="dark"] .property-type-empty-icon {
        background: #312e81;
        border-color: #4338ca;
        color: #c7d2fe;
    }

    html[data-theme="dark"] .property-count-badge {
        background: #1f2937;
        border-color: #374151;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .description-cell {
        color: #94a3b8;
    }

    html[data-theme="dark"] .type-name:hover {
        color: #93c5fd !important;
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

    document.querySelectorAll('.property-type-delete-form').forEach(function (form) {

        form.addEventListener('submit', function (event) {

            const confirmed = window.confirm(
                'Are you sure you want to delete this property type? This action cannot be undone.'
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    });

});
</script>

@endpush
