@extends('layouts.app')

@section('title', 'Edit Property')
@section('page-heading', 'Edit Property')

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="property-page-header mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
        <div>
            <div class="pm-eyebrow">
                <i class="bi bi-buildings me-1"></i>
                Properties / Edit #{{ $property->id }}
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h3 mb-1 fw-bold text-slate-900">
                    Edit Property
                </h1>

                @if($property->status === 'active')
                    <span class="property-status status-active">
                        <i class="bi bi-check-circle-fill"></i>
                        Active
                    </span>
                @elseif($property->status === 'maintenance')
                    <span class="property-status status-maintenance">
                        <i class="bi bi-tools"></i>
                        Maintenance
                    </span>
                @else
                    <span class="property-status status-inactive">
                        <i class="bi bi-pause-circle-fill"></i>
                        Inactive
                    </span>
                @endif
            </div>

            <p class="text-muted mb-0 mt-1">
                Update the information, status, and photos for
                <strong>{{ $property->name }}</strong>.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('properties.show', $property) }}" class="btn pm-btn-outline">
                <i class="bi bi-eye me-1"></i>
                View Property
            </a>

            <a href="{{ route('properties.index') }}" class="btn pm-btn-outline">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Properties
            </a>
        </div>
    </div>
</div>

{{-- Validation Summary --}}
@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="alert-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <div>
                <div class="fw-bold mb-1">Please review the highlighted fields.</div>
                <div class="small">
                    {{ $errors->count() }}
                    {{ $errors->count() === 1 ? 'issue needs' : 'issues need' }}
                    your attention before the property can be updated.
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Property Summary --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="edit-summary-card">
            <div class="summary-icon bg-primary-subtle text-primary">
                <i class="bi bi-door-open"></i>
            </div>
            <div>
                <div class="summary-value">{{ $property->total_units }}</div>
                <div class="summary-label">Total Units</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="edit-summary-card">
            <div class="summary-icon bg-success-subtle text-success">
                <i class="bi bi-grid-3x3-gap"></i>
            </div>
            <div>
                <div class="summary-value">{{ $property->units->count() }}</div>
                <div class="summary-label">Registered Units</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="edit-summary-card">
            <div class="summary-icon bg-info-subtle text-info">
                <i class="bi bi-images"></i>
            </div>
            <div>
                <div class="summary-value">{{ $property->photos->count() }}</div>
                <div class="summary-label">Property Photos</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="edit-summary-card">
            <div class="summary-icon bg-warning-subtle text-warning-emphasis">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div>
                <div class="summary-value text-truncate">{{ $property->city }}</div>
                <div class="summary-label">Location</div>
            </div>
        </div>
    </div>
</div>

