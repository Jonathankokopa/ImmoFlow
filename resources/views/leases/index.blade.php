@extends('layouts.app')

@section('title', 'Leases')
@section('page-heading', 'Leases')

@push('styles')

<style>
    .lease-page {
        --lease-border: #e5e7eb;
        --lease-surface: #ffffff;
        --lease-soft: #f8fafc;
        --lease-primary: #2563eb;
        --lease-muted: #64748b;
    }

    .lease-header {
        padding: 1.5rem;
        border: 1px solid var(--lease-border);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
    }

    .lease-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .45rem;
        color: var(--lease-primary);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .lease-eyebrow::before {
        content: "";
        width: 22px;
        height: 2px;
        border-radius: 999px;
        background: currentColor;
    }

    .lease-summary-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--lease-border);
        border-radius: 1rem;
        background: var(--lease-surface);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .lease-summary-card::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 70px;
        height: 70px;
        border-radius: 0 0 0 100%;
        background: rgba(37, 99, 235, .04);
        pointer-events: none;
    }

    .lease-summary-body {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: 1.15rem;
    }

    .lease-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        font-size: 1.1rem;
    }

    .lease-icon.navy {
        color: #1e3a8a;
        background: #dbeafe;
    }

    .lease-icon.green {
        color: #166534;
        background: #dcfce7;
    }

    .lease-icon.warning {
        color: #92400e;
        background: #fef3c7;
    }

    .lease-icon.info {
        color: #155e75;
        background: #cffafe;
    }

    .lease-metric {
        font-size: 1.5rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .lease-label {
        margin-top: .25rem;
        color: var(--lease-muted);
        font-size: .76rem;
        font-weight: 600;
    }

    .lease-directory {
        overflow: hidden;
        border: 1px solid var(--lease-border);
        border-radius: 1rem;
        background: var(--lease-surface);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .lease-directory-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--lease-border);
        background: var(--lease-soft);
    }

    .lease-count {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .7rem;
        border-radius: 999px;
        background: #e0e7ff;
        color: #3730a3;
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .lease-table {
        min-width: 1050px;
    }

    .lease-table thead th {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--lease-border);
        background: #f8fafc;
        color: #64748b;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .lease-table tbody td {
        padding: 1rem;
        border-color: #eef2f7;
        vertical-align: middle;
    }

    .lease-table tbody tr {
        transition: background-color .15s ease;
    }

    .lease-table tbody tr:hover {
        background: #f8fafc;
    }

    .lease-number {
        color: #94a3b8;
        font-size: .8rem;
        font-weight: 700;
    }

    .lease-person,
    .lease-property {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        text-decoration: none;
        color: #0f172a;
        font-weight: 700;
    }

    .lease-person:hover,
    .lease-property:hover {
        color: var(--lease-primary);
    }

    .lease-avatar {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .65rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: .72rem;
        font-weight: 800;
    }

    .lease-unit {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .55rem;
        border-radius: .5rem;
        background: #f1f5f9;
        color: #334155;
        font-size: .78rem;
        font-weight: 700;
    }

    .lease-date {
        white-space: nowrap;
        color: #475569;
        font-size: .82rem;
    }

    .lease-money {
        white-space: nowrap;
        color: #0f172a;
        font-weight: 800;
    }

    .lease-money-sub {
        display: block;
        margin-top: .2rem;
        color: #94a3b8;
        font-size: .7rem;
    }

    .lease-status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .lease-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .lease-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .lease-status.expired {
        background: #e0e7ff;
        color: #3730a3;
    }

    .lease-status.terminated {
        background: #fee2e2;
        color: #991b1b;
    }

    .lease-status.unknown {
        background: #f1f5f9;
        color: #475569;
    }

    .lease-action {
        border-radius: .55rem;
        font-weight: 600;
    }

    .lease-empty {
        padding: 4rem 1.5rem;
        text-align: center;
    }

    .lease-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.7rem;
    }

    @media (max-width: 767.98px) {
        .lease-header {
            padding: 1.15rem;
        }

        .lease-directory-header {
            align-items: flex-start;
            padding: 1rem;
        }

        .lease-directory-header .lease-count {
            display: none;
        }
    }

    html[data-theme="dark"] .lease-header,
    html[data-theme="dark"] .lease-summary-card,
    html[data-theme="dark"] .lease-directory {
        --lease-border: #334155;
        --lease-surface: #111827;
        --lease-soft: #1e293b;
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .lease-header {
        background: linear-gradient(135deg, #111827 0%, #172033 100%);
    }

    html[data-theme="dark"] .lease-directory-header,
    html[data-theme="dark"] .lease-table thead th {
        background: #1e293b;
        border-color: #334155;
    }

    html[data-theme="dark"] .lease-metric,
    html[data-theme="dark"] .lease-money,
    html[data-theme="dark"] .lease-person,
    html[data-theme="dark"] .lease-property {
        color: #f8fafc;
    }

    html[data-theme="dark"] .lease-table tbody td {
        border-color: #263449;
    }

    html[data-theme="dark"] .lease-table tbody tr:hover {
        background: #172033;
    }

    html[data-theme="dark"] .lease-unit {
        background: #1e293b;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .lease-avatar {
        background: rgba(37, 99, 235, .18);
        color: #93c5fd;
    }

    html[data-theme="dark"] .lease-status.active {
        background: rgba(22, 101, 52, .25);
        color: #86efac;
    }

    html[data-theme="dark"] .lease-status.pending {
        background: rgba(146, 64, 14, .25);
        color: #fcd34d;
    }

    html[data-theme="dark"] .lease-status.expired {
        background: rgba(55, 48, 163, .25);
        color: #c7d2fe;
    }

    html[data-theme="dark"] .lease-status.terminated {
        background: rgba(153, 27, 27, .25);
        color: #fca5a5;
    }

    html[data-theme="dark"] .lease-status.unknown {
        background: #334155;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .lease-count {
        background: rgba(67, 56, 202, .22);
        color: #c7d2fe;
    }

    html[data-theme="dark"] .lease-empty-icon {
        background: rgba(37, 99, 235, .15);
        color: #93c5fd;
    }
</style>

@endpush

@section('content')

<div class="container-fluid lease-page px-0">

@php
    use App\Support\Currency;

    $totalLeases = $leases->count();

    $activeLeases = $leases
        ->where('status', 'active')
        ->count();

    $pendingLeases = $leases
        ->where('status', 'pending')
        ->count();

    $pastLeases = $leases
        ->whereIn('status', ['expired', 'terminated'])
        ->count();
@endphp

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Header --}}
<div class="lease-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="lease-eyebrow">Tenancy Management</div>

            <h1 class="h3 mb-1 fw-bold text-slate-900">
                Lease Directory
            </h1>

            <p class="text-muted mb-0">
                Manage tenant leases, rental contracts, and occupancy agreements.
            </p>
        </div>

        <a href="{{ route('leases.create') }}" class="btn pm-btn-primary">
            <i class="bi bi-file-earmark-plus me-1"></i>
            Add Lease
        </a>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="lease-summary-card h-100">
            <div class="lease-summary-body">
                <span class="lease-icon navy">
                    <i class="bi bi-file-earmark-text"></i>
                </span>

                <div>
                    <div class="lease-metric">{{ $totalLeases }}</div>
                    <div class="lease-label">Total Leases</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="lease-summary-card h-100">
            <div class="lease-summary-body">
                <span class="lease-icon green">
                    <i class="bi bi-check-circle"></i>
                </span>

                <div>
                    <div class="lease-metric">{{ $activeLeases }}</div>
                    <div class="lease-label">Active Leases</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="lease-summary-card h-100">
            <div class="lease-summary-body">
                <span class="lease-icon warning">
                    <i class="bi bi-clock"></i>
                </span>

                <div>
                    <div class="lease-metric">{{ $pendingLeases }}</div>
                    <div class="lease-label">Pending Leases</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="lease-summary-card h-100">
            <div class="lease-summary-body">
                <span class="lease-icon info">
                    <i class="bi bi-calendar-x"></i>
                </span>

                <div>
                    <div class="lease-metric">{{ $pastLeases }}</div>
                    <div class="lease-label">Past / Expired</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Lease Directory --}}
