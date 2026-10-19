@extends('layouts.app')

@section('title', 'Edit Owner')
@section('page-heading', 'Edit Property Owner')

@push('styles')
<style>
    .owner-edit-page {
        --owner-border: #e9ecef;
        --owner-primary: var(--bs-primary, #0d6efd);
    }

    .owner-edit-header {
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
        border: 1px solid var(--owner-border);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .owner-edit-header::after {
        content: "";
        position: absolute;
        width: 200px;
        height: 200px;
        right: -80px;
        top: -100px;
        border-radius: 50%;
        background: rgba(13, 110, 253, .06);
        pointer-events: none;
    }

    .owner-header-icon {
        width: 50px;
        height: 50px;
        flex: 0 0 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .9rem;
        background: rgba(13, 110, 253, .1);
        color: var(--owner-primary);
        font-size: 1.3rem;
    }

    .owner-eyebrow {
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

    .owner-card-body {
        padding: 1.35rem;
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

    .owner-form-label {
        display: block;
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

    .owner-profile-card {
        position: sticky;
        top: 1rem;
    }

    .owner-mini-profile {
        padding: 1.25rem;
        border: 1px solid var(--owner-border);
        border-radius: .9rem;
        background: linear-gradient(145deg, #f8fbff, #ffffff);
        text-align: center;
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

    .owner-profile-name {
        color: #172033;
        font-weight: 700;
        word-break: break-word;
    }

    .owner-profile-item {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .7rem 0;
        border-top: 1px solid var(--owner-border);
        color: #667085;
        font-size: .8rem;
        text-align: left;
    }

    .owner-profile-item i {
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

    .owner-btn {
        min-height: 42px;
        border-radius: .7rem;
        font-weight: 600;
    }

    .owner-alert {
        border: 0;
        border-radius: .85rem;
    }

    .owner-alert ul {
        padding-left: 1.2rem;
    }

    @media (max-width: 991.98px) {
        .owner-profile-card {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .owner-edit-header {
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

    html[data-theme="dark"] .owner-edit-header,
    html[data-theme="dark"] .owner-card,
    html[data-theme="dark"] .owner-action-bar {
        background: #151922;
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-edit-header {
        background: linear-gradient(135deg, #151922 0%, #111827 100%);
    }

    html[data-theme="dark"] .owner-page-title,
    html[data-theme="dark"] .owner-profile-name {
        color: #f1f5f9;
    }

    html[data-theme="dark"] .owner-card-header {
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

    html[data-theme="dark"] .owner-mini-profile {
        background: linear-gradient(145deg, #111827, #151922);
        border-color: #29303d;
    }

    html[data-theme="dark"] .owner-profile-item {
        border-color: #29303d;
        color: #aeb8c6;
    }

    html[data-theme="dark"] .owner-action-note,
    html[data-theme="dark"] .owner-field-help {
        color: #8f9bad;
    }
</style>
@endpush

@section('content')
<div class="container-fluid owner-edit-page px-0">

    {{-- Page Header --}}
    <div class="owner-edit-header mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 position-relative">

            <div class="d-flex align-items-start gap-3">
                <div class="owner-header-icon">
                    <i class="bi bi-person-gear"></i>
                </div>

                <div>
                    <div class="owner-eyebrow mb-1">
                        Property Management / Edit #{{ $owner->id }}
                    </div>

                    <h1 class="h3 mb-1 fw-bold owner-page-title">
                        Edit Property Owner
                    </h1>

                    <p class="text-muted mb-0">
                        Update the information and contact details for
                        <strong>{{ $owner->full_name }}</strong>.
                    </p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('owners.show', $owner) }}"
                   class="btn btn-outline-secondary owner-btn">
                    <i class="bi bi-eye me-1"></i>
                    View Owner
                </a>

                <a href="{{ route('owners.index') }}"
                   class="btn btn-outline-secondary owner-btn">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Owners
                </a>
            </div>

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

    <form action="{{ route('owners.update', $owner) }}"
          method="POST"
          enctype="multipart/form-data"
          id="ownerEditForm">

        @csrf
        @method('PUT')

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
                                    Owner identity and contact details
                                </small>
                            </div>
                        </div>

                        <span class="badge rounded-pill text-bg-light border">
                            Profile
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
                                    value="{{ old('full_name', $owner->full_name) }}"
                                    autocomplete="name"
                                    required
                                >

                                <div class="owner-field-help">
                                    The owner's full legal name.
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
                                    value="{{ old('national_id', $owner->national_id) }}"
                                    autocomplete="off"
                                >

                                <div class="owner-field-help">
                                    Government-issued identification, if available.
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

                                @error('id_document')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @if($owner->id_document_path)
                                    <div class="mt-2 small">
                                        <i class="bi bi-file-earmark-check text-success me-1"></i>
                                        <span>Current Document:</span>
                                        <a href="{{ Storage::url($owner->id_document_path) }}" target="_blank" class="fw-semibold text-decoration-none ms-1">
                                            View / Download Attached Document
                                        </a>
                                    </div>
                                @else
                                    <div class="owner-field-help">
                                        Upload photo or scan of National ID/Passport (JPG, PNG, PDF, max 10MB).
                                    </div>
                                @endif
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
                                    value="{{ old('phone', $owner->phone) }}"
                                    autocomplete="tel"
                                >

                                <div class="owner-field-help">
                                    Prefer an international format where possible.
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
                                    value="{{ old('email', $owner->email) }}"
                                    autocomplete="email"
                                >

                                <div class="owner-field-help">
                                    Used for correspondence and owner records.
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
                                    Location information and management remarks
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
                                >{{ old('address', $owner->address) }}</textarea>

                                <div class="owner-field-help">
                                    Update the owner's residential, business, or mailing address.
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
                                    placeholder="Additional notes, agreements, preferences, or relevant information"
                                >{{ old('notes', $owner->notes) }}</textarea>

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

                <div class="owner-profile-card">

                    {{-- Current Owner --}}
                    <div class="owner-card mb-4">
                        <div class="owner-card-header">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    Current Owner
                                </h6>

                                <small class="text-muted">
                                    Profile summary
                                </small>
                            </div>

                            <i class="bi bi-person-check text-primary"></i>
                        </div>

                        <div class="owner-card-body">
                            @php
                                $initials = collect(
                                    preg_split('/\s+/', trim($owner->full_name))
                                )
                                ->filter()
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->take(2)
                                ->implode('');
                            @endphp

                            <div class="owner-mini-profile">

                                <div class="owner-avatar">
                                    {{ $initials ?: '?' }}
                                </div>

                                <div class="owner-profile-name">
                                    {{ $owner->full_name }}
                                </div>

                                <div class="small text-muted mt-1">
                                    Owner #{{ $owner->id }}
                                </div>

                                <div class="mt-3 text-start">

                                    <div class="owner-profile-item">
                                        <i class="bi bi-buildings"></i>
                                        <span>
                                            {{ $owner->properties->count() }}
                                            {{ $owner->properties->count() === 1 ? 'property' : 'properties' }}
                                            registered
                                        </span>
                                    </div>

                                    <div class="owner-profile-item">
                                        <i class="bi bi-telephone"></i>
                                        <span>
                                            {{ $owner->phone ?: 'No phone number' }}
                                        </span>
                                    </div>

                                    <div class="owner-profile-item">
                                        <i class="bi bi-envelope"></i>
                                        <span>
                                            {{ $owner->email ?: 'No email address' }}
                                        </span>
                                    </div>

                                    <div class="owner-profile-item">
                                        <i class="bi bi-calendar3"></i>
                                        <span>
                                            Member since
                                            {{ $owner->created_at ? $owner->created_at->format('d M Y') : '—' }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Editing Guidance --}}
                    <div class="owner-card">
                        <div class="owner-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle text-primary"></i>

                                <h6 class="fw-bold mb-0">
                                    Editing Information
                                </h6>
                            </div>
                        </div>

                        <div class="owner-card-body">
                            <ul class="small text-muted mb-0 ps-3">
                                <li class="mb-2">
                                    Changes will update the owner's master record.
                                </li>

                                <li class="mb-2">
                                    Property associations remain unchanged.
                                </li>

                                <li>
                                    Review the information before saving your changes.
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

                <span>
                    Review the updated owner information before saving.
                </span>
            </div>

            <div class="d-flex flex-wrap gap-2">

                <a href="{{ route('owners.show', $owner) }}"
                   class="btn btn-outline-secondary owner-btn">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary owner-btn"
                        id="updateOwnerButton">

                    <i class="bi bi-check-lg me-1"></i>

                    <span id="updateOwnerText">
                        Update Owner
                    </span>
                </button>

            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ownerEditForm');
    const button = document.getElementById('updateOwnerButton');
    const text = document.getElementById('updateOwnerText');

    if (form && button) {
        form.addEventListener('submit', function () {
            button.disabled = true;
            text.textContent = 'Updating...';

            const icon = button.querySelector('i');

            if (icon) {
                icon.className = 'bi bi-arrow-repeat me-1';
            }
        });
    }
});
</script>
@endpush