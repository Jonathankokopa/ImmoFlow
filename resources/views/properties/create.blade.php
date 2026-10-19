@extends('layouts.app')

@section('title', 'Add Property')
@section('page-heading', 'Add Property')

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="pm-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <div class="pm-eyebrow">
                <i class="bi bi-buildings me-1"></i>
                Properties / New
            </div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Add Property
            </h1>

            <p class="text-muted mb-0">
                Register a new property and configure its portfolio information.
            </p>
        </div>

        <a href="{{ route('properties.index') }}" class="btn pm-btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Properties
        </a>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert property-error-alert mb-4" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div class="property-error-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">
                    Please check the form
                </h6>

                <p class="mb-2 text-muted">
                    Some information needs your attention before the property can be saved.
                </p>

                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('properties.store') }}"
      method="POST"
      enctype="multipart/form-data"
      id="propertyForm">
    @csrf

    {{-- Main Information --}}
    <div class="row g-4">

        <div class="col-xl-8">

            {{-- Property Details --}}
            <div class="pm-card mb-4">

                <div class="pm-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-section-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Property Information
                            </h5>

                            <small class="text-muted">
                                Basic identity, ownership and location details
                            </small>
                        </div>
                    </div>
                </div>

                <div class="pm-card-body">

                    {{-- Owner & Type --}}
                    <div class="row g-3 mb-1">

                        <div class="col-md-6">
                            <label for="owner_id" class="pm-form-label">
                                Property Owner
                                <span class="text-danger">*</span>
                            </label>

                            <select name="owner_id"
                                    id="owner_id"
                                    class="form-select @error('owner_id') is-invalid @enderror"
                                    required>
                                <option value="">Select property owner</option>

                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}"
                                        @selected(old('owner_id') == $owner->id)>
                                        {{ $owner->full_name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-text">
                                The owner responsible for this property.
                            </div>

                            @error('owner_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="property_type_id" class="pm-form-label">
                                Property Type
                                <span class="text-danger">*</span>
                            </label>

                            <select name="property_type_id"
                                    id="property_type_id"
                                    class="form-select @error('property_type_id') is-invalid @enderror"
                                    required>
                                <option value="">Select property type</option>

                                @foreach($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->id }}"
                                        @selected(old('property_type_id') == $propertyType->id)>
                                        {{ $propertyType->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-text">
                                Choose the category that best describes this property.
                            </div>

                            @error('property_type_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="pm-form-label">
                            Property Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. Sunrise Apartments"
                               required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Address / City --}}
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label for="address" class="pm-form-label">
                                Address
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="address"
                                   id="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address') }}"
                                   placeholder="Enter property address"
                                   required>

                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="city" class="pm-form-label">
                                City
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="city"
                                   id="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city') }}"
                                   placeholder="Enter city"
                                   required>

                            @error('city')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    {{-- Description --}}
                    <div class="mt-3">
                        <label for="description" class="pm-form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the property, amenities, surroundings or other useful details...">{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Photos --}}
            <div class="pm-card mb-4">

                <div class="pm-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-section-icon form-section-icon-info">
                            <i class="bi bi-images"></i>
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Property Photos
                            </h5>

                            <small class="text-muted">
                                Upload photos to showcase the property
                            </small>
                        </div>
                    </div>
                </div>

                <div class="pm-card-body">

                    <div class="photo-upload-box">

                        <div class="photo-upload-content">
                            <div class="photo-upload-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-1">
                                    Choose property photos
                                </h6>

                                <p class="text-muted small mb-3">
                                    You can select multiple images. The first image will automatically
                                    become the initial cover photo.
                                </p>

                                <label for="photos" class="btn btn-sm pm-btn-outline">
                                    <i class="bi bi-folder2-open me-1"></i>
                                    Browse Photos
                                </label>
                            </div>
                        </div>

                        <input type="file"
                               name="photos[]"
                               id="photos"
                               class="visually-hidden @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp"
                               multiple>

                        <div class="photo-upload-meta">
                            <span>
                                <i class="bi bi-file-earmark-image me-1"></i>
                                JPG, PNG or WebP
                            </span>

                            <span>
                                <i class="bi bi-hdd me-1"></i>
                                Max 5MB per photo
                            </span>
                        </div>

                        @error('photos')
                            <div class="text-danger small mt-3">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('photos.*')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Preview --}}
                    <div id="photoPreview" class="row g-3 mt-2"></div>

                </div>
            </div>

            {{-- Additional Settings --}}
            <div class="pm-card mb-4">

                <div class="pm-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-section-icon form-section-icon-success">
                            <i class="bi bi-sliders"></i>
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Property Settings
                            </h5>

                            <small class="text-muted">
                                Configure units, status and registration date
                            </small>
                        </div>
                    </div>
                </div>

                <div class="pm-card-body">

                    <div class="row g-3">

                        {{-- Total Units --}}
                        <div class="col-md-4">
                            <label for="total_units" class="pm-form-label">
                                Total Units
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-door-open"></i>
                                </span>

                                <input type="number"
                                       name="total_units"
                                       id="total_units"
                                       class="form-control @error('total_units') is-invalid @enderror"
                                       value="{{ old('total_units', 0) }}"
                                       min="0"
                                       required>
                            </div>

                            @error('total_units')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label for="status" class="pm-form-label">
                                Status
                                <span class="text-danger">*</span>
                            </label>

                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="active"
                                    @selected(old('status', 'active') === 'active')>
                                    Active
                                </option>

                                <option value="inactive"
                                    @selected(old('status') === 'inactive')>
                                    Inactive
                                </option>

                                <option value="maintenance"
                                    @selected(old('status') === 'maintenance')>
                                    Maintenance
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div class="col-md-4">
                            <label for="date_added" class="pm-form-label">
                                Date Added
                            </label>

                            <input type="date"
                                   name="date_added"
                                   id="date_added"
                                   class="form-control @error('date_added') is-invalid @enderror"
                                   value="{{ old('date_added') }}">

                            @error('date_added')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Side Information --}}
        <div class="col-xl-4">

            <div class="pm-card property-help-card mb-4">
                <div class="pm-card-body">

                    <div class="property-help-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <h5 class="fw-bold text-slate-900 mb-2">
                        Property Setup
                    </h5>

                    <p class="text-muted small mb-3">
                        Complete the required information before saving this property.
                    </p>

                    <div class="setup-check">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Assign an owner</span>
                    </div>

                    <div class="setup-check">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Select the property type</span>
                    </div>

                    <div class="setup-check">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Enter location details</span>
                    </div>

                    <div class="setup-check">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Set the total number of units</span>
                    </div>

                </div>
            </div>

            <div class="pm-card mb-4">
                <div class="pm-card-header">
                    <div>
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>
                            Helpful Tip
                        </h6>
                    </div>
                </div>

                <div class="pm-card-body">
                    <p class="text-muted small mb-0">
                        Upload a clear exterior photo first. It will be used as the property's
                        initial cover image and will make the property easier to identify
                        throughout the system.
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pm-card property-actions-card">

                <div class="pm-card-body">

                    <button type="submit"
                            class="btn pm-btn-primary w-100 mb-2"
                            id="savePropertyBtn">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Property
                    </button>

                    <a href="{{ route('properties.index') }}"
                       class="btn pm-btn-outline w-100">
                        Cancel
                    </a>

                </div>
            </div>

        </div>

    </div>