<div class="lease-directory">

    <div class="lease-directory-header">
        <div>
            <h5 class="fw-bold mb-1">
                Lease Directory
            </h5>

            <small class="text-muted">
                Registered tenancy agreements and rental terms
            </small>
        </div>

        <span class="lease-count">
            <i class="bi bi-file-earmark-text"></i>
            {{ $totalLeases }}
            {{ $totalLeases === 1 ? 'Lease' : 'Leases' }}
        </span>
    </div>

    <div class="table-responsive">

        @if($totalLeases)

            <table class="table lease-table align-middle mb-0">

                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Tenant</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Monthly Rent</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($leases as $lease)

                        @php
                            $leaseCurrency = strtoupper(
                                (string) (
                                    $lease->currency
                                    ?? $lease->unit?->currency
                                    ?? ''
                                )
                            );

                            if (!in_array($leaseCurrency, Currency::SUPPORTED, true)) {
                                $leaseCurrency = '';
                            }

                            $monthlyRent = round(
                                (float) (
                                    $lease->monthly_rent
                                    ?? $lease->unit?->monthly_rent
                                    ?? 0
                                ),
                                2
                            );

                            $tenantName = $lease->tenant?->full_name ?? 'Tenant';

                            $tenantInitials = collect(
                                preg_split('/\s+/', trim($tenantName))
                            )
                            ->filter()
                            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                            ->take(2)
                            ->implode('');

                            $tenantInitials = $tenantInitials ?: 'TN';

                            $statusClass = match ($lease->status) {
                                'active' => 'active',
                                'pending' => 'pending',
                                'expired' => 'expired',
                                'terminated' => 'terminated',
                                default => 'unknown',
                            };

                            $statusLabel = match ($lease->status) {
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'expired' => 'Expired',
                                'terminated' => 'Terminated',
                                default => ucfirst(str_replace('_', ' ', $lease->status ?? 'Unknown')),
                            };
                        @endphp

                        <tr>

                            {{-- Display Number --}}
                            <td class="ps-4">
                                <span class="lease-number">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            {{-- Tenant --}}
                            <td>
                                @if($lease->tenant)
                                    <a
                                        href="{{ route('tenants.show', $lease->tenant) }}"
                                        class="lease-person"
                                    >
                                        <span class="lease-avatar">
                                            {{ $tenantInitials }}
                                        </span>

                                        <span>
                                            {{ $lease->tenant->full_name }}
                                        </span>
                                    </a>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>

                            {{-- Property --}}
                            <td>
                                @if($lease->unit?->property)
                                    <a
                                        href="{{ route('properties.show', $lease->unit->property) }}"
                                        class="lease-property"
                                    >
                                        <i class="bi bi-building text-primary"></i>
                                        {{ $lease->unit->property->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>

                            {{-- Unit --}}
                            <td>
                                @if($lease->unit)
                                    <span class="lease-unit">
                                        <i class="bi bi-door-open"></i>
                                        {{ $lease->unit->unit_number }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Start --}}
                            <td>
                                <span class="lease-date">
                                    {{ $lease->start_date?->format('d M Y') ?? '—' }}
                                </span>
                            </td>

                            {{-- End --}}
                            <td>
                                <span class="lease-date">
                                    {{ $lease->end_date?->format('d M Y') ?? '—' }}
                                </span>
                            </td>

                            {{-- Rent --}}
                            <td>
                                @if($leaseCurrency)
                                    <div class="lease-money">
                                        {{ Currency::format($monthlyRent, $leaseCurrency) }}
                                    </div>

                                    <span class="lease-money-sub">
                                        {{ Currency::name($leaseCurrency) }} / month
                                    </span>
                                @else
                                    <div class="text-muted fw-semibold">—</div>
                                    <span class="text-danger small">
                                        Currency not set
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="lease-status {{ $statusClass }}">
                                    <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end flex-wrap gap-1">

                                    <a
                                        href="{{ route('leases.show', $lease) }}"
                                        class="btn btn-sm pm-btn-outline lease-action"
                                        title="View Lease"
                                    >
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-lg-inline ms-1">View</span>
                                    </a>

                                    <a
                                        href="{{ route('leases.edit', $lease) }}"
                                        class="btn btn-sm pm-btn-outline-warning lease-action"
                                        title="Edit Lease"
                                    >
                                        <i class="bi bi-pencil"></i>
                                        <span class="d-none d-lg-inline ms-1">Edit</span>
                                    </a>

                                    <form
                                        action="{{ route('leases.destroy', $lease) }}"
                                        method="POST"
                                        class="d-inline lease-delete-form"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm pm-btn-outline-danger lease-action"
                                            title="Delete Lease"
                                        >
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-lg-inline ms-1">Delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="lease-empty">

                <div class="lease-empty-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>

                <h5 class="fw-bold text-slate-900 mb-2">
                    No Leases Found
                </h5>

                <p class="text-muted mb-4">
                    There are currently no lease agreements recorded in the system.
                </p>

                <a href="{{ route('leases.create') }}" class="btn pm-btn-primary">
                    <i class="bi bi-file-earmark-plus me-1"></i>
                    Add Lease
                </a>

            </div>

        @endif

    </div>
</div>

</div>
@endsection

@push('scripts')

<script>
    document.querySelectorAll('.lease-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const confirmed = window.confirm(
                'Are you sure you want to delete this lease? This action cannot be undone.'
            );

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
</script>

@endpush
