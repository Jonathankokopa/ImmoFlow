@extends('layouts.app')

@section('title', 'My Profile')
@section('page-heading', 'My Profile')

@section('content')

@php
    $user = auth()->user();

    $initials = collect(
        preg_split('/\s+/', trim($user->name))
    )
    ->filter()
    ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
    ->take(2)
    ->implode('');

    $profilePhoto = $user->profile_photo
        ? asset('storage/' . $user->profile_photo)
        : null;

    $roleLabel = ucwords(
        str_replace('_', ' ', $user->role ?? 'User')
    );
@endphp


<div class="profile-page">

    {{-- =========================================================
         ALERTS
    ========================================================== --}}

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm profile-alert" role="alert">

            <div class="d-flex align-items-center gap-3">

                <div class="alert-icon bg-success-subtle text-success">
                    <i class="bi bi-check-lg"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        Profile updated
                    </div>

                    <div class="small">
                        {{ session('status') }}
                    </div>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>

            </div>

        </div>
    @endif


    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm profile-alert" role="alert">

            <div class="d-flex align-items-start gap-3">

                <div class="alert-icon bg-danger-subtle text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>

                <div class="flex-grow-1">

                    <div class="fw-semibold mb-1">
                        Please correct the following
                    </div>

                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            </div>

        </div>
    @endif


    {{-- =========================================================
         PROFILE HERO
    ========================================================== --}}

    <div class="profile-hero">

        <div class="profile-hero-pattern pattern-one"></div>
        <div class="profile-hero-pattern pattern-two"></div>

        <div class="profile-hero-content">

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">

                <div class="d-flex align-items-center gap-3">

                    <div class="hero-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>

                    <div>

                        <div class="hero-eyebrow">
                            Account Settings
                        </div>

                        <h2 class="mb-1">
                            My Profile
                        </h2>

                        <p class="mb-0">
                            Manage your personal information, profile photo and account security.
                        </p>

                    </div>

                </div>


                <a
                    href="{{ route('dashboard') }}"
                    class="btn btn-light hero-dashboard-btn"
                >
                    <i class="bi bi-grid-1x2-fill me-2"></i>
                    Dashboard
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MAIN LAYOUT
    ========================================================== --}}

    <div class="row g-4 align-items-start">

        {{-- =====================================================
             LEFT PROFILE SUMMARY
        ====================================================== --}}

        <div class="col-12 col-xl-4">

            <div class="profile-card profile-summary-card">

                <div class="profile-summary-top">

                    <div class="profile-avatar-wrapper">

                        <div class="profile-avatar">

                            @if($profilePhoto)

                                <img
                                    src="{{ $profilePhoto }}"
                                    alt="{{ $user->name }}"
                                    id="profilePhotoPreview"
                                >

                                <div
                                    class="avatar-initials"
                                    id="profileInitials"
                                    style="display:none;"
                                >
                                    {{ $initials ?: 'U' }}
                                </div>

                            @else

                                <div
                                    class="avatar-initials"
                                    id="profileInitials"
                                >
                                    {{ $initials ?: 'U' }}
                                </div>

                                <img
                                    src=""
                                    alt="Profile photo"
                                    id="profilePhotoPreview"
                                    style="display:none;"
                                >

                            @endif

                        </div>

                        <div class="avatar-status">
                            <span></span>
                        </div>

                    </div>


                    <h4 class="profile-summary-name">
                        {{ $user->name }}
                    </h4>

                    <div class="profile-summary-email">
                        <i class="bi bi-envelope me-1"></i>
                        {{ $user->email }}
                    </div>


                    <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">

                        <span class="profile-role-badge">
                            <i class="bi bi-shield-check"></i>
                            {{ $roleLabel }}
                        </span>

                        <span class="profile-status-badge">
                            <span class="status-dot"></span>
                            Active
                        </span>

                    </div>

                </div>


                {{-- Profile metadata --}}
                <div class="profile-summary-body">

                    <div class="summary-item">

                        <div class="summary-item-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>

                        <div>
                            <div class="summary-item-label">
                                Member since
                            </div>

                            <div class="summary-item-value">
                                {{ $user->created_at?->format('d M Y') ?? '—' }}
                            </div>
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-item-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <div>
                            <div class="summary-item-label">
                                Last updated
                            </div>

                            <div class="summary-item-value">
                                {{ $user->updated_at?->diffForHumans() ?? '—' }}
                            </div>
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-item-icon">
                            <i class="bi bi-envelope-check"></i>
                        </div>

                        <div>

                            <div class="summary-item-label">
                                Email verification
                            </div>

                            <div class="summary-item-value">

                                @if($user->email_verified_at)

                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Verified
                                    </span>

                                @else

                                    <span class="text-warning-emphasis">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>
                                        Not verified
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Profile tip --}}
                <div class="profile-tip">

                    <div class="profile-tip-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>

                    <div>
                        <div class="fw-semibold">
                            Keep your profile current
                        </div>

                        <div class="small">
                            Use a clear profile photo and keep your contact information up to date.
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             RIGHT SETTINGS
        ====================================================== --}}

        <div class="col-12 col-xl-8">


            {{-- =================================================
                 PERSONAL INFORMATION
            ================================================== --}}

            <div class="profile-card mb-4">

                <div class="profile-card-header">

                    <div class="section-icon bg-primary-subtle text-primary">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>

                        <h5>
                            Personal Information
                        </h5>

                        <p>
                            Update the information associated with your account.
                        </p>

                    </div>

                </div>


                <div class="profile-card-body">

                    <form
                        method="POST"
                        action="{{ route('profile.update') }}"
                        enctype="multipart/form-data"
                        id="profileUpdateForm"
                    >

                        @csrf
                        @method('PATCH')


                        {{-- PROFILE PHOTO --}}
                        <div class="photo-section mb-4">

                            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">

                                <div class="mini-avatar">

                                    @if($profilePhoto)

                                        <img
                                            src="{{ $profilePhoto }}"
                                            alt="{{ $user->name }}"
                                            id="miniPhotoPreview"
                                        >

                                    @else

                                        <div
                                            class="mini-avatar-initials"
                                            id="miniInitials"
                                        >
                                            {{ $initials ?: 'U' }}
                                        </div>

                                        <img
                                            src=""
                                            alt="Profile photo"
                                            id="miniPhotoPreview"
                                            style="display:none;"
                                        >

                                    @endif

                                </div>


                                <div class="flex-grow-1">

                                    <div class="fw-semibold mb-1">
                                        Profile Photo
                                    </div>

                                    <div class="text-muted small mb-3">
                                        Choose a professional image for your account.
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">

                                        <label
                                            for="profile_photo"
                                            class="btn btn-outline-primary btn-sm"
                                        >
                                            <i class="bi bi-camera me-1"></i>
                                            Choose Photo
                                        </label>

                                        <input
                                            type="file"
                                            name="profile_photo"
                                            id="profile_photo"
                                            class="upload-input"
                                            accept="image/jpeg,image/png,image/webp"
                                        >


                                        @if($user->profile_photo)

                                            <button
                                                type="submit"
                                                name="remove_photo"
                                                value="1"
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                <i class="bi bi-trash me-1"></i>
                                                Remove Photo
                                            </button>

                                        @endif

                                    </div>

                                    <div class="photo-help">
                                        JPG, PNG or WebP · Maximum 2 MB
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="divider-label">
                            <span>Basic Details</span>
                        </div>


                        <div class="row g-4">

                            {{-- NAME --}}
                            <div class="col-md-6">

                                <label
                                    for="name"
                                    class="form-label"
                                >
                                    Full Name
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group profile-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Your full name"
                                        autocomplete="name"
                                        required
                                    >

                                </div>

                                @error('name')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- EMAIL --}}
                            <div class="col-md-6">

                                <label
                                    for="email"
                                    class="form-label"
                                >
                                    Email Address
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group profile-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="you@example.com"
                                        autocomplete="email"
                                        required
                                    >

                                </div>

                                @error('email')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        <div class="form-actions mt-4">

                            <a
                                href="{{ route('dashboard') }}"
                                class="btn btn-outline-secondary"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="saveProfileButton"
                            >
                                <i class="bi bi-check-lg me-2"></i>
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- =================================================
                 SECURITY
            ================================================== --}}

            <div class="profile-card mb-4">

                <div class="profile-card-header">

                    <div class="section-icon bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-shield-lock"></i>
                    </div>

                    <div>

                        <h5>
                            Account Security
                        </h5>

                        <p>
                            Protect your account with a strong, regularly updated password.
                        </p>

                    </div>

                </div>


                <div class="profile-card-body">

                    <div class="security-panel">

                        <div class="security-panel-icon">
                            <i class="bi bi-key"></i>
                        </div>

                        <div class="flex-grow-1">

                            <div class="fw-semibold">
                                Password
                            </div>

                            <div class="text-muted small mt-1">
                                Your password is securely encrypted and should never be shared.
                            </div>

                            <div class="security-status mt-2">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Password protected
                            </div>

                        </div>

                        <a
                            href="{{ route('password.request') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Change Password
                        </a>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 ACCOUNT INFORMATION
            ================================================== --}}

            <div class="profile-card">

                <div class="profile-card-header">

                    <div class="section-icon bg-info-subtle text-info-emphasis">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <div>

                        <h5>
                            Account Information
                        </h5>

                        <p>
                            Overview of your account status and access.
                        </p>

                    </div>

                </div>


                <div class="profile-card-body">

                    <div class="account-grid">

                        {{-- ROLE --}}
                        <div class="account-info-item">

                            <div class="account-info-icon bg-primary-subtle text-primary">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <div>

                                <div class="account-info-label">
                                    Account Role
                                </div>

                                <div class="account-info-value">
                                    {{ $roleLabel }}
                                </div>

                            </div>

                        </div>


                        {{-- STATUS --}}
                        <div class="account-info-item">

                            <div class="account-info-icon bg-success-subtle text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>

                            <div>

                                <div class="account-info-label">
                                    Account Status
                                </div>

                                <div class="account-info-value text-success">
                                    Active
                                </div>

                            </div>

                        </div>


                        {{-- VERIFICATION --}}
                        <div class="account-info-item">

                            <div class="account-info-icon bg-warning-subtle text-warning-emphasis">
                                <i class="bi bi-envelope-check"></i>
                            </div>

                            <div>

                                <div class="account-info-label">
                                    Email Verification
                                </div>

                                <div class="account-info-value">

                                    @if($user->email_verified_at)

                                        <span class="text-success">
                                            Verified
                                        </span>

                                    @else

                                        <span class="text-warning-emphasis">
                                            Not verified
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- CREATED --}}
                        <div class="account-info-item">

                            <div class="account-info-icon bg-secondary-subtle text-secondary">
                                <i class="bi bi-calendar3"></i>
                            </div>

                            <div>

                                <div class="account-info-label">
                                    Account Created
                                </div>

                                <div class="account-info-value">
                                    {{ $user->created_at?->format('d M Y') ?? '—' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


@push('styles')
<style>

    /* =========================================================
       PAGE
    ========================================================== */

    .profile-page {
        max-width: 1180px;
        margin: 0 auto;
    }


    /* =========================================================
       ALERTS
    ========================================================== */

    .profile-alert {
        border-radius: 14px;
        padding: 14px 18px;
    }

    .alert-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }


    /* =========================================================
       HERO
    ========================================================== */

    .profile-hero {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        margin-bottom: 24px;
        color: #fff;
        background:
            linear-gradient(
                135deg,
                #1e293b 0%,
                #111827 55%,
                #0f172a 100%
            );
        box-shadow: 0 14px 32px rgba(15, 23, 42, .12);
    }

    .profile-hero-content {
        position: relative;
        z-index: 3;
        padding: 30px 32px;
    }

    .profile-hero-pattern {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .pattern-one {
        width: 280px;
        height: 280px;
        right: -80px;
        top: -150px;
        background: rgba(255,255,255,.055);
    }

    .pattern-two {
        width: 180px;
        height: 180px;
        right: 120px;
        bottom: -130px;
        background: rgba(255,255,255,.035);
    }

    .hero-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.12);
        font-size: 1.45rem;
    }

    .hero-eyebrow {
        color: rgba(255,255,255,.62);
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .72rem;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .profile-hero h2 {
        font-size: 1.65rem;
        font-weight: 750;
    }

    .profile-hero p {
        color: rgba(255,255,255,.70);
        font-size: .92rem;
    }

    .hero-dashboard-btn {
        border: 0;
        font-weight: 600;
        padding: 10px 17px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,.12);
    }


    /* =========================================================
       CARDS
    ========================================================== */

    .profile-card {
        background: #fff;
        border: 1px solid #e7ebf0;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(15,23,42,.045);
        overflow: hidden;
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 20px 24px;
        border-bottom: 1px solid #edf0f3;
    }

    .profile-card-header h5 {
        color: #111827;
        font-weight: 700;
        margin: 0;
        font-size: 1rem;
    }

    .profile-card-header p {
        color: #6b7280;
        margin: 3px 0 0;
        font-size: .82rem;
    }

    .profile-card-body {
        padding: 24px;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }


    /* =========================================================
       PROFILE SUMMARY
    ========================================================== */

    .profile-summary-top {
        text-align: center;
        padding: 30px 24px 24px;
        border-bottom: 1px solid #edf0f3;
    }

    .profile-avatar-wrapper {
        position: relative;
        display: inline-flex;
        margin-bottom: 15px;
    }

    .profile-avatar {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2f7;
        border: 5px solid #fff;
        box-shadow:
            0 0 0 1px #e3e8ef,
            0 10px 28px rgba(15,23,42,.12);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-initials {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 750;
        color: #4338ca;
        background:
            linear-gradient(
                145deg,
                #eef2ff,
                #e0e7ff
            );
    }

    .avatar-status {
        position: absolute;
        right: 5px;
        bottom: 7px;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(15,23,42,.12);
    }

    .avatar-status span {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #22c55e;
        border: 2px solid #fff;
    }

    .profile-summary-name {
        color: #111827;
        font-size: 1.15rem;
        font-weight: 750;
        margin-bottom: 4px;
    }

    .profile-summary-email {
        color: #6b7280;
        font-size: .82rem;
        word-break: break-word;
    }

    .profile-role-badge,
    .profile-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: .72rem;
        font-weight: 700;
    }

    .profile-role-badge {
        background: #eef2ff;
        color: #4338ca;
    }

    .profile-status-badge {
        background: #ecfdf5;
        color: #047857;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .profile-summary-body {
        padding: 18px 24px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
    }

    .summary-item + .summary-item {
        border-top: 1px solid #f0f2f5;
    }

    .summary-item-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4f6f8;
        color: #64748b;
        font-size: .9rem;
    }

    .summary-item-label {
        color: #94a3b8;
        font-size: .72rem;
        margin-bottom: 2px;
    }

    .summary-item-value {
        color: #1f2937;
        font-weight: 650;
        font-size: .82rem;
    }

    .profile-tip {
        margin: 0 18px 18px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #edf0f3;
        color: #64748b;
    }

    .profile-tip-icon {
        width: 31px;
        height: 31px;
        min-width: 31px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #fff7ed;
        color: #ea580c;
    }

    .profile-tip .fw-semibold {
        color: #334155;
        font-size: .78rem;
    }


    /* =========================================================
       PHOTO SECTION
    ========================================================== */

    .photo-section {
        padding: 18px;
        border-radius: 13px;
        background: #f8fafc;
        border: 1px solid #edf0f3;
    }

    .mini-avatar {
        width: 70px;
        height: 70px;
        min-width: 70px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #e2e8f0;
    }

    .mini-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .mini-avatar-initials {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4338ca;
        font-weight: 750;
        font-size: 1.2rem;
    }

    .upload-input {
        display: none;
    }

    .photo-help {
        color: #94a3b8;
        font-size: .72rem;
        margin-top: 8px;
    }


    /* =========================================================
       FORM
    ========================================================== */

    .divider-label {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 22px 0 20px;
        color: #94a3b8;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    .divider-label::before,
    .divider-label::after {
        content: "";
        height: 1px;
        flex: 1;
        background: #edf0f3;
    }

    .form-label {
        color: #374151;
        font-size: .82rem;
        font-weight: 650;
        margin-bottom: 7px;
    }

    .form-control {
        min-height: 46px;
        border-radius: 9px;
        border-color: #dfe4ea;
        font-size: .88rem;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 .2rem rgba(99,102,241,.10);
    }

    .profile-input-group .input-group-text {
        background: #f8fafc;
        border-color: #dfe4ea;
        color: #94a3b8;
        border-radius: 9px 0 0 9px;
    }

    .profile-input-group .form-control {
        border-radius: 0 9px 9px 0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 18px;
        border-top: 1px solid #edf0f3;
    }

    .form-actions .btn,
    .security-panel .btn {
        border-radius: 9px;
        font-weight: 600;
    }


    /* =========================================================
       SECURITY
    ========================================================== */

    .security-panel {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border-radius: 13px;
        background: #f8fafc;
        border: 1px solid #edf0f3;
    }

    .security-panel-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff7ed;
        color: #d97706;
        font-size: 1.1rem;
    }

    .security-status {
        display: inline-flex;
        align-items: center;
        color: #15803d;
        font-size: .72rem;
        font-weight: 650;
    }


    /* =========================================================
       ACCOUNT INFORMATION
    ========================================================== */

    .account-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .account-info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        border: 1px solid #edf0f3;
        border-radius: 12px;
        background: #fafbfc;
    }

    .account-info-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .account-info-label {
        color: #94a3b8;
        font-size: .7rem;
        margin-bottom: 3px;
    }

    .account-info-value {
        color: #1f2937;
        font-size: .82rem;
        font-weight: 700;
    }


    /* =========================================================
       DARK MODE
    ========================================================== */

    html[data-theme="dark"] .profile-hero {
        background: linear-gradient(
            135deg,
            #172b46 0%,
            #0e1726 100%
        );
        box-shadow: 0 16px 34px rgba(0,0,0,.25);
    }

    html[data-theme="dark"] .profile-card {
        background: linear-gradient(
            145deg,
            #172338 0%,
            #141e2e 100%
        );
        border-color: #2c3d58;
        box-shadow: 0 12px 28px rgba(0,0,0,.18);
    }

    html[data-theme="dark"] .profile-card-header,
    html[data-theme="dark"] .profile-summary-top,
    html[data-theme="dark"] .form-actions,
    html[data-theme="dark"] .profile-summary-body {
        border-color: #2c3d58;
    }

    html[data-theme="dark"] .profile-card-header h5,
    html[data-theme="dark"] .profile-summary-name,
    html[data-theme="dark"] .summary-item-value,
    html[data-theme="dark"] .form-label,
    html[data-theme="dark"] .account-info-value {
        color: #f1f5f9;
    }

    html[data-theme="dark"] .profile-card-header p,
    html[data-theme="dark"] .profile-summary-email,
    html[data-theme="dark"] .summary-item-label {
        color: #aab8cc;
    }

    html[data-theme="dark"] .profile-avatar {
        background: #0e1726;
        border-color: #263b58;
        box-shadow:
            0 0 0 1px #3a506d,
            0 10px 28px rgba(0,0,0,.30);
    }

    html[data-theme="dark"] .avatar-initials {
        color: #5eead4;
        background: linear-gradient(
            145deg,
            #18334a,
            #15263a
        );
    }

    html[data-theme="dark"] .avatar-status {
        background: #172338;
    }

    html[data-theme="dark"] .avatar-status span {
        border-color: #172338;
    }

    html[data-theme="dark"] .profile-role-badge {
        background: rgba(129,140,248,.16);
        color: #c7d2fe;
    }

    html[data-theme="dark"] .profile-status-badge {
        background: rgba(74,222,128,.14);
        color: #86efac;
    }

    html[data-theme="dark"] .summary-item + .summary-item,
    html[data-theme="dark"] .divider-label::before,
    html[data-theme="dark"] .divider-label::after {
        background: #2c3d58;
    }

    html[data-theme="dark"] .summary-item-icon {
        background: #0f1a2b;
        color: #aab8cc;
    }

    html[data-theme="dark"] .profile-tip,
    html[data-theme="dark"] .photo-section,
    html[data-theme="dark"] .security-panel,
    html[data-theme="dark"] .account-info-item {
        background: #0f1a2b;
        border-color: #2c3d58;
    }

    html[data-theme="dark"] .profile-tip .fw-semibold,
    html[data-theme="dark"] .security-panel .fw-semibold {
        color: #f1f5f9;
    }

    html[data-theme="dark"] .profile-tip,
    html[data-theme="dark"] .photo-help {
        color: #aab8cc;
    }

    html[data-theme="dark"] .mini-avatar {
        background: #172338;
        border-color: #172338;
    }

    html[data-theme="dark"] .mini-avatar-initials {
        color: #c7d2fe;
    }

    html[data-theme="dark"] .profile-input-group .input-group-text {
        background: #0f1a2b;
        border-color: #3a506d;
        color: #8fa4bf;
    }

    html[data-theme="dark"] .form-control {
        background: #101c2d;
        border-color: #3a506d;
        color: #f1f5f9;
    }

    html[data-theme="dark"] .form-control::placeholder {
        color: #71839b;
    }

    html[data-theme="dark"] .security-panel-icon {
        background: rgba(245,158,11,.12);
        color: #fbbf24;
    }

    html[data-theme="dark"] .security-status {
        color: #86efac;
    }

    html[data-theme="dark"] .account-info-label {
        color: #8192aa;
    }

    html[data-theme="dark"] .account-info-icon {
        background-color: rgba(255,255,255,.06) !important;
    }

    html[data-theme="dark"] .btn-outline-primary,
    html[data-theme="dark"] .btn-outline-secondary {
        color: #bfdbfe;
        border-color: #49617f;
    }

    html[data-theme="dark"] .btn-outline-primary:hover,
    html[data-theme="dark"] .btn-outline-secondary:hover {
        color: #fff;
        background: #263b58;
        border-color: #6b86a8;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 991.98px) {

        .profile-hero-content {
            padding: 26px;
        }

        .profile-hero h2 {
            font-size: 1.45rem;
        }

    }

    @media (max-width: 767.98px) {

        .profile-hero-content {
            padding: 22px;
        }

        .hero-dashboard-btn {
            width: 100%;
        }

        .profile-card-body {
            padding: 20px;
        }

        .profile-card-header {
            padding: 18px 20px;
        }

        .security-panel {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .security-panel .btn {
            width: 100%;
        }

        .account-grid {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 575.98px) {

        .profile-hero {
            border-radius: 15px;
        }

        .profile-hero-content {
            padding: 20px;
        }

        .hero-icon {
            width: 46px;
            height: 46px;
            min-width: 46px;
        }

        .profile-hero h2 {
            font-size: 1.3rem;
        }

        .profile-hero p {
            font-size: .82rem;
        }

        .profile-summary-top {
            padding: 26px 18px 20px;
        }

        .profile-summary-body {
            padding: 16px 18px;
        }

        .profile-tip {
            margin: 0 14px 14px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            width: 100%;
        }

    }

</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const photoInput = document.getElementById('profile_photo');

        const photoPreview = document.getElementById('profilePhotoPreview');
        const initials = document.getElementById('profileInitials');

        const miniPreview = document.getElementById('miniPhotoPreview');
        const miniInitials = document.getElementById('miniInitials');

        const form = document.getElementById('profileUpdateForm');
        const saveButton = document.getElementById('saveProfileButton');


        /*
        |--------------------------------------------------------------------------
        | Profile Photo Preview
        |--------------------------------------------------------------------------
        */

        if (photoInput) {

            photoInput.addEventListener('change', function (event) {

                const file = event.target.files[0];

                if (!file) {
                    return;
                }


                if (!file.type.startsWith('image/')) {

                    alert('Please select a valid image.');

                    photoInput.value = '';

                    return;
                }


                if (file.size > 2 * 1024 * 1024) {

                    alert('The profile photo must be 2 MB or smaller.');

                    photoInput.value = '';

                    return;
                }


                const reader = new FileReader();


                reader.onload = function (e) {

                    if (photoPreview) {

                        photoPreview.src = e.target.result;
                        photoPreview.style.display = 'block';

                    }

                    if (initials) {
                        initials.style.display = 'none';
                    }


                    if (miniPreview) {

                        miniPreview.src = e.target.result;
                        miniPreview.style.display = 'block';

                    }

                    if (miniInitials) {
                        miniInitials.style.display = 'none';
                    }

                };


                reader.readAsDataURL(file);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Double Submit
        |--------------------------------------------------------------------------
        */

        if (form && saveButton) {

            form.addEventListener('submit', function (event) {

                /*
                 * Do not disable the button when the user is removing
                 * the existing profile photo. The server needs the
                 * remove_photo field to be submitted normally.
                 */
                const submitter = event.submitter;

                if (
                    submitter &&
                    submitter.name === 'remove_photo'
                ) {
                    return;
                }


                saveButton.disabled = true;

                saveButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>' +
                    'Saving...';

            });

        }

    });
</script>
@endpush

@endsection