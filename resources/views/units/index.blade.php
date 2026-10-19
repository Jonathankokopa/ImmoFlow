@extends('layouts.app')

@section('title', 'Units')
@section('page-heading', 'Units')

@section('content')

@php
    use App\Support\Currency;

    $totalUnits = $units->count();
    $vacantUnits = $units->where('status', 'vacant')->count();
    $occupiedUnits = $units->where('status', 'occupied')->count();
    $reservedUnits = $units->where('status', 'reserved')->count();
    $maintenanceUnits = $units->where('status', 'maintenance')->count();
@endphp

@push('styles')
    <style>
        .units-page {
            --units-border: #e7ebf0;
            --units-muted: #6b7280;
            --units-dark: #172033;
        }

        .units-header {
            padding: .25rem 0 .5rem;
        }

        .units-eyebrow {
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

        .units-eyebrow::before {
            content: '';
            width: 24px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .units-title {
            color: var(--units-dark);
            letter-spacing: -.025em;
        }

        .units-card {
            border: 1px solid var(--units-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .units-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--units-border);
        }

        .units-summary-card {
            height: 100%;
            border: 1px solid var(--units-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .045);
            padding: 1.1rem;
            position: relative;
            overflow: hidden;
        }

        .units-summary-card::after {
            content: '';
            position: absolute;
            right: -24px;
            bottom: -34px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(15, 23, 42, .025);
        }

        .units-summary-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        .units-icon-navy {
            color: #334155;
            background: #f1f5f9;
        }

        .units-icon-info {
            color: #0369a1;
            background: #e0f2fe;
        }

        .units-icon-success {
            color: #047857;
            background: #d1fae5;
        }

        .units-icon-warning {
            color: #b45309;
            background: #fef3c7;
        }

        .units-metric-value {
            color: var(--units-dark);
            font-size: 1.45rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .units-metric-label {
            color: var(--units-muted);
            font-size: .78rem;
            font-weight: 600;
            margin-top: .2rem;
        }

        .units-table {
            min-width: 1050px;
        }

        .units-table thead th {
            background: #f8fafc;
            color: #64748b;
            border-bottom: 1px solid var(--units-border);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .055em;
            text-transform: uppercase;
            white-space: nowrap;
            padding-top: .85rem;
            padding-bottom: .85rem;
        }

        .units-table tbody td {
            border-color: #eef1f5;
            padding-top: .95rem;
            padding-bottom: .95rem;
            color: #475569;
        }

        .units-table tbody tr {
            transition: background .15s ease;
        }

        .units-table tbody tr:hover {
            background: #fafcff;
        }

        .unit-avatar {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .unit-number {
            color: var(--units-dark);
            font-weight: 750;
        }

        .unit-number:hover {
            color: #0d6efd;
        }

        .unit-reference {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: .1rem;
        }

        .property-name {
            color: var(--units-dark);
            font-weight: 700;
        }

        .property-location {
            color: #94a3b8;
            font-size: .75rem;
            margin-top: .1rem;
        }

        .currency-amount {
            color: var(--units-dark);
            font-weight: 750;
            white-space: nowrap;
        }

        .currency-name {
            color: #94a3b8;
            font-size: .72rem;
            margin-top: .1rem;
        }

        .unit-status {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            padding: .4rem .65rem;
            font-size: .72rem;
            font-weight: 750;
            white-space: nowrap;
        }

        .unit-status-vacant {
            color: #0369a1;
            background: #e0f2fe;
        }

        .unit-status-occupied {
            color: #047857;
            background: #d1fae5;
        }

        .unit-status-reserved {
            color: #b45309;
            background: #fef3c7;
        }

        .unit-status-maintenance {
            color: #b91c1c;
            background: #fee2e2;
        }

        .unit-actions {
            display: inline-flex;
            justify-content: flex-end;
            gap: .35rem;
        }

        .unit-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            transition: all .15s ease;
        }

        .unit-action:hover {
            transform: translateY(-1px);
            color: #0d6efd;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .unit-action-warning:hover {
            color: #b45309;
            border-color: #fde68a;
            background: #fffbeb;
        }

        .unit-action-danger:hover {
            color: #b91c1c;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .units-empty {
            padding: 4.5rem 1.5rem;
        }

        .units-empty-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
            font-size: 1.8rem;
        }

        .units-count-badge {
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

        .units-btn-primary {
            border-radius: 10px;
            font-weight: 700;
            padding: .62rem 1rem;
        }

        .units-alert {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
        }

        @media (max-width: 767.98px) {
            .units-card-header {
                align-items: flex-start;
            }

            .units-table {
                min-width: 980px;
            }
        }

        html[data-theme="dark"] .units-page .units-title,
        html[data-theme="dark"] .units-page .unit-number,
        html[data-theme="dark"] .units-page .property-name,
        html[data-theme="dark"] .units-page .currency-amount,
        html[data-theme="dark"] .units-page .units-metric-value {
            color: #f8fafc !important;
        }

        html[data-theme="dark"] .units-card,
        html[data-theme="dark"] .units-summary-card {
            background: #111827;
            border-color: #263244;
            box-shadow: none;
        }

        html[data-theme="dark"] .units-card-header,
        html[data-theme="dark"] .units-table tbody td {
            border-color: #263244;
        }

        html[data-theme="dark"] .units-table thead th {
            background: #172033;
            color: #94a3b8;
            border-color: #263244;
        }

        html[data-theme="dark"] .units-table tbody tr:hover {
            background: #151f31;
        }

        html[data-theme="dark"] .unit-action {
            background: #111827;
            border-color: #334155;
            color: #cbd5e1;
        }

        html[data-theme="dark"] .unit-avatar,
        html[data-theme="dark"] .units-empty-icon,
        html[data-theme="dark"] .units-count-badge,
        html[data-theme="dark"] .units-icon-navy {
            background: #1e293b;
            color: #cbd5e1;
        }

        html[data-theme="dark"] .units-metric-label,
        html[data-theme="dark"] .property-location,
        html[data-theme="dark"] .currency-name,
        html[data-theme="dark"] .unit-reference {
            color: #94a3b8 !important;
        }
    </style>
@endpush

<div class="container-fluid units-page px-0">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show units-alert mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show units-alert mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 units-header">
        <div>
            <div class="units-eyebrow">Property Management</div>

            <h1 class="h3 mb-1 fw-bold units-title">
                Units
            </h1>

            <p class="text-muted mb-0">
                Manage and monitor every individual unit across your property portfolio.
            </p>
        </div>

        @if(!auth()->user()->isOwner())
            <a href="{{ route('units.create') }}" class="btn btn-primary units-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Unit
            </a>
        @endif
    </div>

    @if($totalUnits)

        {{-- Summary --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-xl-3">
                <div class="units-summary-card">
                    <div class="d-flex align-items-center gap-3">
                        <span class="units-summary-icon units-icon-navy">
                            <i class="bi bi-door-open"></i>
                        </span>

                        <div>
                            <div class="units-metric-value">{{ $totalUnits }}</div>
                            <div class="units-metric-label">Total Units</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="units-summary-card">
                    <div class="d-flex align-items-center gap-3">
                        <span class="units-summary-icon units-icon-info">
                            <i class="bi bi-house-dash"></i>
                        </span>

                        <div>
                            <div class="units-metric-value">{{ $vacantUnits }}</div>
                            <div class="units-metric-label">Vacant Units</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="units-summary-card">
                    <div class="d-flex align-items-center gap-3">
                        <span class="units-summary-icon units-icon-success">
                            <i class="bi bi-house-check"></i>
                        </span>

                        <div>
                            <div class="units-metric-value">{{ $occupiedUnits }}</div>
                            <div class="units-metric-label">Occupied Units</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="units-summary-card">
                    <div class="d-flex align-items-center gap-3">
                        <span class="units-summary-icon units-icon-warning">
                            <i class="bi bi-tools"></i>
                        </span>

                        <div>
                            <div class="units-metric-value">
                                {{ $reservedUnits + $maintenanceUnits }}
                            </div>
                            <div class="units-metric-label">
                                Reserved / Maintenance
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Directory --}}
        <div class="units-card">

            <div class="units-card-header">
                <div>
                    <h5 class="mb-1 fw-bold">Units Directory</h5>
                    <small class="text-muted">
                        Registered units and their current rental status
                    </small>
                </div>

                <span class="units-count-badge">
                    <i class="bi bi-grid-3x3-gap"></i>
                    {{ $totalUnits }} {{ $totalUnits === 1 ? 'Unit' : 'Units' }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table units-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Unit</th>
                            <th>Property</th>
                            <th>Floor</th>
                            <th>Bedrooms</th>
                            <th>Bathrooms</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($units as $unit)
                            <tr>

                                {{-- Display row number, not database ID --}}
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="unit-avatar">
                                            <i class="bi bi-door-closed"></i>
                                        </span>

                                        <div>
                                            <a
                                                href="{{ route('units.show', $unit) }}"
                                                class="unit-number text-decoration-none"
                                            >
                                                {{ $unit->unit_number }}
                                            </a>

                                            <div class="unit-reference">
                                                Unit #{{ $unit->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="property-name">
                                        {{ $unit->property->name ?? 'N/A' }}
                                    </div>

                                    @if($unit->property?->city)
                                        <div class="property-location">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $unit->property->city }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $unit->floor ?? '—' }}</td>
                                <td>{{ $unit->bedrooms }}</td>
                                <td>{{ $unit->bathrooms }}</td>

                                <td>
                                    <div class="currency-amount">
                                        {{ Currency::format(
                                            (float) $unit->monthly_rent,
                                            $unit->currency
                                        ) }}
                                    </div>

                                    <div class="currency-name">
                                        {{ Currency::name($unit->currency) }} / month
                                    </div>
                                </td>

                                <td>
                                    @switch($unit->status)
                                        @case('vacant')
                                            <span class="unit-status unit-status-vacant">
                                                <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                                                Vacant
                                            </span>
                                            @break

                                        @case('occupied')
                                            <span class="unit-status unit-status-occupied">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Occupied
                                            </span>
                                            @break

                                        @case('reserved')
                                            <span class="unit-status unit-status-reserved">
                                                <i class="bi bi-clock-fill"></i>
                                                Reserved
                                            </span>
                                            @break

                                        @default
                                            <span class="unit-status unit-status-maintenance">
                                                <i class="bi bi-tools"></i>
                                                Maintenance
                                            </span>
                                    @endswitch
                                </td>

                                <td class="text-end pe-4">
                                    <div class="unit-actions">

                                        <a
                                            href="{{ route('units.show', $unit) }}"
                                            class="unit-action"
                                            title="View Unit"
                                            aria-label="View Unit"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a
                                            href="{{ route('units.edit', $unit) }}"
                                            class="unit-action unit-action-warning"
                                            title="Edit Unit"
                                            aria-label="Edit Unit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form
                                            action="{{ route('units.destroy', $unit) }}"
                                            method="POST"
                                            class="d-inline unit-delete-form"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="unit-action unit-action-danger"
                                                title="Delete Unit"
                                                aria-label="Delete Unit"
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
            </div>

        </div>

    @else

        {{-- Empty State --}}
        <div class="units-card">
            <div class="units-empty text-center">

                <div class="units-empty-icon mb-3">
                    <i class="bi bi-door-open"></i>
                </div>

                <h4 class="fw-bold units-title mb-2">
                    No Units Yet
                </h4>

                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    There are no registered units yet. Add your first unit to start
                    managing occupancy, rental amounts, and leases.
                </p>

                @if(!auth()->user()->isOwner())
                    <a href="{{ route('units.create') }}" class="btn btn-primary units-btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Unit
                    </a>
                @endif

            </div>
        </div>

    @endif

</div>

@endsection

@push('scripts') <script>
document.querySelectorAll('.unit-delete-form').forEach(function (form) {
form.addEventListener('submit', function (event) {
const confirmed = window.confirm(
'Are you sure you want to delete this unit? This action cannot be undone.'
);

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
</script>

@endpush
