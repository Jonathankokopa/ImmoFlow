@extends('layouts.app')

@section('title', 'Property Owners')
@section('page-heading', 'Property Owners')

@push('styles')
<style>
    .owners-page {
        max-width: 1220px;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    /* =========================================================
       PAGE HEADER
       ========================================================= */

    .owners-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .owners-header-main {
        display: flex;
        align-items: center;
        gap: .9rem;
    }

    .owners-header-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
        font-size: 1.2rem;
    }

    .owners-eyebrow {
        margin-bottom: .18rem;
        color: var(--pm-teal);
        font-size: .65rem;
        font-weight: 750;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .owners-title {
        margin: 0;
        color: var(--pm-navy);
        font-size: 1.4rem;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .owners-subtitle {
        margin: .3rem 0 0;
        color: var(--pm-muted);
        font-size: .8rem;
    }

    .owners-add-btn {
        min-height: 40px;
        padding: .55rem .85rem;
        border-radius: 9px;
        font-size: .78rem;
        font-weight: 650;
        white-space: nowrap;
    }

    /* =========================================================
       ALERTS
       ========================================================= */

    .owners-alert {
        border-radius: 10px;
        font-size: .8rem;
    }

    /* =========================================================
       SUMMARY CARDS
       ========================================================= */

    .owner-stat-card {
        position: relative;
        height: 100%;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        box-shadow: var(--pm-shadow);
    }

    .owner-stat-card::after {
        content: "";
        position: absolute;
        right: -24px;
        bottom: -30px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(13, 148, 136, .035);
        pointer-events: none;
    }

    .owner-stat-card.teal::after {
        background: rgba(13, 148, 136, .06);
    }

    .owner-stat-card.info::after {
        background: rgba(13, 110, 253, .05);
    }

    .owner-stat-body {
        position: relative;
        z-index: 1;
        padding: 1rem;
    }

    .owner-stat-content {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .owner-stat-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        font-size: 1rem;
    }

    .owner-stat-icon.navy {
        background: rgba(31, 41, 55, .08);
        color: var(--pm-navy);
    }

    .owner-stat-icon.teal {
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
    }

    .owner-stat-icon.info {
        background: rgba(13, 110, 253, .09);
        color: #0d6efd;
    }

    .owner-stat-value {
        color: var(--pm-navy);
        font-size: 1.2rem;
        line-height: 1.1;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .owner-stat-label {
        margin-top: .22rem;
        color: var(--pm-muted);
        font-size: .69rem;
        font-weight: 550;
    }

    /* =========================================================
       DIRECTORY CARD
       ========================================================= */

    .owners-directory {
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        box-shadow: var(--pm-shadow);
    }

    .owners-directory-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.05rem 1.15rem;
        border-bottom: 1px solid var(--pm-border);
    }

    .owners-directory-title {
        margin: 0;
        color: var(--pm-navy);
        font-size: .92rem;
        font-weight: 700;
    }

    .owners-directory-subtitle {
        margin-top: .15rem;
        color: var(--pm-muted);
        font-size: .7rem;
    }

    .owners-count {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .35rem .6rem;
        border-radius: 999px;
        background: rgba(31, 41, 55, .07);
        color: var(--pm-navy);
        font-size: .67rem;
        font-weight: 650;
        white-space: nowrap;
    }

    /* =========================================================
       TABLE
       ========================================================= */

    .owners-table-wrap {
        overflow-x: auto;
    }

    .owners-table {
        min-width: 920px;
        margin: 0;
    }

    .owners-table thead th {
        padding: .78rem .7rem;
        background: #f8f9fb;
        border-bottom: 1px solid var(--pm-border);
        color: #8993a1;
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .045em;
        white-space: nowrap;
    }

    .owners-table tbody td {
        padding: .85rem .7rem;
        border-bottom: 1px solid #eef0f3;
        color: #4f5967;
        font-size: .76rem;
        vertical-align: middle;
    }

    .owners-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .owners-table tbody tr {
        transition: background .15s ease;
    }

    .owners-table tbody tr:hover {
        background: #fafcfc;
    }

    .owner-number {
        color: #98a1ad;
        font-size: .7rem;
        font-weight: 600;
    }

    /* =========================================================
       OWNER IDENTITY
       ========================================================= */

    .owner-identity {
        display: flex;
        align-items: center;
        gap: .7rem;
        min-width: 200px;
    }

    .owner-avatar {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: rgba(31, 41, 55, .08);
        color: var(--pm-navy);
        font-size: .88rem;
        font-weight: 700;
    }

    .owner-name {
        display: block;
        color: var(--pm-navy);
        font-size: .78rem;
        font-weight: 650;
        text-decoration: none;
        transition: color .15s ease;
    }

    .owner-name:hover {
        color: var(--pm-teal);
    }

    .owner-reference {
        margin-top: .16rem;
        color: #98a1ad;
        font-size: .64rem;
    }

    /* =========================================================
       CONTACTS
       ========================================================= */

    .owner-contact {
        color: #4f5967;
        text-decoration: none;
        font-size: .75rem;
    }

    .owner-contact:hover {
        color: var(--pm-teal);
    }

    .owner-empty {
        color: #a0a8b3;
        font-size: .75rem;
    }

    /* =========================================================
       PROPERTY BADGE
       ========================================================= */

    .property-count {
        display: inline-flex;
        align-items: center;
        gap: .32rem;
        padding: .35rem .55rem;
        border-radius: 7px;
        background: rgba(13, 148, 136, .08);
        color: var(--pm-teal);
        font-size: .68rem;
        font-weight: 650;
    }

    /* =========================================================
       ACTIONS
       ========================================================= */

    .owner-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .3rem;
    }

    .owner-action {
        min-width: 34px;
        min-height: 32px;
        padding: .35rem .5rem;
        border-radius: 7px;
        font-size: .7rem;
        font-weight: 600;
    }

    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .owners-empty {
        padding: 4rem 1.5rem;
        text-align: center;
    }

    .owners-empty-icon {
        width: 68px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        border-radius: 18px;
        background: rgba(31, 41, 55, .07);
        color: var(--pm-navy);
        font-size: 1.65rem;
    }

    .owners-empty-title {
        margin-bottom: .35rem;
        color: var(--pm-navy);
        font-size: .95rem;
        font-weight: 700;
    }

    .owners-empty-text {
        max-width: 380px;
        margin: 0 auto 1.2rem;
        color: var(--pm-muted);
        font-size: .75rem;
        line-height: 1.55;
    }

    /* =========================================================
       DARK MODE
       ========================================================= */

    html[data-theme="dark"] .owner-stat-card,
    html[data-theme="dark"] .owners-directory {
        background: #151b23;
        border-color: #29313d;
    }

    html[data-theme="dark"] .owners-title,
    html[data-theme="dark"] .owner-stat-value,
    html[data-theme="dark"] .owners-directory-title,
    html[data-theme="dark"] .owner-name,
    html[data-theme="dark"] .owners-empty-title {
        color: #e7ebf0;
    }

    html[data-theme="dark"] .owner-stat-icon.navy,
    html[data-theme="dark"] .owner-avatar,
    html[data-theme="dark"] .owners-empty-icon {
        background: #1d2530;
        color: #dce2e9;
    }

    html[data-theme="dark"] .owners-directory-header {
        border-color: #29313d;
    }

    html[data-theme="dark"] .owners-count {
        background: #1d2530;
        color: #cdd5df;
    }

    html[data-theme="dark"] .owners-table thead th {
        background: #11161d;
        border-color: #29313d;
        color: #8993a1;
    }

    html[data-theme="dark"] .owners-table tbody td {
        border-color: #29313d;
        color: #aeb7c4;
    }

    html[data-theme="dark"] .owners-table tbody tr:hover {
        background: #1a212b;
    }

    html[data-theme="dark"] .owner-contact {
        color: #aeb7c4;
    }

    html[data-theme="dark"] .owner-reference,
    html[data-theme="dark"] .owner-empty,
    html[data-theme="dark"] .owner-number {
        color: #7f8997;
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 767.98px) {
        .owners-header {
            align-items: flex-start;
        }

        .owners-header-main {
            align-items: flex-start;
        }

        .owners-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }

        .owners-title {
            font-size: 1.2rem;
        }

        .owners-subtitle {
            font-size: .75rem;
        }

        .owners-add-btn {
            width: 100%;
        }

        .owners-header > div:last-child {
            width: 100%;
        }

        .owners-directory-header {
            align-items: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .owners-page {
            padding-bottom: 1rem;
        }

        .owners-header {
            margin-bottom: 1.15rem;
        }

        .owner-stat-body {
            padding: .9rem;
        }

        .owner-stat-icon {
            width: 38px;
            height: 38px;
        }

        .owner-stat-value {
            font-size: 1.05rem;
        }

        .owners-directory-header {
            padding: .95rem;
        }
    }
</style>
@endpush


@section('content')

<div class="owners-page">

    {{-- =========================================================
         ALERTS
         ========================================================= --}}

    @if(session('success'))
        <div
            class="alert alert-success alert-dismissible fade show owners-alert mb-4"
            role="alert"
        >
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif


    @if(session('error'))
        <div
            class="alert alert-danger alert-dismissible fade show owners-alert mb-4"
            role="alert"
        >
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif


    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}

    <div class="owners-header">

        <div class="owners-header-main">

            <div class="owners-header-icon">
                <i class="bi bi-person-badge"></i>
            </div>

            <div>

                <div class="owners-eyebrow">
                    Property Management
                </div>

                <h1 class="owners-title">
                    Property Owners
                </h1>

                <p class="owners-subtitle">
                    Manage property owners, assignments, and contact information.
                </p>

            </div>

        </div>


        @if(!auth()->user()->isOwner())
            <div>
                <a
                    href="{{ route('owners.create') }}"
                    class="btn pm-btn-primary owners-add-btn"
                >
                    <i class="bi bi-person-plus me-1"></i>
                    Add Owner
                </a>
            </div>
        @endif

    </div>


    {{-- =========================================================
         SUMMARY CARDS
         ========================================================= --}}

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-4">

            <div class="owner-stat-card">

                <div class="owner-stat-body">

                    <div class="owner-stat-content">

                        <div class="owner-stat-icon navy">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <div>

                            <div class="owner-stat-value">
                                {{ $owners->count() }}
                            </div>

                            <div class="owner-stat-label">
                                Total Owners
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-4">

            <div class="owner-stat-card teal">

                <div class="owner-stat-body">

                    <div class="owner-stat-content">

                        <div class="owner-stat-icon teal">
                            <i class="bi bi-buildings"></i>
                        </div>

                        <div>

                            <div class="owner-stat-value">
                                {{ $owners->sum(fn ($owner) => $owner->properties->count()) }}
                            </div>

                            <div class="owner-stat-label">
                                Properties Owned
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-md-4">

            <div class="owner-stat-card info">

                <div class="owner-stat-body">

                    <div class="owner-stat-content">

                        <div class="owner-stat-icon info">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <div>

                            <div class="owner-stat-value">
                                {{ $owners->whereNotNull('phone')->count() }}
                            </div>

                            <div class="owner-stat-label">
                                With Phone Numbers
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         OWNERS DIRECTORY
         ========================================================= --}}

    <div class="owners-directory">

        <div class="owners-directory-header">

            <div>

                <h5 class="owners-directory-title">
                    Owners Directory
                </h5>

                <div class="owners-directory-subtitle">
                    Registered property owners in the system
                </div>

            </div>


            <div class="owners-count">
                <i class="bi bi-people"></i>

                {{ $owners->count() }}

                {{ $owners->count() === 1 ? 'Owner' : 'Owners' }}
            </div>

        </div>


        @if($owners->count())

            <div class="owners-table-wrap">

                <table class="table owners-table align-middle mb-0">

                    <thead>

                        <tr>

                            <th class="ps-4">
                                #
                            </th>

                            <th>
                                Owner
                            </th>

                            <th>
                                National ID
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Properties
                            </th>

                            <th>
                                Portal Status
                            </th>

                            <th class="text-end pe-4">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($owners as $owner)

                            @php
                                $initials = collect(
                                    preg_split('/\s+/', trim($owner->full_name))
                                )
                                ->filter()
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->take(2)
                                ->implode('');
                            @endphp

                            <tr>

                                {{-- Row Number --}}

                                <td class="ps-4">

                                    <span class="owner-number">
                                        {{ $loop->iteration }}
                                    </span>

                                </td>


                                {{-- Owner --}}

                                <td>

                                    <div class="owner-identity">

                                        <div class="owner-avatar">
                                            {{ $initials ?: 'O' }}
                                        </div>

                                        <div>

                                            <a
                                                href="{{ route('owners.show', $owner) }}"
                                                class="owner-name"
                                            >
                                                {{ $owner->full_name }}
                                            </a>

                                            <div class="owner-reference">
                                                Owner #{{ $owner->id }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- National ID --}}

                                <td>

                                    @if($owner->national_id)

                                        <span>
                                            {{ $owner->national_id }}
                                        </span>

                                    @else

                                        <span class="owner-empty">
                                            Not provided
                                        </span>

                                    @endif

                                </td>


                                {{-- Phone --}}

                                <td>

                                    @if($owner->phone)

                                        <a
                                            href="tel:{{ $owner->phone }}"
                                            class="owner-contact"
                                        >
                                            <i class="bi bi-telephone me-1"></i>
                                            {{ $owner->phone }}
                                        </a>

                                    @else

                                        <span class="owner-empty">
                                            Not provided
                                        </span>

                                    @endif

                                </td>


                                {{-- Email --}}

                                <td>

                                    @if($owner->email)

                                        <a
                                            href="mailto:{{ $owner->email }}"
                                            class="owner-contact"
                                        >
                                            {{ $owner->email }}
                                        </a>

                                    @else

                                        <span class="owner-empty">
                                            Not provided
                                        </span>

                                    @endif

                                </td>


                                {{-- Properties --}}

                                <td>

                                    <span class="property-count">

                                        <i class="bi bi-building"></i>

                                        {{ $owner->properties->count() }}

                                    </span>

                                </td>


                                {{-- Portal Status --}}

                                <td>

                                    @if($owner->user)
                                        @if($owner->user->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-shield-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                <i class="bi bi-shield-x me-1"></i>Deactivated
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                            <i class="bi bi-dash-circle me-1"></i>No Account
                                        </span>
                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td class="text-end pe-4">

                                    <div class="owner-actions">

                                        <a
                                            href="{{ route('owners.show', $owner) }}"
                                            class="btn btn-sm pm-btn-outline owner-action"
                                            title="View Owner"
                                        >
                                            <i class="bi bi-eye"></i>

                                            <span class="d-none d-lg-inline ms-1">
                                                View
                                            </span>
                                        </a>


                                        <a
                                            href="{{ route('owners.edit', $owner) }}"
                                            class="btn btn-sm pm-btn-outline-warning owner-action"
                                            title="Edit Owner"
                                        >
                                            <i class="bi bi-pencil"></i>

                                            <span class="d-none d-lg-inline ms-1">
                                                Edit
                                            </span>
                                        </a>


                                        <form
                                            action="{{ route('owners.destroy', $owner) }}"
                                            method="POST"
                                            class="d-inline owner-delete-form"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm pm-btn-outline-danger owner-action"
                                                title="Delete Owner"
                                            >
                                                <i class="bi bi-trash"></i>

                                                <span class="d-none d-lg-inline ms-1">
                                                    Delete
                                                </span>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            {{-- =================================================
                 EMPTY STATE
                 ================================================= --}}

            <div class="owners-empty">

                <div class="owners-empty-icon">
                    <i class="bi bi-person-badge"></i>
                </div>

                <h5 class="owners-empty-title">
                    No Owners Found
                </h5>

                <p class="owners-empty-text">
                    There are no property owners registered yet.
                    Add your first owner to begin assigning properties and
                    managing owner information.
                </p>

                @if(!auth()->user()->isOwner())
                    <a
                        href="{{ route('owners.create') }}"
                        class="btn pm-btn-primary"
                    >
                        <i class="bi bi-person-plus me-1"></i>
                        Add First Owner
                    </a>
                @endif

            </div>

        @endif

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * ---------------------------------------------------------
         * DELETE CONFIRMATION
         * ---------------------------------------------------------
         */

        document.querySelectorAll('.owner-delete-form').forEach(function (form) {

            form.addEventListener('submit', function (event) {

                const confirmed = window.confirm(
                    'Are you sure you want to delete this owner? This action cannot be undone.'
                );

                if (!confirmed) {
                    event.preventDefault();
                }

            });

        });

    });
</script>
@endpush