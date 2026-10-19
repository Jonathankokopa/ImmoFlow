@extends('layouts.app')

@section('title', 'Edit Property Type')

@section('page-heading', 'Edit Property Type')

@push('styles')

<style>
    .pm-page {
        max-width: 1200px;
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
        gap: 0.5rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .pm-card-body {
        padding: 1.5rem;
    }

    .pm-form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
    }

    .form-control,
    .form-select {
        min-height: 46px;
        border-color: #cbd5e1;
        border-radius: 0.7rem;
        padding: 0.65rem 0.85rem;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .pm-field-hint {
        margin-top: 0.45rem;
        font-size: 0.78rem;
        color: #64748b;
    }

    .pm-status-preview {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-top: 0.75rem;
        padding: 0.65rem 0.75rem;
        border-radius: 0.7rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.82rem;
        color: #475569;
    }

    .pm-status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #198754;
        flex: 0 0 auto;
    }

    .pm-info-box {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding: 0.9rem 1rem;
        border: 1px solid #dbeafe;
        border-radius: 0.75rem;
        background: #eff6ff;
        color: #1e40af;
    }

    .pm-info-box i {
        font-size: 1.1rem;
        margin-top: 0.05rem;
    }

    .pm-info-box strong {
        display: block;
        margin-bottom: 0.15rem;
        font-size: 0.85rem;
    }

    .pm-info-box span {
        font-size: 0.8rem;
        color: #475569;
    }

    .pm-record-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .pm-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0.65rem 1rem;
        border-radius: 0.7rem;
        font-weight: 600;
    }

    .pm-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0.65rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.7rem;
        background: #fff;
        color: #334155;
        font-weight: 600;
    }

    .pm-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    @media (max-width: 767.98px) {
        .pm-card-body {
            padding: 1rem;
        }

        .pm-card-header {
            padding: 1rem;
        }

        .pm-header .d-flex {
            width: 100%;
            flex-wrap: wrap;
        }

        .pm-header .btn {
            flex: 1 1 auto;
        }

        .pm-btn-primary,
        .pm-btn-outline {
            flex: 1 1 auto;
        }
    }

    html[data-theme="dark"] .pm-eyebrow {
        color: #94a3b8;
    }

    html[data-theme="dark"] .pm-header h1 {
        color: #f8fafc;
    }

    html[data-theme="dark"] .pm-card {
        background: #111827;
        border-color: #273449;
        box-shadow: none;
    }

    html[data-theme="dark"] .pm-card-header {
        background: #172033;
        border-color: #273449;
    }

    html[data-theme="dark"] .pm-form-label {
        color: #e2e8f0;
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select {
        background-color: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }

    html[data-theme="dark"] .form-control::placeholder {
        color: #64748b;
    }

    html[data-theme="dark"] .form-control:focus,
    html[data-theme="dark"] .form-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.12);
    }

    html[data-theme="dark"] .pm-field-hint {
        color: #94a3b8;
    }

    html[data-theme="dark"] .pm-status-preview {
        background: #172033;
        border-color: #334155;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-info-box {
        background: rgba(30, 64, 175, 0.16);
        border-color: rgba(96, 165, 250, 0.25);
        color: #bfdbfe;
    }

    html[data-theme="dark"] .pm-info-box span {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .pm-record-badge {
        background: rgba(99, 102, 241, 0.16);
        color: #c7d2fe;
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
</style>

@endpush

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 pm-header">
    <div>
        <div class="pm-eyebrow">
            Property Management / Property Types / Edit
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <h1 class="h3 mb-0 fw-bold">
                Edit Property Type
            </h1>

            <span class="pm-record-badge">
                <i class="bi bi-hash"></i>
                {{ $propertyType->id }}
            </span>
        </div>

        <p class="text-muted mb-0">
            Update classification settings and status for
            <strong>{{ $propertyType->name }}</strong>.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a
            href="{{ route('property-types.show', $propertyType) }}"
            class="btn pm-btn-outline"
        >
            <i class="bi bi-eye me-1"></i>
            View Type
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

{{-- Edit Form --}}
<div class="pm-card">
    <div class="pm-card-header">
        <i class="bi bi-pencil-square text-primary"></i>

        <div>
            <h5 class="mb-0 fw-bold">
                Property Type Information
            </h5>

            <small class="text-muted">
                Update the name, operational status, and description.
            </small>
        </div>
    </div>

    <div class="pm-card-body">

        <div class="pm-info-box">
            <i class="bi bi-info-circle"></i>

            <div>
                <strong>Updating a property classification</strong>
                <span>
                    Changes to the name or description will be reflected wherever
                    this property type is displayed in the system.
                </span>
            </div>
        </div>

        <form
            action="{{ route('property-types.update', $propertyType) }}"
            method="POST"
            id="propertyTypeEditForm"
        >
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- Type Name --}}
                <div class="col-md-8">
                    <label for="name" class="pm-form-label">
                        Property Type Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $propertyType->name) }}"
                        placeholder="e.g. Apartment, Office, Villa"
                        maxlength="255"
                        autocomplete="off"
                        required
                    >

                    <div class="pm-field-hint">
                        Use a clear and recognizable classification name.
                    </div>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Operational Status --}}
                <div class="col-md-4">
                    <label for="is_active" class="pm-form-label">
                        Operational Status
                        <span class="text-danger">*</span>
                    </label>

                    @php
                        $selectedStatus = (string) old(
                            'is_active',
                            $propertyType->is_active ? '1' : '0'
                        );
                    @endphp

                    <select
                        name="is_active"
                        id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror"
                        required
                    >
                        <option value="1" @selected($selectedStatus === '1')}>
                            Active
                        </option>

                        <option value="0" @selected($selectedStatus === '0')}>
                            Inactive
                        </option>
                    </select>

                    <div class="pm-status-preview" id="statusPreview">
                        <span class="pm-status-dot" id="statusDot"></span>

                        <span id="statusText">
                            This property type is currently active.
                        </span>
                    </div>

                    @error('is_active')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label for="description" class="pm-form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        maxlength="2000"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Enter a description for this property type..."
                    >{{ old('description', $propertyType->description) }}</textarea>

                    <div class="d-flex justify-content-between gap-3 mt-2">
                        <div class="pm-field-hint mt-0">
                            Optional. Explain the scope or characteristics of this category.
                        </div>

                        <small class="text-muted text-nowrap" id="descriptionCount">
                            0 / 2000
                        </small>
                    </div>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 pt-4 mt-4 border-top">

                <button
                    type="submit"
                    class="btn btn-primary pm-btn-primary"
                    id="updatePropertyTypeBtn"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Update Property Type
                </button>

                <a
                    href="{{ route('property-types.show', $propertyType) }}"
                    class="btn pm-btn-outline"
                >
                    Cancel
                </a>

            </div>
        </form>
    </div>
</div>

</div>
@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('propertyTypeEditForm');
        const submitButton = document.getElementById('updatePropertyTypeBtn');
        const statusSelect = document.getElementById('is_active');
        const statusText = document.getElementById('statusText');
        const statusDot = document.getElementById('statusDot');
        const description = document.getElementById('description');
        const descriptionCount = document.getElementById('descriptionCount');

        function updateStatusPreview() {
            if (!statusSelect || !statusText || !statusDot) {
                return;
            }

            if (statusSelect.value === '1') {
                statusText.textContent =
                    'This property type is active and available for selection.';
                statusDot.style.backgroundColor = '#198754';
            } else {
                statusText.textContent =
                    'This property type is inactive and should not be used for new selections.';
                statusDot.style.backgroundColor = '#64748b';
            }
        }

        function updateDescriptionCount() {
            if (!description || !descriptionCount) {
                return;
            }

            descriptionCount.textContent =
                `${description.value.length} / 2000`;
        }

        statusSelect?.addEventListener('change', updateStatusPreview);
        description?.addEventListener('input', updateDescriptionCount);

        form?.addEventListener('submit', function () {
            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...';
        });

        updateStatusPreview();
        updateDescriptionCount();
    });
</script>

@endpush