{{-- Property Information --}}
<div class="pm-card">
    <div class="pm-card-header">
        <div>
            <h5 class="mb-1 fw-bold">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                Property Information
            </h5>
            <div class="small text-muted">
                Update the core details of this property.
            </div>
        </div>

        <span class="section-badge">
            <i class="bi bi-building"></i>
            General Details
        </span>
    </div>

    <div class="pm-card-body">

        <form
            action="{{ route('properties.update', $property) }}"
            method="POST"
            enctype="multipart/form-data"
            id="propertyForm"
        >
            @csrf
            @method('PUT')

            {{-- Owner & Property Type --}}
            <div class="row g-3 mb-1">

                <div class="col-md-6">
                    <label for="owner_id" class="form-label">
                        Property Owner
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group has-validation">
                        <span class="input-group-text">
                            <i class="bi bi-person-badge"></i>
                        </span>

                        <select
                            name="owner_id"
                            id="owner_id"
                            class="form-select @error('owner_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Select property owner</option>

                            @foreach($owners as $owner)
                                <option
                                    value="{{ $owner->id }}"
                                    @selected(old('owner_id', $property->owner_id) == $owner->id)
                                >
                                    {{ $owner->full_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('owner_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="property_type_id" class="form-label">
                        Property Type
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group has-validation">
                        <span class="input-group-text">
                            <i class="bi bi-building"></i>
                        </span>

                        <select
                            name="property_type_id"
                            id="property_type_id"
                            class="form-select @error('property_type_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Select property type</option>

                            @foreach($propertyTypes as $propertyType)
                                <option
                                    value="{{ $propertyType->id }}"
                                    @selected(old('property_type_id', $property->property_type_id) == $propertyType->id)
                                >
                                    {{ $propertyType->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('property_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Property Name --}}
            <div class="mb-3">
                <label for="name" class="form-label">
                    Property Name
                    <span class="text-danger">*</span>
                </label>

                <div class="input-group has-validation">
                    <span class="input-group-text">
                        <i class="bi bi-house-door"></i>
                    </span>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $property->name) }}"
                        placeholder="e.g. Sunrise Apartments"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Address & City --}}
            <div class="row g-3 mb-1">

                <div class="col-md-8">
                    <label for="address" class="form-label">
                        Address
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group has-validation">
                        <span class="input-group-text">
                            <i class="bi bi-geo-alt"></i>
                        </span>

                        <input
                            type="text"
                            name="address"
                            id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            value="{{ old('address', $property->address) }}"
                            placeholder="Enter property address"
                            required
                        >

                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="city" class="form-label">
                        City
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group has-validation">
                        <span class="input-group-text">
                            <i class="bi bi-pin-map"></i>
                        </span>

                        <input
                            type="text"
                            name="city"
                            id="city"
                            class="form-control @error('city') is-invalid @enderror"
                            value="{{ old('city', $property->city) }}"
                            placeholder="Enter city"
                            required
                        >

                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description" class="form-label">
                    Description
                    <span class="text-muted fw-normal">(Optional)</span>
                </label>

                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Enter a description of the property..."
                >{{ old('description', $property->description) }}</textarea>

                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Property Configuration --}}
            <div class="form-section-heading">
                <div>
                    <div class="fw-bold">Property Configuration</div>
                    <div class="small text-muted">
                        Update capacity, operational status, and registration date.
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">

                {{-- Total Units --}}
                <div class="col-md-4">
                    <label for="total_units" class="form-label">
                        Total Units
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group has-validation">
                        <span class="input-group-text">
                            <i class="bi bi-door-open"></i>
                        </span>

                        <input
                            type="number"
                            name="total_units"
                            id="total_units"
                            class="form-control @error('total_units') is-invalid @enderror"
                            value="{{ old('total_units', $property->total_units) }}"
                            min="0"
                            required
                        >

                        <span class="input-group-text">units</span>

                        @error('total_units')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-4">
                    <label for="status" class="form-label">
                        Status
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="active" @selected(old('status', $property->status) === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected(old('status', $property->status) === 'inactive')>
                            Inactive
                        </option>
                        <option value="maintenance" @selected(old('status', $property->status) === 'maintenance')>
                            Maintenance
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date Added --}}
                <div class="col-md-4">
                    <label for="date_added" class="form-label">
                        Date Added
                        <span class="text-muted fw-normal">(Optional)</span>
                    </label>

                    <input
                        type="date"
                        name="date_added"
                        id="date_added"
                        class="form-control @error('date_added') is-invalid @enderror"
                        value="{{ old('date_added', $property->date_added) }}"
                    >

                    @error('date_added')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Add More Photos --}}
            <div class="form-section-heading">
                <div>
                    <div class="fw-bold">
                        <i class="bi bi-cloud-arrow-up me-1 text-primary"></i>
                        Add More Photos
                    </div>
                    <div class="small text-muted">
                        Upload additional property photos. Existing photos are managed below.
                    </div>
                </div>
            </div>

            <div class="photo-upload-box mb-3">
                <div class="photo-upload-icon">
                    <i class="bi bi-images"></i>
                </div>

                <div class="flex-grow-1">
                    <label for="photos" class="form-label fw-semibold mb-1">
                        Choose property photos
                    </label>

                    <input
                        type="file"
                        name="photos[]"
                        id="photos"
                        class="form-control @error('photos') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >

                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        JPG, PNG and WebP supported. Maximum 5MB per photo.
                    </div>

                    @error('photos')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    @error('photos.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- New Photo Preview --}}
            <div id="photoPreview" class="row g-3 mb-4"></div>

            {{-- Form Buttons --}}
            <div class="form-actions">
                <div class="small text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Your existing property information will be preserved unless changed.
                </div>

                <div class="d-flex gap-2">
                    <a
                        href="{{ route('properties.show', $property) }}"
                        class="btn pm-btn-outline"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn pm-btn-primary"
                        id="updatePropertyBtn"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Update Property
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- Existing Photos --}}
<div class="pm-card mt-4">

    <div class="pm-card-header">
        <div>
            <h5 class="mb-1 fw-bold">
                <i class="bi bi-images me-2 text-primary"></i>
                Existing Property Photos
            </h5>

            <div class="small text-muted">
                Set a cover photo or remove photos that are no longer needed.
            </div>
        </div>

        <span class="pm-tag pm-tag-navy">
            <i class="bi bi-image me-1"></i>
            {{ $property->photos->count() }}
            {{ $property->photos->count() === 1 ? 'Photo' : 'Photos' }}
        </span>
    </div>

    <div class="pm-card-body">

        @if($property->photos->count())

            <div class="row g-3">

                @foreach($property->photos as $photo)

                    <div
                        class="col-6 col-md-4 col-lg-3"
                        id="photo-{{ $photo->id }}"
                    >
                        <div class="existing-photo-card">

                            <div class="existing-photo-image-wrapper">

                                <img
                                    src="{{ asset('storage/' . $photo->path) }}"
                                    alt="{{ $photo->caption ?: $property->name }}"
                                    class="existing-photo-image"
                                    loading="lazy"
                                >

                                @if($photo->is_cover)
                                    <span class="existing-photo-cover">
                                        <i class="bi bi-star-fill me-1"></i>
                                        Cover Photo
                                    </span>
                                @endif

                            </div>

                            <div class="existing-photo-content">

                                @if(!$photo->is_cover)

                                    <form
                                        action="{{ route('properties.photos.cover', [$property, $photo]) }}"
                                        method="POST"
                                        class="mb-2"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm pm-btn-outline w-100"
                                        >
                                            <i class="bi bi-star me-1"></i>
                                            Set as Cover
                                        </button>
                                    </form>

                                @else

                                    <div class="current-cover-label mb-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Current Cover
                                    </div>

                                @endif

                                <form
                                    action="{{ route('properties.photos.destroy', [$property, $photo]) }}"
                                    method="POST"
                                    class="photo-delete-form"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm pm-btn-outline-danger w-100"
                                    >
                                        <i class="bi bi-trash me-1"></i>
                                        Delete Photo
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-photo-state">
                <div class="empty-photo-icon">
                    <i class="bi bi-images"></i>
                </div>

                <div>
                    <div class="fw-bold text-slate-900">No photos uploaded</div>
                    <div class="small text-muted">
                        Add photos using the uploader above.
                    </div>
                </div>
            </div>

        @endif

    </div>
