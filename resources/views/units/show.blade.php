@extends('layouts.app')

@section('title', 'Unit Details')
@section('page-heading', 'Unit Details')

@section('content')

@php
    use App\Support\Currency;

    $leaseCount = $unit->leases->count();
    $maintenanceCount = $unit->maintenanceRequests->count();

    $statusLabel = match ($unit->status) {
        'vacant' => 'Vacant',
        'occupied' => 'Occupied',
        'reserved' => 'Reserved',
        'maintenance' => 'Maintenance',
        default => ucwords(str_replace('_', ' ', $unit->status)),
    };

    $statusClass = match ($unit->status) {
        'vacant' => 'us-status-info',
        'occupied' => 'us-status-success',
        'reserved' => 'us-status-warning',
        'maintenance' => 'us-status-danger',
        default => 'us-status-neutral',
    };

    $statusIcon = match ($unit->status) {
        'vacant' => 'bi-circle-fill',
        'occupied' => 'bi-check-circle-fill',
        'reserved' => 'bi-clock-fill',
        'maintenance' => 'bi-tools',
        default => 'bi-circle',
    };
@endphp

@push('styles')
    <style>
        .unit-show-page {
            --us-border: #e7ebf0;
            --us-dark: #172033;
            --us-muted: #64748b;
        }

        .us-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #64748b;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: .45rem;
        }

        .us-eyebrow::before {
            content: '';
            width: 24px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .us-title {
            color: var(--us-dark);
            letter-spacing: -.025em;
        }

        .us-card {
            border: 1px solid var(--us-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .us-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--us-border);
        }

        .us-card-body {
            padding: 1.25rem;
        }

        .us-summary {
            height: 100%;
            border: 1px solid var(--us-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            padding: 1.1rem;
        }

        .us-summary-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        .us-icon-primary {
            background: #eff6ff;
            color: #2563eb;
        }

        .us-icon-success {
            background: #d1fae5;
            color: #047857;
        }

        .us-icon-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .us-icon-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .us-metric-value {
            color: var(--us-dark);
            font-size: 1.25rem;
            line-height: 1.2;
            font-weight: 800;
        }

        .us-metric-label {
            color: var(--us-muted);
            font-size: .74rem;
            font-weight: 650;
            margin-top: .2rem;
        }

        .us-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            background: var(--us-border);
            border: 1px solid var(--us-border);
            border-radius: 13px;
            overflow: hidden;
        }

        .us-info-item {
            background: #fff;
            padding: 1rem;
            min-height: 94px;
        }

        .us-info-label {
            color: #94a3b8;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: .35rem;
        }

        .us-info-value {
            color: var(--us-dark);
            font-size: .88rem;
            font-weight: 700;
        }

        .us-info-value a {
            color: #2563eb;
        }

        .us-info-subtext {
            color: #94a3b8;
            font-size: .72rem;
            margin-top: .2rem;
        }

        .us-status {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .42rem .68rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 750;
            white-space: nowrap;
        }

        .us-status-info {
            color: #0369a1;
            background: #e0f2fe;
        }

        .us-status-success {
            color: #047857;
            background: #d1fae5;
        }

        .us-status-warning {
            color: #b45309;
            background: #fef3c7;
        }

        .us-status-danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .us-status-neutral {
            color: #475569;
            background: #f1f5f9;
        }

        .us-table {
            min-width: 850px;
        }

        .us-table thead th {
            background: #f8fafc;
            color: #64748b;
            border-bottom: 1px solid var(--us-border);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .055em;
            text-transform: uppercase;
            white-space: nowrap;
            padding-top: .85rem;
            padding-bottom: .85rem;
        }

        .us-table tbody td {
            border-color: #eef1f5;
            color: #475569;
            padding-top: .9rem;
            padding-bottom: .9rem;
        }

        .us-table tbody tr:hover {
            background: #fafcff;
        }

        .us-empty {
            padding: 3.5rem 1.5rem;
            text-align: center;
        }

        .us-empty-icon {
            width: 62px;
            height: 62px;
            border-radius: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
            font-size: 1.5rem;
        }

        .us-count {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .42rem .7rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: .72rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .us-btn {
            border-radius: 10px;
            font-weight: 700;
            padding: .62rem 1rem;
        }

        .us-rent {
            color: var(--us-dark);
            font-weight: 800;
        }

        .us-rent-label {
            color: #94a3b8;
            font-size: .72rem;
            margin-top: .15rem;
        }

        @media (max-width: 991.98px) {
            .us-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .us-info-grid {
                grid-template-columns: 1fr;
            }

            .us-card-header {
                align-items: flex-start;
            }
        }

        html[data-theme="dark"] .us-title,
        html[data-theme="dark"] .us-metric-value,
        html[data-theme="dark"] .us-info-value,
        html[data-theme="dark"] .us-rent {
            color: #f8fafc !important;
        }

        html[data-theme="dark"] .us-card,
        html[data-theme="dark"] .us-summary {
            background: #111827;
            border-color: #263244;
            box-shadow: none;
        }

        html[data-theme="dark"] .us-card-header {
            border-color: #263244;
        }

        html[data-theme="dark"] .us-info-grid {
            background: #263244;
            border-color: #263244;
        }

        html[data-theme="dark"] .us-info-item {
            background: #111827;
        }

        html[data-theme="dark"] .us-table thead th {
            background: #172033;
            color: #94a3b8;
            border-color: #263244;
        }

        html[data-theme="dark"] .us-table tbody td {
            border-color: #263244;
        }

        html[data-theme="dark"] .us-table tbody tr:hover {
            background: #151f31;
        }

        html[data-theme="dark"] .us-empty-icon,
        html[data-theme="dark"] .us-count {
            background: #1e293b;
            color: #cbd5e1;
        }

        html[data-theme="dark"] .us-metric-label,
        html[data-theme="dark"] .us-info-subtext,
        html[data-theme="dark"] .us-rent-label {
            color: #94a3b8;
        }

        html[data-theme="dark"] .us-icon-primary,
        html[data-theme="dark"] .us-icon-info,
        html[data-theme="dark"] .us-icon-success,
        html[data-theme="dark"] .us-icon-warning {
            background: #1e293b;
        }
    </style>
@endpush

<div class="container-fluid unit-show-page px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">

        <div>
            <div class="us-eyebrow">Units / Details</div>

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <h1 class="h3 mb-1 fw-bold us-title">
                    Unit {{ $unit->unit_number }}
                </h1>

                <span class="us-status {{ $statusClass }}">
                    <i class="bi {{ $statusIcon }}"></i>
                    {{ $statusLabel }}
                </span>

            </div>

            <p class="text-muted mb-0">
                View specifications, lease history, and maintenance activity for this unit.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a
                href="{{ route('units.edit', $unit) }}"
                class="btn btn-outline-warning us-btn"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit Unit
            </a>

            <a
                href="{{ route('units.index') }}"
                class="btn btn-outline-secondary us-btn"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Units
            </a>

        </div>

    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="us-summary">

                <div class="d-flex align-items-center gap-3">

                    <span class="us-summary-icon us-icon-primary">
                        <i class="bi bi-building"></i>
                    </span>

                    <div class="min-w-0">
                        <div class="us-metric-value text-truncate">
                            {{ $unit->property->name ?? '—' }}
                        </div>

                        <div class="us-metric-label">
                            Property
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="us-summary">

                <div class="d-flex align-items-center gap-3">

                    <span class="us-summary-icon us-icon-success">
                        <i class="bi bi-cash-stack"></i>
                    </span>

                    <div>
                        <div class="us-metric-value">
                            {{ Currency::format(
                                (float) $unit->monthly_rent,
                                $unit->currency
                            ) }}
                        </div>

                        <div class="us-metric-label">
                            Monthly Rent
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="us-summary">

                <div class="d-flex align-items-center gap-3">

                    <span class="us-summary-icon us-icon-info">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>

                    <div>
                        <div class="us-metric-value">
                            {{ $leaseCount }}
                        </div>

                        <div class="us-metric-label">
                            Total Leases
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="us-summary">

                <div class="d-flex align-items-center gap-3">

                    <span class="us-summary-icon us-icon-warning">
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>
                        <div class="us-metric-value">
                            {{ $maintenanceCount }}
                        </div>

                        <div class="us-metric-label">
                            Maintenance Requests
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    {{-- Unit Information --}}
    <div class="us-card mb-4">

        <div class="us-card-header">

            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="bi bi-door-open me-2 text-primary"></i>
                    Unit Information
                </h5>

                <small class="text-muted">
                    Core specifications and rental configuration
                </small>
            </div>

            <span class="us-count">
                <i class="bi bi-hash"></i>
                Unit {{ $unit->id }}
            </span>

        </div>

        <div class="us-card-body">

            <div class="us-info-grid">

                <div class="us-info-item">
                    <div class="us-info-label">Property</div>

                    <div class="us-info-value">
                        @if($unit->property)
                            <a
                                href="{{ route('properties.show', $unit->property) }}"
                                class="text-decoration-none"
                            >
                                <i class="bi bi-buildings me-1"></i>
                                {{ $unit->property->name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    @if($unit->property?->city)
                        <div class="us-info-subtext">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $unit->property->city }}
                        </div>
                    @endif
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Unit Number</div>

                    <div class="us-info-value">
                        {{ $unit->unit_number }}
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Floor</div>

                    <div class="us-info-value">
                        {{ $unit->floor ?? '—' }}
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Bedrooms</div>

                    <div class="us-info-value">
                        {{ $unit->bedrooms }}
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Bathrooms</div>

                    <div class="us-info-value">
                        {{ $unit->bathrooms }}
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Monthly Rent</div>

                    <div class="us-info-value us-rent">
                        {{ Currency::format(
                            (float) $unit->monthly_rent,
                            $unit->currency
                        ) }}
                    </div>

                    <div class="us-info-subtext">
                        {{ Currency::name($unit->currency) }} / month
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Currency</div>

                    <div class="us-info-value">
                        {{ $unit->currency }}
                    </div>

                    <div class="us-info-subtext">
                        {{ Currency::name($unit->currency) }}
                        ({{ Currency::symbol($unit->currency) }})
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Status</div>

                    <div>
                        <span class="us-status {{ $statusClass }}">
                            <i class="bi {{ $statusIcon }}"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="us-info-item">
                    <div class="us-info-label">Record ID</div>

                    <div class="us-info-value">
                        #{{ $unit->id }}
                    </div>

                    <div class="us-info-subtext">
                        System reference
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Leases --}}
    <div class="us-card mb-4">

        <div class="us-card-header">

            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                    Leases
                </h5>

                <small class="text-muted">
                    Tenancy agreements associated with this unit
                </small>
            </div>

            <span class="us-count">
                <i class="bi bi-files"></i>
                {{ $leaseCount }}
                {{ $leaseCount === 1 ? 'Lease' : 'Leases' }}
            </span>

        </div>

        <div class="p-0">

            @if($leaseCount)

                <div class="table-responsive">

                    <table class="table us-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Tenant</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Monthly Rent</th>
                                <th>Currency</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($unit->leases as $lease)

                                <tr>

                                    {{-- Display row number, not lease ID --}}
                                    <td class="ps-4 text-muted fw-semibold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $lease->tenant->full_name ?? 'Tenant #' . $lease->tenant_id }}
                                        </div>

                                        <div class="small text-muted">
                                            Lease #{{ $lease->id }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $lease->start_date }}
                                    </td>

                                    <td>
                                        {{ $lease->end_date }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ Currency::format(
                                                (float) $lease->monthly_rent,
                                                $lease->currency
                                            ) }}
                                        </div>

                                        <div class="small text-muted">
                                            {{ Currency::name($lease->currency) }} / month
                                        </div>
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $lease->currency }}
                                        </span>

                                        <div class="small text-muted">
                                            {{ Currency::symbol($lease->currency) }}
                                        </div>
                                    </td>

                                    <td>

                                        @if($lease->status === 'active')

                                            <span class="us-status us-status-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Active
                                            </span>

                                        @elseif($lease->status === 'expired')

                                            <span class="us-status us-status-neutral">
                                                <i class="bi bi-clock-history"></i>
                                                Expired
                                            </span>

                                        @elseif($lease->status === 'terminated')

                                            <span class="us-status us-status-danger">
                                                <i class="bi bi-x-circle-fill"></i>
                                                Terminated
                                            </span>

                                        @else

                                            <span class="us-status us-status-warning">
                                                {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="us-empty">

                    <div class="us-empty-icon mb-3">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <h5 class="fw-bold us-title mb-1">
                        No Leases Found
                    </h5>

                    <p class="text-muted mb-0">
                        No lease agreements have been registered for this unit yet.
                    </p>

                </div>

            @endif

        </div>

    </div>

    {{-- Maintenance Requests --}}
    <div class="us-card">

        <div class="us-card-header">

            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="bi bi-tools me-2 text-primary"></i>
                    Maintenance Requests
                </h5>

                <small class="text-muted">
                    Maintenance activity and service history for this unit
                </small>
            </div>

            <span class="us-count">
                <i class="bi bi-wrench-adjustable"></i>
                {{ $maintenanceCount }}
                {{ $maintenanceCount === 1 ? 'Request' : 'Requests' }}
            </span>

        </div>

        <div class="p-0">

            @if($maintenanceCount)

                <div class="table-responsive">

                    <table class="table us-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Requested Date</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($unit->maintenanceRequests as $request)

                                <tr>

                                    {{-- Display row number, not request ID --}}
                                    <td class="ps-4 text-muted fw-semibold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $request->title }}
                                        </div>

                                        <div class="small text-muted">
                                            Request #{{ $request->id }}
                                        </div>
                                    </td>

                                    <td>

                                        @if($request->priority === 'high')

                                            <span class="us-status us-status-danger">
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                                High
                                            </span>

                                        @elseif($request->priority === 'medium')

                                            <span class="us-status us-status-warning">
                                                <i class="bi bi-dash-circle-fill"></i>
                                                Medium
                                            </span>

                                        @elseif($request->priority === 'low')

                                            <span class="us-status us-status-success">
                                                <i class="bi bi-arrow-down-circle-fill"></i>
                                                Low
                                            </span>

                                        @else

                                            <span class="us-status us-status-neutral">
                                                {{ ucfirst($request->priority) }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($request->status === 'open')

                                            <span class="us-status us-status-danger">
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                                Open
                                            </span>

                                        @elseif($request->status === 'in_progress')

                                            <span class="us-status us-status-warning">
                                                <i class="bi bi-arrow-repeat"></i>
                                                In Progress
                                            </span>

                                        @elseif($request->status === 'completed')

                                            <span class="us-status us-status-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Completed
                                            </span>

                                        @else

                                            <span class="us-status us-status-neutral">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>
                                        {{ $request->requested_date }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="us-empty">

                    <div class="us-empty-icon mb-3">
                        <i class="bi bi-tools"></i>
                    </div>

                    <h5 class="fw-bold us-title mb-1">
                        No Maintenance Requests
                    </h5>

                    <p class="text-muted mb-0">
                        No maintenance requests have been recorded for this unit yet.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