</form>

</div>
@endsection

@push('styles')

<style>
    .property-error-alert {
        border: 1px solid #fecaca;
        border-radius: 14px;
        background: #fff7f7;
        padding: 16px 18px;
    }

    .property-error-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fee2e2;
        color: #dc2626;
    }

    .form-section-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
    }

    .form-section-icon-info {
        background: #eff6ff;
        color: #2563eb;
    }

    .form-section-icon-success {
        background: #ecfdf5;
        color: #047857;
    }

    .pm-form-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: .88rem;
        font-weight: 650;
    }

    .form-control,
    .form-select,
    .input-group-text {
        border-color: #dbe2ea;
        border-radius: 10px;
    }

    .form-control,
    .form-select {
        min-height: 44px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .08);
    }

    .input-group .input-group-text {
        background: #f8fafc;
        color: #64748b;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .photo-upload-box {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        padding: 22px;
        transition: border-color .2s ease, background .2s ease;
    }

    .photo-upload-box:hover {
        border-color: #93c5fd;
        background: #f8fbff;
    }

    .photo-upload-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .photo-upload-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e0ecff;
        color: #2563eb;
        font-size: 1.35rem;
    }

    .photo-upload-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: .78rem;
    }

    .photo-preview-card {
        position: relative;
        overflow: hidden;
        border-radius: 13px;
        border: 1px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
    }

    .photo-preview-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .photo-preview-label {
        padding: 9px 10px;
        font-size: .78rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .photo-preview-number,
    .photo-cover-label {
        position: absolute;
        top: 8px;
        border-radius: 7px;
        padding: 4px 7px;
        font-size: .7rem;
        font-weight: 700;
    }

    .photo-preview-number {
        left: 8px;
        background: rgba(15, 23, 42, .72);
        color: #fff;
    }

    .photo-cover-label {
        right: 8px;
        background: rgba(37, 99, 235, .92);
        color: #fff;
    }

    .property-help-card {
        background: linear-gradient(145deg, #f8fbff, #ffffff);
    }

    .property-help-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e0ecff;
        color: #2563eb;
        margin-bottom: 14px;
    }

    .setup-check {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 9px 0;
        border-top: 1px solid #eef2f7;
        color: #475569;
        font-size: .86rem;
    }

    .setup-check i {
        color: #10b981;
    }

    .property-actions-card {
        position: sticky;
        top: 20px;
    }

    @media (max-width: 1199.98px) {
        .property-actions-card {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .photo-upload-content {
            align-items: flex-start;
            flex-direction: column;
        }

        .photo-upload-box {
            padding: 17px;
        }

        .photo-upload-meta {
            flex-direction: column;
            gap: 7px;
        }
    }

    html[data-theme="dark"] .property-error-alert {
        background: #450a0a;
        border-color: #7f1d1d;
    }

    html[data-theme="dark"] .form-section-icon {
        background: #312e81;
        color: #c7d2fe;
    }

    html[data-theme="dark"] .form-section-icon-info {
        background: #172554;
        color: #93c5fd;
    }

    html[data-theme="dark"] .form-section-icon-success {
        background: #052e25;
        color: #6ee7b7;
    }

    html[data-theme="dark"] .pm-form-label {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select,
    html[data-theme="dark"] .input-group-text {
        background-color: #111827;
        border-color: #374151;
        color: #f8fafc;
    }

    html[data-theme="dark"] .input-group-text {
        background: #1f2937;
        color: #94a3b8;
    }

    html[data-theme="dark"] .photo-upload-box {
        background: #111827;
        border-color: #374151;
    }

    html[data-theme="dark"] .photo-upload-box:hover {
        background: #172033;
        border-color: #4b5563;
    }

    html[data-theme="dark"] .photo-upload-icon,
    html[data-theme="dark"] .property-help-icon {
        background: #172554;
        color: #93c5fd;
    }

    html[data-theme="dark"] .photo-upload-meta,
    html[data-theme="dark"] .setup-check {
        border-color: #1f2937;
    }

    html[data-theme="dark"] .photo-preview-card {
        background: #111827;
        border-color: #374151;
    }

    html[data-theme="dark"] .property-help-card {
        background: #111827;
    }

    html[data-theme="dark"] .text-slate-900 {
        color: #f8fafc !important;
    }
</style>

@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photoPreview');
    const propertyForm = document.getElementById('propertyForm');
    const savePropertyBtn = document.getElementById('savePropertyBtn');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            photoPreview.innerHTML = '';

            const files = Array.from(this.files || []);

            files.forEach(function (file, index) {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';

                const card = document.createElement('div');
                card.className = 'photo-preview-card';

                const image = document.createElement('img');
                image.className = 'photo-preview-image';
                image.alt = file.name;

                const number = document.createElement('span');
                number.className = 'photo-preview-number';
                number.textContent = '#' + (index + 1);

                const label = document.createElement('div');
                label.className = 'photo-preview-label';
                label.textContent = file.name;

                card.appendChild(image);
                card.appendChild(number);
                card.appendChild(label);

                if (index === 0) {
                    const cover = document.createElement('span');
                    cover.className = 'photo-cover-label';
                    cover.innerHTML = '<i class="bi bi-star-fill me-1"></i> Cover';
                    card.appendChild(cover);
                }

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

    if (propertyForm && savePropertyBtn) {
        propertyForm.addEventListener('submit', function () {
            savePropertyBtn.disabled = true;

            savePropertyBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                'Saving Property...';
        });
    }
});
</script>

@endpush