</div>

</div>

@endsection

@push('styles')

<style>
    .property-page-header {
        position: relative;
    }

    .property-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-maintenance {
        background: #fef3c7;
        color: #92400e;
    }

    .status-inactive {
        background: #e2e8f0;
        color: #475569;
    }

    .edit-summary-card {
        display: flex;
        align-items: center;
        gap: 13px;
        height: 100%;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.15rem;
    }

    .summary-value {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .summary-label {
        margin-top: 3px;
        color: #64748b;
        font-size: .76rem;
        font-weight: 600;
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: .75rem;
        font-weight: 700;
    }

    .form-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
        margin: 8px 0 18px;
        border-top: 1px solid #e2e8f0;
    }

    .form-label {
        color: #334155;
        font-size: .86rem;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .input-group-text {
        background: #f8fafc;
        border-color: #dbe3ec;
        color: #64748b;
    }

    .form-control,
    .form-select {
        min-height: 44px;
        border-color: #dbe3ec;
        border-radius: 9px;
    }

    .input-group > .form-control,
    .input-group > .form-select {
        border-radius: 0 9px 9px 0;
    }

    .input-group > .input-group-text:first-child {
        border-radius: 9px 0 0 9px;
    }

    textarea.form-control {
        min-height: auto;
    }

    .photo-upload-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        border: 1px dashed #b8c5d6;
        border-radius: 14px;
        background: #f8fafc;
    }

    .photo-upload-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #e8f1ff;
        color: #0d6efd;
        font-size: 1.3rem;
    }

    .photo-preview-card,
    .existing-photo-card {
        overflow: hidden;
        height: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
    }

    .photo-preview-image,
    .existing-photo-image {
        display: block;
        width: 100%;
        height: 170px;
        object-fit: cover;
    }

    .photo-preview-image {
        height: 150px;
    }

    .photo-preview-card {
        position: relative;
    }

    .photo-preview-number {
        position: absolute;
        top: 9px;
        left: 9px;
        padding: 4px 8px;
        border-radius: 7px;
        background: rgba(15, 23, 42, .72);
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
    }

    .photo-preview-label {
        padding: 9px 11px;
        color: #64748b;
        font-size: .78rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .existing-photo-image-wrapper {
        position: relative;
        overflow: hidden;
    }

    .existing-photo-image {
        transition: transform .3s ease;
    }

    .existing-photo-card:hover .existing-photo-image {
        transform: scale(1.025);
    }

    .existing-photo-cover {
        position: absolute;
        top: 10px;
        left: 10px;
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 7px;
        background: rgba(13, 110, 253, .94);
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
    }

    .existing-photo-content {
        padding: 12px;
    }

    .current-cover-label {
        padding: 7px;
        border-radius: 8px;
        background: #ecfdf5;
        color: #15803d;
        text-align: center;
        font-size: .78rem;
        font-weight: 700;
    }

    .empty-photo-state {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 22px;
        border: 1px dashed #cbd5e1;
        border-radius: 13px;
        background: #f8fafc;
    }

    .empty-photo-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        border-radius: 12px;
        background: #e2e8f0;
        color: #64748b;
        font-size: 1.25rem;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .alert-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 38px;
        border-radius: 10px;
        background: rgba(220, 53, 69, .12);
        color: #dc3545;
    }

    .pm-btn-outline-danger {
        color: #dc3545;
        border: 1px solid #f1b8bf;
        background: transparent;
    }

    .pm-btn-outline-danger:hover {
        color: #fff;
        background: #dc3545;
        border-color: #dc3545;
    }

    html[data-theme="dark"] .edit-summary-card,
    html[data-theme="dark"] .existing-photo-card,
    html[data-theme="dark"] .photo-preview-card {
        background: #111827;
        border-color: #273449;
    }

    html[data-theme="dark"] .summary-value,
    html[data-theme="dark"] .text-slate-900,
    html[data-theme="dark"] .form-label {
        color: #e5e7eb !important;
    }

    html[data-theme="dark"] .summary-label,
    html[data-theme="dark"] .text-muted {
        color: #94a3b8 !important;
    }

    html[data-theme="dark"] .section-badge,
    html[data-theme="dark"] .photo-upload-box,
    html[data-theme="dark"] .empty-photo-state {
        background: #172033;
        border-color: #334155;
    }

    html[data-theme="dark"] .input-group-text,
    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select {
        background-color: #111827;
        border-color: #334155;
        color: #e5e7eb;
    }

    html[data-theme="dark"] .input-group-text {
        background: #172033;
        color: #94a3b8;
    }

    html[data-theme="dark"] .form-section-heading,
    html[data-theme="dark"] .form-actions {
        border-color: #273449;
    }

    @media (max-width: 767.98px) {
        .photo-upload-box {
            align-items: flex-start;
            flex-direction: column;
        }

        .form-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .form-actions .d-flex {
            width: 100%;
        }

        .form-actions .btn {
            flex: 1;
        }
    }
