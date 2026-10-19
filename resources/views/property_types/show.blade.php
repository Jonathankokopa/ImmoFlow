@extends('layouts.app')

@section('title', 'Property Type Details')
@section('page-heading', 'Property Type Details')

@push('styles')

<style>
    .pm-page {
        max-width: 1400px;
    }

    .pm-header {
        padding: 0.25rem 0;
    }

    .pm-eyebrow {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.4rem;
    }

    .pm-header h1 {
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .pm-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .pm-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .pm-card-body {
        padding: 1.35rem;
    }

    .pm-card-body-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .pm-icon-chip {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 0.8rem;
        font-size: 1.1rem;
    }

    .pm-chip-primary {
        background: #e8f1ff;
        color: #0d6efd;
    }

    .pm-chip-teal {
        background: #e6fffb;
        color: #087f75;
    }

    .pm-chip-info {
        background: #e8f5ff;
        color: #0b6fa4;
    }

    .pm-chip-navy {
        background: #eef2f7;
        color: #334155;
    }

    .pm-metric-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .pm-metric-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
    }

    .pm-accent-teal {
        border-top: 3px solid #0f9d8a;
    }

    .pm-accent-info {
        border-top: 3px solid #0d6efd;
    }

    .pm-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .pm-tag-good {
        background: #e9f8ef;
        color: #198754;
    }

    .pm-tag-navy {
        background: #eef2f7;
        color: #475569;
    }

    .pm-tag-warn {
        background: #fff4df;
        color: #a16207;
    }

    .pm-btn-outline,
    .pm-btn-outline-warning {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0.6rem 0.9rem;
        border-radius: 0.7rem;
        font-weight: 600;
    }

    .pm-btn-outline {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #334155;
    }

    .pm-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .pm-btn-outline-warning {
        border: 1px solid #f0c36d;
        background: #fffaf0;
        color: #9a6700;
    }

    .pm-btn-outline-warning:hover {
        background: #fff3d6;
        border-color: #e4ad45;
        color: #7c5200;
    }

    .pm-detail-value {
        color: #0f172a;
        font-weight: 600;
        line-height: 1.5;
    }

    .pm-description {
        padding: 1rem;
        border-radius: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        line-height: 1.7;
    }

    .pm-table {
        min-width: 850px;
    }

    .pm-table thead th {
        padding-top: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .pm-table tbody td {
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
        border-color: #eef2f7;
        color: #334155;
    }

    .pm-table tbody tr:hover {
        background: #f8fafc;
    }

    .pm-property-name {
        color: #0f172a;
        font-weight: 650;
    }

    .pm-property-name:hover {
        color: #0d6efd;
    }

    .pm-empty {
        padding: 4rem 1.5rem;
        text-align: center;
    }

    .pm-empty-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border-radius: 1rem;
        background: #eef2f7;
        color: #64748b;
        font-size: 1.6rem;
    }

    @media (max-width: 767.98px) {
        .pm-card-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 1rem;
        }

        .pm-card-body {
            padding: 1rem;
        }

        .pm-header .d-flex {
            width: 100%;
            flex-wrap: wrap;
        }

        .pm-header .btn {
            flex: 1 1 auto;
        }

        .pm-card-body-header {
            align-items: flex-start;
        }
    }

    html[data-theme="dark"] .pm-eyebrow {
        color: #94a3b8;
    }

    html[data-theme="dark"] .pm-header h1,
    html[data-theme="dark"] .pm-metric-value,
    html[data-theme="dark"] .pm-detail-value,
    html[data-theme="dark"] .pm-property-name {
        color: #f8fafc;
    }

    html[data-theme="dark"] .pm-card {
        background: #111827;
        border-color: #273449;
        box-shadow: none;
    }

    html[data-theme="dark"] .pm-card-header,
    html[data-theme="dark"] .pm-table thead th {
        background: #172033;
        border-color: #273449;
    }

    html[data-theme="dark"] .pm-table tbody td {
        border-color: #273449;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-table tbody tr:hover {
        background: #172033;
    }

    html[data-theme="dark"] .pm-description {
        background: #172033;
        border-color: #334155;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-chip-primary {
        background: rgba(13, 110, 253, 0.16);
        color: #8ab4ff;
    }

    html[data-theme="dark"] .pm-chip-teal {
        background: rgba(15, 157, 138, 0.16);
        color: #5eead4;
    }

    html[data-theme="dark"] .pm-chip-info {
        background: rgba(13, 110, 253, 0.14);
        color: #7dd3fc;
    }

    html[data-theme="dark"] .pm-chip-navy,
    html[data-theme="dark"] .pm-tag-navy,
    html[data-theme="dark"] .pm-empty-icon {
        background: #1e293b;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-tag-good {
        background: rgba(25, 135, 84, 0.16);
        color: #75d9a1;
    }

    html[data-theme="dark"] .pm-tag-warn {
        background: rgba(161, 98, 7, 0.18);
        color: #fbbf24;
    }

    html[data-theme="dark"] .pm-btn-outline {
        background: #172033;
        border-color: #475569;
        color: #e2e8f0;
    }

    html[data-theme="dark"] .pm-btn-outline:hover {
        background: #1e293b;
        border-color: #64748b;
        color: #fff;
    }

    html[data-theme="dark"] .pm-btn-outline-warning {
        background: rgba(161, 98, 7, 0.12);
        border-color: rgba(245, 158, 11, 0.4);
        color: #fbbf24;
    }

    html[data-theme="dark"] .pm-btn-outline-warning:hover {
        background: rgba(161, 98, 7, 0.2);
        border-color: rgba(245, 158, 11, 0.6);
        color: #fcd34d;
    }
