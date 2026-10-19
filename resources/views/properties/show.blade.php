@extends('layouts.app')

@section('title', 'Property Details')
@section('page-heading', 'Property Details')

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 pm-header">
    <div>
        <div class="pm-eyebrow">
            Properties / Details
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="h3 mb-1 fw-bold text-slate-900">
                {{ $property->name }}
            </h1>

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
        </div>

        <p class="text-muted mb-0">
            View property profile, photos, occupancy status, and registered units.
        </p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a
            href="{{ route('properties.edit', $property) }}"
            class="btn pm-btn-outline-warning"
        >
            <i class="bi bi-pencil me-1"></i>
            Edit Property
        </a>

        <a
            href="{{ route('properties.index') }}"
            class="btn pm-btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Properties
        </a>
    </div>
</div>

@php
    $coverPhoto = $property->photos->firstWhere('is_cover', true)
        ?? $property->photos->first();

    $galleryPhotos = $property->photos->reject(function ($photo) use ($coverPhoto) {
        return $coverPhoto && $photo->id === $coverPhoto->id;
    });
@endphp

{{-- Property Photos --}}
<div class="pm-card mb-4">
    <div class="pm-card-header">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-images me-2 text-primary"></i>
                Property Photos
            </h5>

            <small class="text-muted">
                Photos registered for this property
            </small>
        </div>

        <span class="pm-tag pm-tag-navy">
            {{ $property->photos->count() }}
            {{ $property->photos->count() === 1 ? 'Photo' : 'Photos' }}
        </span>
    </div>

    <div class="pm-card-body">
        @if($property->photos->count())

            @if($coverPhoto)
                <div class="property-gallery-cover mb-3">

                    <a
                        href="{{ asset('storage/' . $coverPhoto->path) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="property-gallery-main-link"
                    >
                        <img
                            src="{{ asset('storage/' . $coverPhoto->path) }}"
                            alt="{{ $coverPhoto->caption ?: $property->name }}"
                            class="property-gallery-main-image"
                        >

                        <div class="property-gallery-overlay">
                            <span>
                                <i class="bi bi-arrows-fullscreen me-1"></i>
                                View Full Image
                            </span>
                        </div>
                    </a>

                    <div class="property-gallery-cover-label">
                        <i class="bi bi-star-fill me-1"></i>
                        Cover Photo
                    </div>
                </div>
            @endif

            @if($galleryPhotos->count())
                <div class="row g-3">
                    @foreach($galleryPhotos as $photo)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a
                                href="{{ asset('storage/' . $photo->path) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="property-gallery-thumbnail-link"
                            >
                                <img
                                    src="{{ asset('storage/' . $photo->path) }}"
                                    alt="{{ $photo->caption ?: $property->name }}"
                                    class="property-gallery-thumbnail"
                                    loading="lazy"
                                >

                                <div class="property-gallery-thumbnail-overlay">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <div class="property-no-photos">
                <div class="pm-icon-chip pm-chip-navy mx-auto mb-3 property-empty-icon">
                    <i class="bi bi-images"></i>
                </div>

                <h5 class="fw-bold text-slate-900 mb-1">
                    No Property Photos
                </h5>

                <p class="text-muted mb-3">
                    No photos have been uploaded for this property yet.
                </p>

                <a
                    href="{{ route('properties.edit', $property) }}"
                    class="btn pm-btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Photos
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Property Summary --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="pm-card h-100 property-summary-card">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-primary">
                        <i class="bi bi-door-open"></i>
                    </span>

                    <div>
                        <div class="pm-metric-value">
                            {{ $property->total_units }}
                        </div>
                        <div class="pm-metric-label">
                            Total Units
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="pm-card h-100 pm-accent-teal property-summary-card">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-teal">
                        <i class="bi bi-check2-square"></i>
                    </span>

                    <div>
                        <div class="pm-metric-value">
                            {{ $property->units->count() }}
                        </div>
                        <div class="pm-metric-label">
                            Registered Units
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="pm-card h-100 pm-accent-info property-summary-card">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-info">
                        <i class="bi bi-person-badge"></i>
                    </span>

                    <div class="min-w-0">
                        <div class="pm-metric-value fs-6 text-truncate" title="{{ $property->owner->full_name ?? 'N/A' }}">
                            {{ $property->owner->full_name ?? 'N/A' }}
                        </div>

                        <div class="pm-metric-label">
                            Owner
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="pm-card h-100 pm-accent-warning property-summary-card">
            <div class="pm-card-body">
                <div class="pm-card-body-header">
                    <span class="pm-icon-chip pm-chip-warning">
                        <i class="bi bi-geo-alt"></i>
                    </span>

                    <div class="min-w-0">
                        <div class="pm-metric-value fs-6 text-truncate" title="{{ $property->city }}">
                            {{ $property->city }}
                        </div>

                        <div class="pm-metric-label">
                            Location
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Property Information --}}
<div class="pm-card mb-4">
    <div class="pm-card-header">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-info-circle me-2 text-primary"></i>
                Property Information
            </h5>

            <small class="text-muted">
                General details regarding this property
            </small>
        </div>
    </div>

    <div class="pm-card-body">
        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Property Name</div>
                <div class="fw-semibold text-slate-900">
                    {{ $property->name }}
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Owner</div>
                <div class="fw-semibold text-slate-900">
                    {{ $property->owner->full_name ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Property Type</div>
                <div class="fw-semibold text-slate-900">
                    {{ $property->propertyType->name ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">City</div>
                <div class="fw-semibold text-slate-900">
                    <i class="bi bi-geo-alt me-1 text-muted"></i>
                    {{ $property->city }}
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Address</div>
                <div class="fw-semibold text-slate-900">
                    <i class="bi bi-pin-map me-1 text-muted"></i>
                    {{ $property->address }}
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Total Capacity</div>
                <div class="fw-semibold text-slate-900">
                    {{ $property->total_units }} units
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Status</div>

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
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pm-metric-label mb-1">Date Added</div>
                <div class="fw-semibold text-slate-900">
                    {{ $property->date_added
                        ? \Carbon\Carbon::parse($property->date_added)->format('d M Y')
                        : 'Not specified'
                    }}
                </div>
            </div>

            <div class="col-12">
                <div class="pm-metric-label mb-1">Description</div>

                <div class="property-description">
                    {{ $property->description ?: 'No description provided.' }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Property Units --}}
<div class="pm-card">
    <div class="pm-card-header">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-door-open me-2 text-primary"></i>
                Property Units
            </h5>

            <small class="text-muted">
                Units registered under this property
            </small>
        </div>

        <span class="pm-tag pm-tag-navy">
            {{ $property->units->count() }}
            {{ $property->units->count() === 1 ? 'Unit' : 'Units' }}
        </span>
    </div>

    <div class="pm-card-body p-0">

        @if($property->units->count())

            <div class="table-responsive">
                <table class="table pm-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Unit</th>
                            <th>Floor</th>
                            <th>Bedrooms</th>
                            <th>Bathrooms</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($property->units as $unit)
                            <tr>

                                <td class="ps-4 text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">

                                        <div class="pm-icon-chip pm-chip-navy property-unit-icon me-2">
                                            <i class="bi bi-door-open"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold text-slate-900">
                                                {{ $unit->unit_number }}
                                            </div>

                                            <small class="text-muted">
                                                Unit #{{ $unit->id }}
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td>
                                    {{ $unit->floor ?? '-' }}
                                </td>

                                <td>
                                    {{ $unit->bedrooms }}
                                </td>

                                <td>
                                    {{ $unit->bathrooms }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ number_format($unit->monthly_rent, 2) }}
                                    </span>
                                </td>

                                <td>
                                    @if($unit->status === 'occupied')
                                        <span class="pm-tag pm-tag-good">
                                            <i class="bi bi-person-check me-1"></i>
                                            Occupied
                                        </span>
                                    @elseif($unit->status === 'vacant')
                                        <span class="pm-tag pm-tag-info">
                                            <i class="bi bi-house-check me-1"></i>
                                            Vacant
                                        </span>
                                    @else
                                        <span class="pm-tag pm-tag-navy">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @else

            <div class="text-center py-5">
                <div class="pm-icon-chip pm-chip-navy mx-auto mb-3 property-units-empty-icon">
                    <i class="bi bi-door-open"></i>
                </div>

                <h5 class="fw-bold text-slate-900 mb-1">
                    No Units Yet
                </h5>

                <p class="text-muted mb-0">
                    No units have been registered under this property yet.
                </p>
            </div>

        @endif

    </div>
</div>

</div>

@endsection

@push('styles')

<style>
    .property-gallery-cover {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .property-gallery-main-link {
        position: relative;
        display: block;
        overflow: hidden;
        text-decoration: none;
    }

    .property-gallery-main-image {
        display: block;
        width: 100%;
        height: 430px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .property-gallery-main-link:hover .property-gallery-main-image {
        transform: scale(1.02);
    }

    .property-gallery-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0);
        color: transparent;
        transition: all 0.3s ease;
    }

    .property-gallery-main-link:hover .property-gallery-overlay {
        background: rgba(0, 0, 0, 0.35);
        color: #fff;
    }

    .property-gallery-overlay span {
        padding: 9px 14px;
        border-radius: 8px;
        background: rgba(0, 0, 0, 0.7);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .property-gallery-cover-label {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 7px 11px;
        border-radius: 8px;
        background: rgba(13, 110, 253, 0.94);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        pointer-events: none;
    }

    .property-gallery-thumbnail-link {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 12px;
        text-decoration: none;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .property-gallery-thumbnail {
        display: block;
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .property-gallery-thumbnail-link:hover .property-gallery-thumbnail {
        transform: scale(1.04);
    }

    .property-gallery-thumbnail-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0);
        color: transparent;
        transition: all 0.3s ease;
        font-size: 1.25rem;
    }

    .property-gallery-thumbnail-link:hover .property-gallery-thumbnail-overlay {
        background: rgba(0, 0, 0, 0.30);
        color: #fff;
    }

    .property-no-photos {
        padding: 55px 20px;
        text-align: center;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
    }

    .property-empty-icon,
    .property-units-empty-icon {
        width: 58px;
        height: 58px;
        font-size: 1.5rem;
    }

    .property-summary-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .property-summary-card:hover {
        transform: translateY(-2px);
    }

    .property-description {
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        line-height: 1.7;
        white-space: pre-line;
    }

    .property-unit-icon {
        width: 38px;
        height: 38px;
        font-size: 0.95rem;
        flex: 0 0 38px;
    }

    .min-w-0 {
        min-width: 0;
    }

    @media (max-width: 767.98px) {
        .property-gallery-main-image {
            height: 280px;
        }

        .property-gallery-thumbnail {
            height: 140px;
        }

        .property-gallery-cover-label {
            top: 10px;
            left: 10px;
        }

        .property-description {
            font-size: 0.92rem;
        }
    }

    html[data-theme="dark"] .property-gallery-cover,
    html[data-theme="dark"] .property-gallery-thumbnail-link {
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .property-no-photos,
    html[data-theme="dark"] .property-description {
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .property-description {
        color: #cbd5e1;
    }
</style>

@endpush
