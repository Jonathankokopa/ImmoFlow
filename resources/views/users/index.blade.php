@extends('layouts.app')

@section('title', 'Users')
@section('page-heading', 'Users')

@section('content')

{{-- =========================================================
     Alerts
========================================================= --}}

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
                <div class="fw-semibold">Action failed</div>
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


{{-- =========================================================
     Page Header
========================================================= --}}

<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                  style="width: 42px; height: 42px;">
                <i class="bi bi-people fs-5"></i>
            </span>

            <div>
                <h2 class="mb-0 fw-bold">Users</h2>
            </div>
        </div>

        <p class="text-muted mb-0">
            Manage system users, access roles, and account permissions.
        </p>
    </div>

    <a
        href="{{ route('users.create') }}"
        class="btn btn-primary px-3 shadow-sm"
    >
        <i class="bi bi-person-plus me-2"></i>
        Add User
    </a>

</div>


{{-- =========================================================
     Summary Cards
========================================================= --}}

@php
    $totalUsers = $users->count();

    $administratorCount = $users->where('role', 'administrator')->count();
    $managerCount = $users->where('role', 'property_manager')->count();
    $accountantCount = $users->where('role', 'accountant')->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Total Users --}}
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-2">
                            Total Users
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $totalUsers }}
                        </div>

                        <div class="text-muted small mt-1">
                            System accounts
                        </div>
                    </div>

                    <div class="rounded-3 bg-primary-subtle text-primary p-3">
                        <i class="bi bi-people fs-5"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- Administrators --}}
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-2">
                            Administrators
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $administratorCount }}
                        </div>

                        <div class="text-muted small mt-1">
                            Full system access
                        </div>
                    </div>

                    <div class="rounded-3 bg-danger-subtle text-danger p-3">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- Property Managers --}}
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-2">
                            Property Managers
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $managerCount }}
                        </div>

                        <div class="text-muted small mt-1">
                            Property operations
                        </div>
                    </div>

                    <div class="rounded-3 bg-info-subtle text-info p-3">
                        <i class="bi bi-building fs-5"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- Accountants --}}
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-2">
                            Accountants
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $accountantCount }}
                        </div>

                        <div class="text-muted small mt-1">
                            Financial operations
                        </div>
                    </div>

                    <div class="rounded-3 bg-success-subtle text-success p-3">
                        <i class="bi bi-calculator fs-5"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>


{{-- =========================================================
     Users Table
========================================================= --}}

<div class="card border-0 shadow-sm overflow-hidden">

    {{-- Card Header --}}
    <div class="card-header bg-white border-bottom py-3 px-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

            <div>
                <h5 class="mb-1 fw-bold">
                    System Users
                </h5>

                <p class="text-muted small mb-0">
                    Accounts with access to this property management system.
                </p>
            </div>

            @if($totalUsers)
                <span class="badge bg-light text-dark border px-3 py-2">
                    {{ $totalUsers }}
                    {{ $totalUsers === 1 ? 'user' : 'users' }}
                </span>
            @endif

        </div>

    </div>


    @if($users->count())

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="px-4 py-3">
                            User
                        </th>

                        <th class="py-3">
                            Email
                        </th>

                        <th class="py-3">
                            Role
                        </th>

                        <th class="py-3">
                            Joined
                        </th>

                        <th class="text-end px-4 py-3">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($users as $user)

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

                            $roleClass = match ($user->role) {
                                'administrator' => 'bg-danger-subtle text-danger',
                                'property_manager' => 'bg-primary-subtle text-primary',
                                'accountant' => 'bg-success-subtle text-success',
                                'maintenance_staff' => 'bg-warning-subtle text-warning-emphasis',
                                'reception_staff' => 'bg-info-subtle text-info-emphasis',
                                default => 'bg-secondary-subtle text-secondary',
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


                        <tr>

                            {{-- User --}}
                            <td class="px-4 py-3">

                                <div class="d-flex align-items-center">

                                    <div
                                        class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 44px; height: 44px;"
                                    >
                                        {{ $initials ?: 'U' }}
                                    </div>

                                    <div class="ms-3">

                                        <div class="fw-semibold">
                                            {{ $user->name }}
                                        </div>

                                        <div class="text-muted small">
                                            User #{{ $loop->iteration }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Email --}}
                            <td>

                                <a
                                    href="mailto:{{ $user->email }}"
                                    class="text-decoration-none text-body"
                                >
                                    <i class="bi bi-envelope text-muted me-1"></i>
                                    {{ $user->email }}
                                </a>

                            </td>


                            {{-- Role --}}
                            <td>

                                <span class="badge {{ $roleClass }} rounded-pill px-3 py-2">

                                    <i class="bi {{ $roleIcon }} me-1"></i>

                                    {{ $roleLabel }}

                                </span>

                            </td>


                            {{-- Created --}}
                            <td>

                                <div class="fw-medium">
                                    {{ $user->created_at->format('d M Y') }}
                                </div>

                                <div class="text-muted small">
                                    {{ $user->created_at->format('H:i') }}
                                </div>

                            </td>


                            {{-- Actions --}}
                            <td class="text-end px-4">

                                <div class="d-inline-flex align-items-center gap-2">

                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit user"
                                    >
                                        <i class="bi bi-pencil me-1"></i>
                                        Edit
                                    </a>


                                    @if(auth()->id() !== $user->id)

                                        <form
                                            action="{{ route('users.destroy', $user) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}? This action cannot be undone.');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete user"
                                            >
                                                <i class="bi bi-trash me-1"></i>
                                                Delete
                                            </button>

                                        </form>

                                    @else

                                        <span
                                            class="badge bg-light text-muted border px-3 py-2"
                                            title="You cannot delete your own account"
                                        >
                                            <i class="bi bi-person-check me-1"></i>
                                            Current account
                                        </span>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- Footer --}}
        <div class="card-footer bg-white border-top px-4 py-3">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                <div class="text-muted small">
                    Showing
                    <strong class="text-body">{{ $users->count() }}</strong>
                    {{ $users->count() === 1 ? 'user' : 'users' }}
                </div>

                <a
                    href="{{ route('users.create') }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-person-plus me-1"></i>
                    Add another user
                </a>

            </div>

        </div>


    @else

        {{-- =====================================================
             Empty State
        ====================================================== --}}

        <div class="card-body py-5">

            <div class="text-center mx-auto" style="max-width: 520px;">

                <div
                    class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-4"
                    style="width: 76px; height: 76px;"
                >
                    <i class="bi bi-people fs-2"></i>
                </div>

                <h4 class="fw-bold mb-2">
                    No users yet
                </h4>

                <p class="text-muted mb-4">
                    There are currently no system users. Create your first
                    user to start managing access to the property management
                    system.
                </p>

                <a
                    href="{{ route('users.create') }}"
                    class="btn btn-primary px-4"
                >
                    <i class="bi bi-person-plus me-2"></i>
                    Add First User
                </a>

            </div>

        </div>

    @endif

</div>


{{-- =========================================================
     Small UI Enhancements
========================================================= --}}

@push('styles')
<style>
    .table-hover tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.025);
    }

    .card {
        border-radius: 0.85rem;
    }

    .badge {
        font-weight: 600;
    }

    .btn {
        border-radius: 0.55rem;
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.10) !important;
    }

    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.10) !important;
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.10) !important;
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.14) !important;
    }

    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.12) !important;
    }
</style>
@endpush

@endsection