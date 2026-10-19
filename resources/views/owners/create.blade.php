@extends('layouts.app')

@section('title', 'Add Owner')
@section('page-heading', 'Add Property Owner')

@push('styles')
<style>
    .owner-create-page {
        --owner-primary: var(--bs-primary, #0d6efd);
        --owner-border: #e9ecef;
        --owner-muted: #6c757d;
        --owner-soft: #f8f9fa;
    }

    .owner-create-header {
        position: relative;
        padding: 1.5rem;
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        overflow: hidden;
    }

    .owner-create-header::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -70px;
        top: -80px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.06);
        pointer-events: none;
    }

    .owner-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        margin-bottom: .45rem;
        color: var(--owner-primary);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .owner-page-title {
        color: #172033;
        letter-spacing: -.025em;
    }

    .owner-header-description {
        max-width: 650px;
        font-size: .92rem;
    }

    .owner-header-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .9rem;
        background: rgba(13, 110, 253, .1);
        color: var(--owner-primary);
        font-size: 1.35rem;
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
        background: #fff;
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

    .owner-form-label {
        display: flex;
        align-items: center;
        gap: .2rem;
        margin-bottom: .45rem;
        color: #344054;
        font-size: .82rem;
        font-weight: 650;
    }

    .owner-form-label .required {
        color: #dc3545;
    }

    .owner-form-control {
        min-height: 45px;
        border-color: #dfe3e8;
        border-radius: .7rem;
        font-size: .9rem;
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    textarea.owner-form-control {
        min-height: auto;
        resize: vertical;
    }

    .owner-form-control:focus {
        border-color: rgba(13, 110, 253, .55);
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .1);
    }

    .owner-form-control::placeholder {
        color: #adb5bd;
    }

    .owner-field-help {
        margin-top: .35rem;
        color: #8a94a6;
        font-size: .73rem;
    }

    .owner-side-card {
        position: sticky;
        top: 1rem;
    }

    .owner-preview {
        padding: 1.25rem;
        border-radius: .9rem;
        border: 1px solid var(--owner-border);
        background: linear-gradient(145deg, #f8fbff, #ffffff);
    }

    .owner-avatar {
        width: 68px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #4dabf7);
        color: #fff;
        font-size: 1.35rem;
        font-weight: 700;
        box-shadow: 0 .5rem 1.25rem rgba(13, 110, 253, .18);
    }

    .owner-preview-name {
        color: #172033;
        font-weight: 700;
        word-break: break-word;
    }

    .owner-preview-placeholder {
        color: #8a94a6;
    }

    .owner-info-list {
        margin: 1.25rem 0 0;
        padding: 0;
        list-style: none;
    }

    .owner-info-list li {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        padding: .7rem 0;
        border-top: 1px solid var(--owner-border);
        color: #667085;
        font-size: .82rem;
    }

    .owner-info-list li i {
        color: var(--owner-primary);
        margin-top: .1rem;
    }

    .owner-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 .25rem 1rem rgba(20, 35, 55, .035);
    }

    .owner-action-note {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: #7a8594;
        font-size: .78rem;
    }

    .owner-btn-primary {
        min-height: 42px;
        padding: .55rem 1rem;
        border-radius: .7rem;
        font-weight: 600;
        box-shadow: 0 .3rem .8rem rgba(13, 110, 253, .12);
    }

    .owner-btn-outline {
        min-height: 42px;
        padding: .55rem 1rem;
        border-radius: .7rem;
        font-weight: 600;
    }

    .owner-alert {
        border: 0;
        border-radius: .85rem;
        box-shadow: 0 .25rem .8rem rgba(20, 35, 55, .04);
    }

    .owner-alert ul {
        padding-left: 1.2rem;
    }

    @media (max-width: 991.98px) {
        .owner-side-card {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .owner-create-header {
            padding: 1.1rem;
        }

        .owner-card-body {
            padding: 1rem;
        }

        .owner-card-header {
            padding: 1rem;
        }

        .owner-action-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .owner-action-bar .d-flex {
            width: 100%;
        }

        .owner-action-bar .btn {
            flex: 1;
        }
    }

    html[data-theme="dark"] .owner-create-header,
    html[data-theme="dark"] .owner-card,
    html[data-theme="dark"] .owner-action-bar {
        background: #151922;
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-create-header {
        background: linear-gradient(135deg, #151922 0%, #111827 100%);
    }

    html[data-theme="dark"] .owner-page-title,
    html[data-theme="dark"] .owner-preview-name {
        color: #f1f5f9;
    }

    html[data-theme="dark"] .owner-card-header {
        background: #151922;
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-form-label {
        color: #d9e0ea;
    }

    html[data-theme="dark"] .owner-form-control {
        background: #10141c;
        border-color: #343c4a;
        color: #e8edf5;
    }

    html[data-theme="dark"] .owner-form-control::placeholder {
        color: #697386;
    }

    html[data-theme="dark"] .owner-preview {
        background: linear-gradient(145deg, #111827, #151922);
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-info-list li {
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-action-note,
    html[data-theme="dark"] .owner-field-help {
        color: #8f9bad;
    }
</style>
@endpush

@section('content')
<div class="container-fluid owner-create-page px-0">

    {{-- Page Header --}}
    <div class="owner-create-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
            <div class="d-flex align-items-start gap-3">
                <div class="owner-header-icon">
                    <i class="bi bi-person-plus"></i>
                </div>

                <div>
                    <div class="owner-eyebrow">
                        <i class="bi bi-buildings"></i>
                        Property Management / New Owner
                    </div>

                    <h1 class="h3 mb-1 fw-bold owner-page-title">
                        Add Property Owner
                    </h1>

                    <p class="text-muted owner-header-description mb-0">
                        Create an owner profile and keep their contact and identification
                        information organized in one place.
                    </p>
                </div>
            </div>

            <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary owner-btn-outline">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Owners
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger owner-alert mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                <div>
                    <div class="fw-semibold mb-1">
                        Please correct the following errors:
                    </div>

                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('owners.store') }}" method="POST" enctype="multipart/form-data" id="ownerCreateForm">
        @csrf

        <div class="row g-4">

            {{-- Main Form --}}
            <div class="col-xl-8">

                {{-- Personal Information --}}
                <div class="owner-card mb-4">
                    <div class="owner-card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="owner-section-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    Personal Information
                                </h5>
                                <small class="text-muted">
                                    Owner identity and identification details
                                </small>
                            </div>
                        </div>

                        <span class="badge rounded-pill text-bg-light border">
                            Required: Name
                        </span>
                    </div>

                    <div class="owner-card-body">
                        <div class="row g-3">

                            {{-- Full Name --}}
                            <div class="col-md-6">
                                <label for="full_name" class="owner-form-label">
                                    Full Name
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="full_name"
                                    id="full_name"
                                    class="form-control owner-form-control @error('full_name') is-invalid @enderror"
                                    value="{{ old('full_name') }}"
                                    placeholder="e.g. Robert Smith"
                                    autocomplete="name"
                                    required
                                >

                                <div class="owner-field-help">
                                    Enter the owner's full legal name.
                                </div>

                                @error('full_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- National ID --}}
                            <div class="col-md-6">
                                <label for="national_id" class="owner-form-label">
                                    National ID / Passport Number
                                </label>

                                <input
                                    type="text"
                                    name="national_id"
                                    id="national_id"
                                    class="form-control owner-form-control @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id') }}"
                                    placeholder="e.g. ID98765432"
                                    autocomplete="off"
                                >

                                <div class="owner-field-help">
                                    Optional government-issued identification.
                                </div>

                                @error('national_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- ID / Passport Document or Photo --}}
                            <div class="col-md-6">
                                <label for="id_document" class="owner-form-label">
                                    ID / Passport Document or Photo
                                </label>

                                <input
                                    type="file"
                                    name="id_document"
                                    id="id_document"
                                    class="form-control owner-form-control @error('id_document') is-invalid @enderror"
                                    accept="image/*,.pdf,.doc,.docx"
                                >

                                <div class="owner-field-help">
                                    Upload photo or scan of National ID/Passport (JPG, PNG, PDF, max 10MB).
                                </div>

                                @error('id_document')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone" class="owner-form-label">
                                    Phone Number
                                </label>

                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    class="form-control owner-form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="e.g. +254 700 123 456"
                                    autocomplete="tel"
                                >

                                <div class="owner-field-help">
                                    Use an international format where possible.
                                </div>

                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="owner-form-label">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control owner-form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="e.g. owner@example.com"
                                    autocomplete="email"
                                >

                                <div class="owner-field-help">
                                    Used for owner correspondence and records.
                                </div>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Address & Notes --}}
                <div class="owner-card mb-4">
                    <div class="owner-card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="owner-section-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    Address & Notes
                                </h5>
                                <small class="text-muted">
                                    Additional contact information and management remarks
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="owner-card-body">
                        <div class="row g-3">

                            {{-- Address --}}
                            <div class="col-12">
                                <label for="address" class="owner-form-label">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    id="address"
                                    rows="3"
                                    class="form-control owner-form-control @error('address') is-invalid @enderror"
                                    placeholder="Physical or business address"
                                >{{ old('address') }}</textarea>

                                <div class="owner-field-help">
                                    Add a residential, business, or mailing address if available.
                                </div>

                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label for="notes" class="owner-form-label">
                                    Notes
                                </label>

                                <textarea
                                    name="notes"
                                    id="notes"
                                    rows="4"
                                    class="form-control owner-form-control @error('notes') is-invalid @enderror"
                                    placeholder="Additional notes, agreements, preferences, or other relevant information"
                                >{{ old('notes') }}</textarea>

                                <div class="owner-field-help">
                                    Keep internal notes concise and relevant to property management.
                                </div>

                                @error('notes')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-xl-4">

                <div class="owner-side-card">

                    {{-- Owner Preview --}}
                    <div class="owner-card mb-4">
                        <div class="owner-card-header">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    Owner Preview
                                </h6>
                                <small class="text-muted">
                                    Profile summary
                                </small>
                            </div>

                            <i class="bi bi-eye text-muted"></i>
                        </div>

                        <div class="owner-card-body">
                            <div class="owner-preview text-center">

                                <div class="owner-avatar" id="ownerAvatar">
                                    <span id="ownerInitials">?</span>
                                </div>

                                <div class="owner-preview-name" id="ownerPreviewName">
                                    <span class="owner-preview-placeholder">
                                        New Property Owner
                                    </span>
                                </div>

                                <div class="small text-muted mt-1" id="ownerPreviewEmail">
                                    No email provided
                                </div>

                                <ul class="owner-info-list text-start">
                                    <li>
                                        <i class="bi bi-person-badge"></i>
                                        <span id="ownerPreviewId">
                                            No identification number
                                        </span>
                                    </li>

                                    <li>
                                        <i class="bi bi-telephone"></i>
                                        <span id="ownerPreviewPhone">
                                            No phone number
                                        </span>
                                    </li>

                                    <li>
                                        <i class="bi bi-geo-alt"></i>
                                        <span id="ownerPreviewAddress">
                                            No address provided
                                        </span>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    {{-- Information --}}
                    <div class="owner-card mb-4">
                        <div class="owner-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle text-primary"></i>
                                <h6 class="fw-bold mb-0">
                                    Before You Save
                                </h6>
                            </div>
                        </div>

                        <div class="owner-card-body">
                            <ul class="small text-muted mb-0 ps-3">
                                <li class="mb-2">
                                    Make sure the owner's full name is accurate.
                                </li>
                                <li class="mb-2">
                                    Identification and contact details can be added later if unavailable now.
                                </li>
                                <li>
                                    Owner information can be edited after creation.
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="owner-action-bar mt-2 mb-4">
            <div class="owner-action-note">
                <i class="bi bi-shield-check"></i>
                <span>Your owner record will be added to the management database.</span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('owners.index') }}"
                   class="btn btn-outline-secondary owner-btn-outline">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary owner-btn-primary"
                        id="saveOwnerButton">
                    <i class="bi bi-check-lg me-1"></i>
                    <span id="saveOwnerText">Save Owner</span>
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ownerCreateForm');
    const nameInput = document.getElementById('full_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const idInput = document.getElementById('national_id');
    const addressInput = document.getElementById('address');

    const initials = document.getElementById('ownerInitials');
    const previewName = document.getElementById('ownerPreviewName');
    const previewEmail = document.getElementById('ownerPreviewEmail');
    const previewPhone = document.getElementById('ownerPreviewPhone');
    const previewId = document.getElementById('ownerPreviewId');
    const previewAddress = document.getElementById('ownerPreviewAddress');

    const saveButton = document.getElementById('saveOwnerButton');
    const saveText = document.getElementById('saveOwnerText');

    function getInitials(name) {
        const parts = name.trim().split(/\s+/).filter(Boolean);

        if (!parts.length) {
            return '?';
        }

        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }

        return (
            parts[0].charAt(0) +
            parts[parts.length - 1].charAt(0)
        ).toUpperCase();
    }

    function updatePreview() {
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const id = idInput.value.trim();
        const address = addressInput.value.trim();

        initials.textContent = getInitials(name);

        previewName.innerHTML = name
            ? escapeHtml(name)
            : '<span class="owner-preview-placeholder">New Property Owner</span>';

        previewEmail.textContent = email || 'No email provided';
        previewPhone.textContent = phone || 'No phone number';
        previewId.textContent = id || 'No identification number';
        previewAddress.textContent = address || 'No address provided';
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    [
        nameInput,
        emailInput,
        phoneInput,
        idInput,
        addressInput
    ].forEach(function (input) {
        input.addEventListener('input', updatePreview);
    });

    if (form) {
        form.addEventListener('submit', function () {
            if (!saveButton) {
                return;
            }

            saveButton.disabled = true;
            saveText.textContent = 'Saving...';

            saveButton.querySelector('i').className =
                'bi bi-arrow-repeat me-1';
        });
    }

    updatePreview();
});
</script>
@endpush