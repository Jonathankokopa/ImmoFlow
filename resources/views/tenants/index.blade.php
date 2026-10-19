@extends('layouts.app')

@section('title', 'Tenants Directory')
@section('page-heading', 'Tenants')

@push('styles')

<style>
    .tenant-page {
        max-width: 1500px;
    }

    .tenant-header {
        padding: 0.25rem 0;
    }

    .tenant-eyebrow {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
    }

    .tenant-header-icon {
        width: 54px;
        height: 54px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 1rem;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: #fff;
        box-shadow: 0 8px 18px rgba(13, 148, 136, 0.2);
    }

    .tenant-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.045);
        overflow: hidden;
    }

    .tenant-card:hover {
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
    }

    .tenant-summary-card {
        padding: 1.15rem;
        height: 100%;
    }

    .tenant-summary-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.8rem;
        font-size: 1.15rem;
    }

    .tenant-summary-value {
        font-size: 1.8rem;
        font-weight: 750;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    .tenant-summary-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
    }

    .tenant-toolbar {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .tenant-filter-tabs {
        flex-wrap: wrap;
    }

    .tenant-filter-tabs .nav-link {
        color: #64748b;
        border: 1px solid transparent;
        background: transparent;
    }

    .tenant-filter-tabs .nav-link:hover {
        color: #0f172a;
        background: #f8fafc;
    }

    .tenant-filter-tabs .nav-link.active {
        color: #0f766e;
        background: #e6fffb;
        border-color: #ccfbf1;
    }

    .tenant-search .input-group-text,
    .tenant-search .form-control {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .tenant-search .input-group-text {
        border-right: 0;
        color: #64748b;
    }

    .tenant-search .form-control {
        border-left: 0;
        box-shadow: none;
    }

    .tenant-search .form-control:focus {
        border-color: #86b7fe;
        box-shadow: none;
    }

    .tenant-avatar {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #0f172a, #334155);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 750;
    }

    .tenant-name {
        color: #0f172a;
        font-weight: 700;
    }

    .tenant-name:hover {
        color: #0d9488;
    }

    .tenant-table {
        min-width: 1050px;
    }

    .tenant-table thead th {
        padding: 0.85rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 750;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tenant-table tbody td {
        padding: 1rem;
        border-color: #eef2f7;
        vertical-align: middle;
        font-size: 0.86rem;
    }

    .tenant-table tbody tr:hover {
        background: #f8fafc;
    }

    .tenant-id {
        font-size: 0.76rem;
        font-weight: 650;
        color: #64748b;
    }

    .tenant-id-reference {
        display: block;
        margin-top: 0.15rem;
        font-size: 0.68rem;
        color: #94a3b8;
    }

    .tenant-contact {
        color: #334155;
        text-decoration: none;
    }

    .tenant-contact:hover {
        color: #0d9488;
    }

    .tenant-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.38rem 0.65rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .tenant-status-active {
        background: #e9f8ef;
        color: #198754;
    }

    .tenant-status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .tenant-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 0.6rem;
    }

    .tenant-footer {
        padding: 0.9rem 1rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.76rem;
    }

    .tenant-empty {
        padding: 4.5rem 1.5rem;
        text-align: center;
    }

    .tenant-empty-icon {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border-radius: 1.1rem;
        background: #e6fffb;
        color: #0f766e;
        font-size: 1.7rem;
    }

    .tenant-print-button,
    .tenant-primary-button {
        min-height: 42px;
        border-radius: 0.7rem;
        font-weight: 650;
    }

    .tenant-primary-button {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border: none;
    }

    .tenant-primary-button:hover {
        background: linear-gradient(135deg, #0f766e, #115e59);
    }

    .tenant-no-results {
        display: none;
        padding: 3rem 1rem;
        text-align: center;
        color: #64748b;
    }

    @media (max-width: 767.98px) {
        .tenant-page {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .tenant-header-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .tenant-header-actions .btn {
            flex: 1 1 auto;
        }

        .tenant-toolbar {
            padding: 0.9rem;
        }

        .tenant-filter-tabs {
            width: 100%;
        }

        .tenant-filter-tabs .nav-item {
            flex: 1 1 auto;
        }

        .tenant-filter-tabs .nav-link {
            width: 100%;
        }
    }

    @media print {
        .tenant-header-actions,
        .tenant-toolbar,
        .tenant-actions-column,
        .tenant-footer {
            display: none !important;
        }

        .tenant-card {
            box-shadow: none;
            border-color: #ddd;
        }

        .tenant-page {
            max-width: none;
        }
    }

    html[data-theme="dark"] .tenant-eyebrow {
        color: #94a3b8;
    }

    html[data-theme="dark"] .tenant-card {
        background: #111827;
        border-color: #273449;
        box-shadow: none;
    }

    html[data-theme="dark"] .tenant-summary-value,
    html[data-theme="dark"] .tenant-name {
        color: #f8fafc;
    }

    html[data-theme="dark"] .tenant-toolbar {
        background: #111827;
        border-color: #273449;
    }

    html[data-theme="dark"] .tenant-filter-tabs .nav-link {
        color: #94a3b8;
    }

    html[data-theme="dark"] .tenant-filter-tabs .nav-link:hover {
        color: #f8fafc;
        background: #172033;
    }

    html[data-theme="dark"] .tenant-filter-tabs .nav-link.active {
        color: #5eead4;
        background: rgba(13, 148, 136, 0.15);
        border-color: rgba(45, 212, 191, 0.2);
    }

    html[data-theme="dark"] .tenant-search .input-group-text,
    html[data-theme="dark"] .tenant-search .form-control {
        background: #172033;
        border-color: #334155;
        color: #f8fafc;
    }

    html[data-theme="dark"] .tenant-table thead th,
    html[data-theme="dark"] .tenant-footer {
        background: #172033;
        border-color: #273449;
    }

    html[data-theme="dark"] .tenant-table tbody td {
        border-color: #273449;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .tenant-table tbody tr:hover {
        background: #172033;
    }

    html[data-theme="dark"] .tenant-contact {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .tenant-status-active {
        background: rgba(25, 135, 84, 0.16);
        color: #75d9a1;
    }

    html[data-theme="dark"] .tenant-status-inactive {
        background: #1e293b;
        color: #94a3b8;
    }

    html[data-theme="dark"] .tenant-empty-icon {
        background: rgba(13, 148, 136, 0.14);
        color: #5eead4;
    }
</style>

@endpush

@section('content')

<div class="container-fluid tenant-page px-0">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 tenant-header">
    <div class="d-flex align-items-center gap-3">
        <div class="tenant-header-icon">
            <i class="bi bi-people-fill fs-4"></i>
        </div>

        <div>
            <div class="tenant-eyebrow">
                Property Management / Tenants
            </div>

            <h1 class="h3 fw-bold mb-1">
                Tenants Directory
            </h1>

            <p class="text-muted small mb-0">
                Manage tenant contacts, identification profiles, and tenancy statuses.
            </p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 tenant-header-actions">
        <button
            type="button"
            onclick="window.print()"
            class="btn btn-outline-secondary tenant-print-button"
        >
            <i class="bi bi-printer me-1"></i>
            Print
        </button>

        <a
            href="{{ route('tenants.create') }}"
            class="btn btn-primary tenant-primary-button px-3"
        >
            <i class="bi bi-person-plus-fill me-1"></i>
            Register Tenant
        </a>
    </div>
</div>

{{-- Summary --}}
@php
    $totalTenants = $tenants->count();
    $activeTenants = $tenants->where('status', 'active')->count();
    $inactiveTenants = $tenants->where('status', 'inactive')->count();
@endphp

<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-4">
        <div class="tenant-card tenant-summary-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="tenant-summary-label">Registered Tenants</span>

                <span class="tenant-summary-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people"></i>
                </span>
            </div>

            <div class="tenant-summary-value mb-1">
                {{ $totalTenants }}
            </div>

            <div class="text-muted small">
                Total tenant profiles
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4">
        <div class="tenant-card tenant-summary-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="tenant-summary-label">Active Tenancy</span>

                <span class="tenant-summary-icon bg-success-subtle text-success">
                    <i class="bi bi-person-check"></i>
                </span>
            </div>

            <div class="tenant-summary-value text-success mb-1">
                {{ $activeTenants }}
            </div>

            <div class="text-muted small">
                Current active leaseholders
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-xl-4">
        <div class="tenant-card tenant-summary-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="tenant-summary-label">Inactive / Past Tenants</span>

                <span class="tenant-summary-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-person-dash"></i>
                </span>
            </div>

            <div class="tenant-summary-value text-secondary mb-1">
                {{ $inactiveTenants }}
            </div>

            <div class="text-muted small">
                Vacated or inactive records
            </div>
        </div>
    </div>

</div>

{{-- Directory --}}
<div class="tenant-card">

    {{-- Toolbar --}}
    <div class="tenant-toolbar">
        <div class="row align-items-center g-3">

            <div class="col-lg-7">
                <ul class="nav nav-pills gap-1 tenant-filter-tabs mb-0" id="tenantFilterTabs">

                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link active rounded-3 px-3 py-2 btn-sm fw-semibold"
                            data-filter="all"
                        >
                            All
                            <span class="badge bg-secondary-subtle text-dark rounded-pill ms-1">
                                {{ $totalTenants }}
                            </span>
                        </button>
                    </li>

                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link rounded-3 px-3 py-2 btn-sm fw-semibold"
                            data-filter="active"
                        >
                            Active
                            <span class="badge bg-success-subtle text-success rounded-pill ms-1">
                                {{ $activeTenants }}
                            </span>
                        </button>
                    </li>

                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link rounded-3 px-3 py-2 btn-sm fw-semibold"
                            data-filter="inactive"
                        >
                            Inactive
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1">
                                {{ $inactiveTenants }}
                            </span>
                        </button>
                    </li>

                </ul>
            </div>

            <div class="col-lg-5">
                <div class="input-group tenant-search">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="search"
                        id="tenantSearchInput"
                        class="form-control ps-0"
                        placeholder="Search name, ID, phone, email..."
                        autocomplete="off"
                    >
                </div>
            </div>

        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">

        @if($totalTenants)

            <table class="table tenant-table align-middle mb-0" id="tenantsTable">

                <thead>
                    <tr>
                        <th class="ps-4" style="width: 70px;">#</th>
                        <th>Tenant</th>
                        <th>National ID / Passport</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-end pe-4 tenant-actions-column">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($tenants as $tenant)

                        @php
                            $tenantName = $tenant->full_name ?? $tenant->name ?? 'Tenant';

                            $initials = collect(
                                preg_split('/\s+/', trim($tenantName))
                            )
                            ->filter()
                            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                            ->take(2)
                            ->implode('');

                            $searchText = strtolower(
                                $tenantName . ' ' .
                                ($tenant->national_id ?? '') . ' ' .
                                ($tenant->phone ?? '') . ' ' .
                                ($tenant->email ?? '') . ' ' .
                                $tenant->id
                            );
                        @endphp

                        <tr
                            data-status="{{ strtolower($tenant->status ?? 'inactive') }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- Row Number --}}
                            <td class="ps-4">
                                <span class="tenant-id">
                                    {{ $loop->iteration }}
                                </span>

                                <span class="tenant-id-reference">
                                    Tenant #{{ $tenant->id }}
                                </span>
                            </td>

                            {{-- Tenant --}}
                            <td>
                                <div class="d-flex align-items-center gap-3">

                                    <div class="tenant-avatar">
                                        {{ $initials ?: 'T' }}
                                    </div>

                                    <div class="min-w-0">
                                        <a
                                            href="{{ route('tenants.show', $tenant) }}"
                                            class="tenant-name text-decoration-none d-block text-truncate"
                                            style="max-width: 250px;"
                                        >
                                            {{ $tenantName }}
                                        </a>

                                        <div class="text-muted small">
                                            Registered profile
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- National ID --}}
                            <td>
                                @if($tenant->national_id)
                                    <code class="text-dark bg-light px-2 py-1 rounded border">
                                        {{ $tenant->national_id }}
                                    </code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Phone --}}
                            <td>
                                @if($tenant->phone)
                                    <a
                                        href="tel:{{ $tenant->phone }}"
                                        class="tenant-contact"
                                    >
                                        <i class="bi bi-telephone text-primary me-1"></i>
                                        {{ $tenant->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Email --}}
                            <td>
                                @if($tenant->email)
                                    <a
                                        href="mailto:{{ $tenant->email }}"
                                        class="tenant-contact"
                                    >
                                        <i class="bi bi-envelope text-teal me-1"></i>
                                        {{ $tenant->email }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="tenant-status tenant-status-active">
                                        <i class="bi bi-check-circle"></i>
                                        Active Lease
                                    </span>
                                @else
                                    <span class="tenant-status tenant-status-inactive">
                                        <i class="bi bi-pause-circle"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-4 tenant-actions-column">
                                <div class="d-flex align-items-center justify-content-end gap-1">

                                    <a
                                        href="{{ route('tenants.show', $tenant) }}"
                                        class="btn btn-light border tenant-action text-primary"
                                        title="View Profile"
                                        aria-label="View {{ $tenantName }}"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a
                                        href="{{ route('tenants.edit', $tenant) }}"
                                        class="btn btn-light border tenant-action text-warning"
                                        title="Edit Profile"
                                        aria-label="Edit {{ $tenantName }}"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        action="{{ route('tenants.destroy', $tenant) }}"
                                        method="POST"
                                        class="tenant-delete-form d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-light border tenant-action text-danger"
                                            title="Delete Tenant"
                                            aria-label="Delete {{ $tenantName }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div id="tenantNoResults" class="tenant-no-results">
                <i class="bi bi-search fs-3 d-block mb-2"></i>
                <strong>No matching tenants</strong>
                <div class="small mt-1">
                    Try a different search term or status filter.
                </div>
            </div>

        @else

            <div class="tenant-empty">

                <div class="tenant-empty-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h5 class="fw-bold mb-1">
                    No Tenants Registered
                </h5>

                <p class="text-muted small mb-4 mx-auto" style="max-width: 420px;">
                    There are currently no tenant profiles registered in the system.
                    Register your first tenant to begin managing tenancy records.
                </p>

                <a
                    href="{{ route('tenants.create') }}"
                    class="btn btn-primary tenant-primary-button px-4"
                >
                    <i class="bi bi-person-plus me-1"></i>
                    Register First Tenant
                </a>

            </div>

        @endif

    </div>

    @if($totalTenants)
        <div class="tenant-footer d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                Showing
                <strong class="text-body">{{ $totalTenants }}</strong>
                tenant profiles
            </div>

            <div>
                <i class="bi bi-shield-check text-success me-1"></i>
                Occupant records
            </div>
        </div>
    @endif

</div>

</div>
@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('tenantSearchInput');
        const filterButtons = document.querySelectorAll('#tenantFilterTabs [data-filter]');
        const rows = document.querySelectorAll('#tenantsTable tbody tr');
        const noResults = document.getElementById('tenantNoResults');

        let activeFilter = 'all';

        function applyTenantFilters() {
            const search = (searchInput?.value || '').toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const status = row.dataset.status || '';
                const searchable = row.dataset.search || '';

                const matchesStatus =
                    activeFilter === 'all' || status === activeFilter;

                const matchesSearch =
                    !search || searchable.includes(search);

                const visible = matchesStatus && matchesSearch;

                row.style.display = visible ? '' : 'none';

                if (visible) {
                    visibleCount++;
                }
            });

            if (noResults) {
                noResults.style.display =
                    rows.length > 0 && visibleCount === 0
                        ? 'block'
                        : 'none';
            }
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = this.dataset.filter || 'all';

                filterButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                this.classList.add('active');

                applyTenantFilters();
            });
        });

        searchInput?.addEventListener('input', applyTenantFilters);

        document.querySelectorAll('.tenant-delete-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const confirmed = window.confirm(
                    'Are you sure you want to delete this tenant? This action cannot be undone.'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            });
        });
    });
</script>

@endpush