</style>

@endpush

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 pm-header">
    <div>
        <div class="pm-eyebrow">
            Property Management / Property Types / Details
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="h3 mb-1 fw-bold">
                {{ $propertyType->name }}
            </h1>

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
        </div>

        <p class="text-muted mb-0">
            View classification details and properties assigned to this category.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a
            href="{{ route('property-types.edit', $propertyType) }}"
            class="btn pm-btn-outline-warning"
        >
            <i class="bi bi-pencil me-1"></i>
            Edit Type
        </a>

        <a
            href="{{ route('property-types.index') }}"
            class="btn pm-btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Types
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

    {{-- Property Type --}}
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="pm-card h-100">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-primary">
                        <i class="bi bi-tag"></i>
                    </span>

                    <div class="min-w-0">
                        <div class="pm-metric-value text-truncate">
                            {{ $propertyType->name }}
                        </div>

                        <div class="pm-metric-label">
                            Property Type
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Properties Count --}}
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="pm-card h-100 pm-accent-teal">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-teal">
                        <i class="bi bi-buildings"></i>
                    </span>

                    <div>
                        <div class="pm-metric-value">
                            {{ $propertyType->properties->count() }}
                        </div>

                        <div class="pm-metric-label">
                            Properties Using Type
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="col-12 col-lg-4">
        <div class="pm-card h-100 pm-accent-info">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-info">
                        <i class="bi bi-check2-circle"></i>
                    </span>

                    <div>
                        <div class="pm-metric-value">
                            @if($propertyType->is_active)
                                <span class="text-success">
                                    Active Category
                                </span>
                            @else
                                <span class="text-secondary">
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <div class="pm-metric-label">
                            Operational Status
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Property Type Information --}}
<div class="pm-card mb-4">
    <div class="pm-card-header">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-info-circle me-2 text-primary"></i>
                Property Type Information
            </h5>

            <small class="text-muted">
                Configuration and description
            </small>
        </div>
    </div>

    <div class="pm-card-body">
        <div class="row g-4">

            {{-- Type Name --}}
            <div class="col-md-6">
                <div class="pm-metric-label mb-1">
                    Type Name
                </div>

                <div class="pm-detail-value">
                    {{ $propertyType->name }}
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <div class="pm-metric-label mb-1">
                    Operational Status
                </div>

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
            </div>

            {{-- Description --}}
            <div class="col-12">
                <div class="pm-metric-label mb-2">
                    Description
                </div>

                <div class="pm-description">
                    {{ $propertyType->description ?: 'No description provided.' }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Properties Using This Type --}}
<div class="pm-card">

    <div class="pm-card-header">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-buildings me-2 text-primary"></i>
                Properties Using This Type
            </h5>

            <small class="text-muted">
                Registered properties assigned under this classification
            </small>
        </div>

        @php
            $propertyCount = $propertyType->properties->count();
        @endphp

        <span class="pm-tag pm-tag-navy">
            {{ $propertyCount }}
            {{ $propertyCount === 1 ? 'Property' : 'Properties' }}
        </span>
    </div>

    <div class="pm-card-body p-0">

        @if($propertyCount > 0)

            <div class="table-responsive">
                <table class="table pm-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Property</th>
                            <th>Owner</th>
                            <th>City</th>
                            <th>Units</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($propertyType->properties as $property)
                            <tr>

                                {{-- Display row number, not database ID --}}
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Property --}}
                                <td>
                                    <div class="d-flex align-items-center">

                                        <div
                                            class="pm-icon-chip pm-chip-navy p-2 me-2"
                                            style="width: 38px; height: 38px; font-size: 0.95rem;"
                                        >
                                            <i class="bi bi-building"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <a
                                                href="{{ route('properties.show', $property) }}"
                                                class="pm-property-name text-decoration-none d-block text-truncate"
                                                style="max-width: 260px;"
                                            >
                                                {{ $property->name }}
                                            </a>

                                            @if($property->address)
                                                <div
                                                    class="small text-muted text-truncate"
                                                    style="max-width: 260px;"
                                                >
                                                    {{ $property->address }}
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </td>

                                {{-- Owner --}}
                                <td>
                                    {{ $property->owner->full_name ?? 'N/A' }}
                                </td>

                                {{-- City --}}
                                <td>
                                    {{ $property->city ?: 'N/A' }}
                                </td>

                                {{-- Units --}}
                                <td>
                                    <span class="fw-semibold">
                                        {{ $property->total_units }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($property->status === 'active')
                                        <span class="pm-tag pm-tag-good">
                                            Active
                                        </span>
                                    @elseif($property->status === 'maintenance')
                                        <span class="pm-tag pm-tag-warn">
                                            Maintenance
                                        </span>
                                    @else
                                        <span class="pm-tag pm-tag-navy">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-end pe-4">
                                    <a
                                        href="{{ route('properties.show', $property) }}"
                                        class="btn btn-sm pm-btn-outline"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @else

            <div class="pm-empty">
                <div class="pm-empty-icon">
                    <i class="bi bi-buildings"></i>
                </div>

                <h5 class="fw-bold mb-1">
                    No Properties Found
                </h5>

                <p class="text-muted mb-0">
                    No properties have been assigned to this category yet.
                </p>
            </div>

        @endif

    </div>
</div>

</div>
@endsection