</style>

@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photoPreview');
    const propertyForm = document.getElementById('propertyForm');
    const updateButton = document.getElementById('updatePropertyBtn');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            photoPreview.innerHTML = '';

            const files = Array.from(this.files || []);
            let previewIndex = 0;

            files.forEach(function (file) {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                previewIndex++;

                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';

                const card = document.createElement('div');
                card.className = 'photo-preview-card';

                const image = document.createElement('img');
                image.className = 'photo-preview-image';
                image.alt = file.name;

                const number = document.createElement('span');
                number.className = 'photo-preview-number';
                number.textContent = '#' + previewIndex;

                const label = document.createElement('div');
                label.className = 'photo-preview-label';
                label.textContent = file.name;

                card.appendChild(image);
                card.appendChild(number);
                card.appendChild(label);
                col.appendChild(card);
                photoPreview.appendChild(col);

                const reader = new FileReader();

                reader.onload = function (event) {
                    image.src = event.target.result;
                };

                reader.readAsDataURL(file);
            });
        });
    }

    document.querySelectorAll('.photo-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const confirmed = window.confirm(
                'Are you sure you want to delete this photo? This action cannot be undone.'
            );

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    if (propertyForm && updateButton) {
        propertyForm.addEventListener('submit', function () {
            updateButton.disabled = true;
            updateButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>' +
                'Updating Property...';
        });
    }
});
</script>

@endpush
