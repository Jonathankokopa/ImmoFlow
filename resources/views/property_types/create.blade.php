@extends('layouts.app')

@section('title', 'Add Property Type')

@section('page-heading', 'Add Property Type')

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

    .pm-form-label .text-danger {
        font-weight: 700;
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

    .pm-info-box {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
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

    @media (max-width: 767.98px) {
        .pm-card-body {
            padding: 1rem;
        }

        .pm-card-header {
            padding: 1rem;
        }

        .pm-header .btn {
            width: 100%;
        }

        .pm-header .d-flex {
            width: 100%;
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

    html[data-theme="dark"] .pm-info-box {
        background: rgba(30, 64, 175, 0.16);
        border-color: rgba(96, 165, 250, 0.25);
        color: #bfdbfe;
    }

    html[data-theme="dark"] .pm-info-box span {
        color: #cbd5e1;
    }
</style>

@endpush

@section('content')

<div class="container-fluid pm-page px-0">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 pm-header">
    <div>
        <div class="pm-eyebrow">
            Property Management / Property Types / New
        </div>

        <h1 class="h3 mb-1 fw-bold">
            Add Property Type
        </h1>

        <p class="text-muted mb-0">
            Create a new classification category for properties.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('property-types.index') }}" class="btn pm-btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Property Types
        </a>
    </div>
</div>

{{-- Property Type Form --}}
<div class="pm-card">
    <div class="pm-card-header">
        <i class="bi bi-tags text-primary"></i>

        <div>
            <h5 class="mb-0 fw-bold">
                Property Type Information
            </h5>
            <small class="text-muted">
                Define the name, status, and description for this classification.
            </small>
        </div>
    </div>

    <div class="pm-card-body">

        <div class="pm-info-box">
            <i class="bi bi-info-circle"></i>

            <div>
                <strong>Property classifications</strong>
                <span>
                    Use clear, reusable categories such as Residential, Commercial,
                    Apartment, Villa, or Office to keep your property directory organized.
                </span>
            </div>
        </div>

        <form action="{{ route('property-types.store') }}" method="POST" id="propertyTypeForm">
            @csrf

            <div class="row g-4">

                {{-- Type Name --}}
                <div class="col-md-8">
                    <label for="name" class="pm-form-label">
                        Type Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="e.g. Commercial, Residential, Villa, Apartment"
                        maxlength="255"
                        autocomplete="off"
                        required
                    >

                    <div class="pm-field-hint">
                        Choose a short and recognizable name for this property category.
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
                        $selectedStatus = old('is_active', '1');
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
                            This property type will be available for selection.
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
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe the scope or characteristics of this property type..."
                        maxlength="2000"
                    >{{ old('description') }}</textarea>

                    <div class="d-flex justify-content-between mt-2">
                        <div class="pm-field-hint mt-0">
                            Optional. Add details that help staff understand when to use this category.
                        </div>

                        <small class="text-muted" id="descriptionCount">
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
                    id="savePropertyTypeBtn"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Save Property Type
                </button>

                <a
                    href="{{ route('property-types.index') }}"
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
        const form = document.getElementById('propertyTypeForm');
        const submitButton = document.getElementById('savePropertyTypeBtn');
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
                    'This property type will be available for selection.';
                statusDot.style.backgroundColor = '#198754';
            } else {
                statusText.textContent =
                    'This property type will be hidden from active selections.';
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
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';
        });

        updateStatusPreview();
        updateDescriptionCount();
    });
</script>

@endpush
