@extends('layouts.app')

@section('title', 'Edit User')
@section('page-heading', 'Edit User')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success-subtle text-success p-2 me-3">
                <i class="bi bi-check-lg"></i>
            </div>

            <div class="flex-grow-1">
                <div class="fw-semibold">Success</div>
                <div class="small">{{ session('success') }}</div>
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger-subtle text-danger p-2 me-3">
                <i class="bi bi-exclamation-lg"></i>
            </div>

            <div class="flex-grow-1">
                <div class="fw-semibold">Unable to update user</div>
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
                <i class="bi bi-person-gear fs-5"></i>
            </span>

            <h2 class="mb-0 fw-bold">
                Edit User
            </h2>
        </div>

        <p class="text-muted mb-0">
            Update this user's account details, role, or password.
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


@php
    $initials = collect(
        preg_split('/\s+/', trim($user->name))
    )
    ->filter()
    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
    ->take(2)
    ->implode('');

    $roleLabel = match ($user->role) {
        'administrator' => 'Administrator',
        'property_manager' => 'Property Manager',
        'accountant' => 'Accountant',
        'maintenance_staff' => 'Maintenance Staff',
        'reception_staff' => 'Reception Staff',
        default => ucwords(str_replace('_', ' ', $user->role)),
    };

    $roleIcon = match ($user->role) {
        'administrator' => 'bi-shield-lock',
        'property_manager' => 'bi-building',
        'accountant' => 'bi-calculator',
        'maintenance_staff' => 'bi-tools',
        'reception_staff' => 'bi-headset',
        default => 'bi-person',
    };
@endphp


{{-- User Summary --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">

            <div
                class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                style="width:64px;height:64px;font-size:1.25rem;"
            >
                {{ $initials ?: 'U' }}
            </div>

            <div class="flex-grow-1">

                <h5 class="fw-bold mb-1">
                    {{ $user->name }}
                </h5>

                <div class="text-muted small mb-2">
                    <i class="bi bi-envelope me-1"></i>
                    {{ $user->email }}
                </div>

                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                    <i class="bi {{ $roleIcon }} me-1"></i>
                    {{ $roleLabel }}
                </span>

            </div>

            <div class="text-md-end">
                <div class="text-muted small">
                    Account created
                </div>

                <div class="fw-semibold">
                    {{ $user->created_at->format('d M Y') }}
                </div>
            </div>

        </div>

    </div>
</div>


<form
    action="{{ route('users.update', $user) }}"
    method="POST"
    id="editUserForm"
>
    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- Account Details --}}
        <div class="col-12 col-xl-8">

            <div class="card border-0 shadow-sm h-100">

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
                                Update the user's basic information and access level.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- Name --}}
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
                                    value="{{ old('name', $user->name) }}"
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
                                    value="{{ old('email', $user->email) }}"
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
                                        @selected(old('role', $user->role) === $role)
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
                                <i class="bi bi-info-circle me-1"></i>
                                The selected role determines the user's system permissions.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Account Information --}}
        <div class="col-12 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-bottom p-4">

                    <h5 class="fw-bold mb-1">
                        Account Information
                    </h5>

                    <p class="text-muted small mb-0">
                        Current account details.
                    </p>

                </div>

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">User ID</span>
                        <span class="fw-semibold">#{{ $user->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Current role</span>
                        <span class="fw-semibold text-end">
                            {{ $roleLabel }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Created</span>
                        <span class="fw-semibold">
                            {{ $user->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Last updated</span>
                        <span class="fw-semibold">
                            {{ $user->updated_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="alert alert-info border-0 mt-4 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Changing the user's role may change which areas of the system they can access.
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
                                Change the password only if necessary.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- New Password --}}
                        <div class="col-md-6">

                            <label for="password" class="form-label fw-semibold">
                                New Password
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
                                    autocomplete="new-password"
                                    placeholder="Enter new password"
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
                                Leave blank to keep the current password.
                            </div>

                        </div>


                        {{-- Confirm Password --}}
                        <div class="col-md-6">

                            <label for="password_confirmation" class="form-label fw-semibold">
                                Confirm New Password
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
                                    autocomplete="new-password"
                                    placeholder="Repeat new password"
                                >

                            </div>

                            <div class="form-text">
                                Only required when changing the password.
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
            <i class="bi bi-shield-check me-1"></i>
            Changes are saved securely.
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
                id="updateUserButton"
            >
                <i class="bi bi-check-lg me-2"></i>
                Update User
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

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.10) !important;
    }

    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.10) !important;
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.14) !important;
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const form = document.getElementById('editUserForm');
        const submitButton = document.getElementById('updateUserButton');

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

        if (form && submitButton) {

            form.addEventListener('submit', function () {

                submitButton.disabled = true;

                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>' +
                    'Saving...';

            });

        }

    });
</script>
@endpush

@endsection