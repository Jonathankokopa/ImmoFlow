@extends('portal.layout')

@section('title', 'Change Password')

@section('page-heading', 'Change Password')

@section('content')

<div class="container-fluid">


    {{-- ================================================================== --}}
    {{-- PAGE HEADER --}}
    {{-- ================================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Change Password
            </h2>

            <p class="text-muted mb-0">
                Keep your tenant portal account secure.
            </p>

        </div>


        <span class="badge bg-primary px-3 py-2">

            <i class="bi bi-shield-lock me-1"></i>

            Security

        </span>

    </div>


    {{-- ================================================================== --}}
    {{-- CONTENT --}}
    {{-- ================================================================== --}}

    <div class="row g-4">


        {{-- ================================================================ --}}
        {{-- PASSWORD FORM --}}
        {{-- ================================================================ --}}

        <div class="col-lg-7">

            <div class="card portal-card h-100">


                <div class="card-header bg-white py-3">

                    <h5 class="mb-1 fw-bold">

                        <i class="bi bi-key me-2 text-primary"></i>

                        Update Password

                    </h5>

                    <p class="text-muted small mb-0">

                        Enter your current password and choose a new password.

                    </p>

                </div>


                <div class="card-body p-4">

                    <form
                        method="POST"
                        action="{{ route('portal.password.update') }}"
                    >

                        @csrf

                        @method('PUT')


                        {{-- ================================================== --}}
                        {{-- CURRENT PASSWORD --}}
                        {{-- ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="current_password"
                                class="form-label fw-semibold"
                            >

                                Current Password

                            </label>


                            <div class="input-group">

                                <span class="input-group-text bg-light">

                                    <i class="bi bi-lock"></i>

                                </span>


                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    autocomplete="current-password"
                                    required
                                >

                            </div>


                            @error('current_password')

                                <div class="text-danger small mt-1">

                                    {{ $message }}

                                </div>

                            @enderror

                        </div>


                        {{-- ================================================== --}}
                        {{-- NEW PASSWORD --}}
                        {{-- ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="password"
                                class="form-label fw-semibold"
                            >

                                New Password

                            </label>


                            <div class="input-group">

                                <span class="input-group-text bg-light">

                                    <i class="bi bi-shield-lock"></i>

                                </span>


                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    autocomplete="new-password"
                                    required
                                >

                            </div>


                            @error('password')

                                <div class="text-danger small mt-1">

                                    {{ $message }}

                                </div>

                            @enderror

                        </div>


                        {{-- ================================================== --}}
                        {{-- CONFIRM PASSWORD --}}
                        {{-- ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="password_confirmation"
                                class="form-label fw-semibold"
                            >

                                Confirm New Password

                            </label>


                            <div class="input-group">

                                <span class="input-group-text bg-light">

                                    <i class="bi bi-check2-shield"></i>

                                </span>


                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    autocomplete="new-password"
                                    required
                                >

                            </div>

                        </div>


                        {{-- ================================================== --}}
                        {{-- BUTTON --}}
                        {{-- ================================================== --}}

                        <div class="d-flex justify-content-end">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-shield-check me-1"></i>

                                Change Password

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- ================================================================ --}}
        {{-- SECURITY INFORMATION --}}
        {{-- ================================================================ --}}

        <div class="col-lg-5">

            <div class="card portal-card h-100">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-1 fw-bold">

                        <i class="bi bi-shield-check me-2 text-success"></i>

                        Password Security

                    </h5>

                    <p class="text-muted small mb-0">

                        Tips for keeping your account secure.

                    </p>

                </div>


                <div class="card-body p-4">


                    <div class="d-flex align-items-start mb-3">

                        <div
                            class="bg-success bg-opacity-10 text-success rounded p-2 me-3"
                        >

                            <i class="bi bi-check-circle"></i>

                        </div>


                        <div>

                            <div class="fw-semibold">
                                Use a strong password
                            </div>

                            <div class="text-muted small">
                                Use at least 8 characters.
                            </div>

                        </div>

                    </div>


                    <div class="d-flex align-items-start mb-3">

                        <div
                            class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3"
                        >

                            <i class="bi bi-person-lock"></i>

                        </div>


                        <div>

                            <div class="fw-semibold">
                                Keep it private
                            </div>

                            <div class="text-muted small">
                                Never share your password with anyone.
                            </div>

                        </div>

                    </div>


                    <div class="d-flex align-items-start mb-3">

                        <div
                            class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3"
                        >

                            <i class="bi bi-exclamation-triangle"></i>

                        </div>


                        <div>

                            <div class="fw-semibold">
                                Avoid personal information
                            </div>

                            <div class="text-muted small">
                                Don't use easily guessed information.
                            </div>

                        </div>

                    </div>


                    <div class="d-flex align-items-start">

                        <div
                            class="bg-info bg-opacity-10 text-info rounded p-2 me-3"
                        >

                            <i class="bi bi-key"></i>

                        </div>


                        <div>

                            <div class="fw-semibold">
                                Use a unique password
                            </div>

                            <div class="text-muted small">
                                Avoid reusing passwords from other accounts.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection