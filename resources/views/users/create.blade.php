@extends('layouts.app')

@section('title', 'Add User')
@section('page-heading', 'Add User')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger-subtle text-danger p-2 me-3">
                <i class="bi bi-exclamation-lg"></i>
            </div>

            <div class="flex-grow-1">
                <div class="fw-semibold">Unable to create user</div>
                <div class="small">{{ session('error') }}</div>
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
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start">
            <div class="rounded-circle bg-danger-subtle text-danger p-2 me-3">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div>
                <div class="fw-semibold mb-1">
                    Please correct the following:
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


{{-- Page Header --}}
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span
                class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                style="width:42px;height:42px;"
            >
                <i class="bi bi-person-plus fs-5"></i>
            </span>

            <h2 class="mb-0 fw-bold">
                Add User
            </h2>
        </div>

        <p class="text-muted mb-0">
            Create a new system user and assign the appropriate role.
        </p>
    </div>

    <a
        href="{{ route('users.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Back to Users
    </a>

</div>


<form
    action="{{ route('users.store') }}"
    method="POST"
    id="createUserForm"
>
    @csrf

    <div class="row g-4">

        {{-- Account Details --}}
        <div class="col-12 col-xl-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom p-4">

                    <div class="d-flex align-items-center">

                        <div class="rounded-3 bg-primary-subtle text-primary p-2 me-3">
                            <i class="bi bi-person fs-5"></i>
                        </div>

                        <div>
                            <h5 class="fw-bold mb-1">
                                Account Details
                            </h5>

                            <p class="text-muted small mb-0">
                                Enter the new user's basic information.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- Full Name --}}
                        <div class="col-md-6">

                            <label for="name" class="form-label fw-semibold">
                                Full Name
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person text-muted"></i>
                                </span>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="e.g. John Doe"
                                    autocomplete="name"
                                    required
                                    autofocus
                                >

                            </div>

                            @error('name')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Email --}}
                        <div class="col-md-6">

                            <label for="email" class="form-label fw-semibold">
                                Email Address
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="name@example.com"
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


                        {{-- Role --}}
                        <div class="col-12">

                            <label for="role" class="form-label fw-semibold">
                                User Role
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="role"
                                id="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Select a role
                                </option>

                                @foreach($roles as $role)

                                    @php
                                        $optionLabel = match ($role) {
                                            'administrator' => 'Administrator',
                                            'property_manager' => 'Property Manager',
                                            'accountant' => 'Accountant',
                                            'maintenance_staff' => 'Maintenance Staff',
                                            'reception_staff' => 'Reception Staff',
                                            default => ucwords(str_replace('_', ' ', $role)),
                                        };
                                    @endphp

                                    <option
                                        value="{{ $role }}"
                                        @selected(old('role') === $role)
                                    >
                                        {{ $optionLabel }}
                                    </option>

                                @endforeach

                            </select>

                            @error('role')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                <i class="bi bi-shield-check me-1"></i>
                                The selected role determines the user's system permissions.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- System Access --}}
        <div class="col-12 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-bottom p-4">

                    <h5 class="fw-bold mb-1">
                        System Access
                    </h5>

                    <p class="text-muted small mb-0">
                        Important account information.
                    </p>

                </div>

                <div class="card-body p-4">

                    <div class="mb-4">

                        <div class="fw-semibold mb-2">
                            System users only
                        </div>

                        <p class="text-muted small mb-0">
                            This form is intended for administrators and
                            staff members who need access to the management
                            system.
                        </p>

                    </div>

                    <div class="alert alert-info border-0 small mb-4">

                        <div class="d-flex align-items-start">

                            <i class="bi bi-info-circle me-2 mt-1"></i>

                            <div>
                                Tenant portal accounts should be created
                                from the corresponding tenant record so
                                the account remains securely linked to
                                that tenant.
                            </div>

                        </div>

                    </div>

                    <div class="d-flex align-items-center gap-3 py-3 border-top">

                        <div class="rounded-3 bg-success-subtle text-success p-2">
                            <i class="bi bi-check-circle"></i>
                        </div>

                        <div>
                            <div class="fw-semibold small">
                                Secure account
                            </div>

                            <div class="text-muted small">
                                Password protected
                            </div>
                        </div>

                    </div>

                    <div class="d-flex align-items-center gap-3 py-3 border-top">

                        <div class="rounded-3 bg-primary-subtle text-primary p-2">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <div>
                            <div class="fw-semibold small">
                                Role-based access
                            </div>

                            <div class="text-muted small">
                                Permissions follow the assigned role
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Password --}}
        <div class="col-12">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom p-4">

                    <div class="d-flex align-items-center">

                        <div class="rounded-3 bg-warning-subtle text-warning-emphasis p-2 me-3">
                            <i class="bi bi-key fs-5"></i>
                        </div>

                        <div>
                            <h5 class="fw-bold mb-1">
                                Password
                            </h5>

                            <p class="text-muted small mb-0">
                                Set a secure password for the new account.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- Password --}}
                        <div class="col-md-6">

                            <label for="password" class="form-label fw-semibold">
                                Password
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Create a password"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="togglePassword"
                                    tabindex="-1"
                                    title="Show password"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            @error('password')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Use at least 8 characters.
                            </div>

                        </div>


                        {{-- Confirm Password --}}
                        <div class="col-md-6">

                            <label for="password_confirmation" class="form-label fw-semibold">
                                Confirm Password
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control"
                                    placeholder="Repeat the password"
                                    autocomplete="new-password"
                                    required
                                >

                            </div>

                            <div
                                id="passwordMatchMessage"
                                class="form-text"
                            >
                                Enter the same password again.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Actions --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4">

        <div class="text-muted small">
            <i class="bi bi-shield-lock me-1"></i>
            Account credentials are securely submitted.
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('users.index') }}"
                class="btn btn-outline-secondary px-4"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary px-4"
                id="createUserButton"
            >
                <i class="bi bi-person-plus me-2"></i>
                Create User
            </button>

        </div>

    </div>

</form>


@push('styles')
<style>
    .card {
        border-radius: 0.85rem;
    }

    .btn {
        border-radius: 0.55rem;
    }

    .form-control,
    .form-select,
    .input-group-text {
        border-radius: 0.55rem;
    }

    .input-group > .form-control {
        border-radius: 0 0.55rem 0.55rem 0;
    }

    .input-group > .input-group-text {
        border-radius: 0.55rem 0 0 0.55rem;
    }

    .input-group > .btn {
        border-radius: 0 0.55rem 0.55rem 0;
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.10) !important;
    }

    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.12) !important;
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.10) !important;
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.14) !important;
    }

    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.10) !important;
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const togglePassword = document.getElementById('togglePassword');
        const matchMessage = document.getElementById('passwordMatchMessage');
        const form = document.getElementById('createUserForm');
        const submitButton = document.getElementById('createUserButton');

        if (togglePassword && password) {

            togglePassword.addEventListener('click', function () {

                const isPassword = password.type === 'password';

                password.type = isPassword ? 'text' : 'password';

                this.innerHTML = isPassword
                    ? '<i class="bi bi-eye-slash"></i>'
                    : '<i class="bi bi-eye"></i>';

                this.title = isPassword
                    ? 'Hide password'
                    : 'Show password';
            });

        }

        function checkPasswordMatch() {

            if (!confirmation.value) {
                matchMessage.textContent = 'Enter the same password again.';
                matchMessage.className = 'form-text';
                return;
            }

            if (password.value === confirmation.value) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.className = 'form-text text-success';
            } else {
                matchMessage.textContent = 'Passwords do not match.';
                matchMessage.className = 'form-text text-danger';
            }
        }

        if (password && confirmation) {
            password.addEventListener('input', checkPasswordMatch);
            confirmation.addEventListener('input', checkPasswordMatch);
        }

        if (form && submitButton) {

            form.addEventListener('submit', function () {

                submitButton.disabled = true;

                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>' +
                    'Creating...';
            });

        }

    });
</script>
@endpush

@endsection