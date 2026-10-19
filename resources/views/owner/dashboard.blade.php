@extends('layouts.app')

@section('title', 'Owner Portfolio Dashboard')
@section('page-heading', 'Owner Portfolio Dashboard')

@php
    use App\Support\Currency;
@endphp

@push('styles')
<style>
    .owner-dashboard {
        --od-primary: #0d9488;
        --od-primary-dark: #0f766e;
        --od-accent: #2563eb;
        --od-success: #16a34a;
        --od-warning: #d97706;
        --od-danger: #dc2626;
        --od-bg-card: #ffffff;
        --od-border: #e2e8f0;
        --od-text-main: #0f172a;
        --od-text-muted: #64748b;
    }

    .od-hero {
        position: relative;
        overflow: hidden;
        padding: 1.75rem;
        border-radius: 1.1rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }

    .od-hero::after {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        right: -100px;
        top: -100px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(13, 148, 136, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .od-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border: 1px solid var(--od-border);
        border-radius: 0.9rem;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    }

    .od-time-pills {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        padding: 0.25rem;
        border-radius: 0.65rem;
        background: #f1f5f9;
    }

    .od-time-pill {
        padding: 0.4rem 0.85rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--od-text-muted);
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .od-time-pill:hover {
        color: var(--od-text-main);
        background: rgba(255, 255, 255, 0.7);
    }

    .od-time-pill.active {
        background: #ffffff;
        color: var(--od-primary-dark);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .od-currency-pills {
        display: inline-flex;
        gap: 0.3rem;
    }

    .od-curr-btn {
        padding: 0.35rem 0.75rem;
        border: 1px solid var(--od-border);
        border-radius: 0.5rem;
        background: #ffffff;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--od-text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .od-curr-btn.active {
        background: var(--od-primary);
        color: #ffffff;
        border-color: var(--od-primary);
        box-shadow: 0 3px 8px rgba(13, 148, 136, 0.25);
    }

    /* KPI Cards */
    .od-kpi-card {
        height: 100%;
        padding: 1.25rem;
        border: 1px solid var(--od-border);
        border-radius: 0.95rem;
        background: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.035);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .od-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
    }

    .od-kpi-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 1.25rem;
    }

    .od-kpi-icon.teal { background: rgba(13, 148, 136, 0.1); color: var(--od-primary); }
    .od-kpi-icon.green { background: rgba(22, 163, 74, 0.1); color: var(--od-success); }
    .od-kpi-icon.blue { background: rgba(37, 99, 235, 0.1); color: var(--od-accent); }
    .od-kpi-icon.amber { background: rgba(217, 119, 6, 0.1); color: var(--od-warning); }
    .od-kpi-icon.red { background: rgba(220, 38, 38, 0.1); color: var(--od-danger); }
    .od-kpi-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

    .od-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--od-text-muted);
    }

    .od-kpi-value {
        margin-top: 0.25rem;
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--od-text-main);
        line-height: 1.2;
    }

    .od-kpi-subtext {
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: var(--od-text-muted);
    }

    /* Collection Rate Meter */
    .od-meter-wrap {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .od-progress-ring {
        position: relative;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: conic-gradient(var(--od-primary) calc(var(--pct) * 1%), #e2e8f0 0);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 72px;
    }

    .od-progress-ring::before {
        content: "";
        position: absolute;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #ffffff;
    }

    .od-progress-text {
        position: relative;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--od-text-main);
    }

    /* Charts & Section Cards */
    .od-card {
        height: 100%;
        border: 1px solid var(--od-border);
        border-radius: 1rem;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.035);
        overflow: hidden;
    }

    .od-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--od-border);
    }

    .od-card-body {
        padding: 1.35rem;
    }

    /* Progress bar breakdown */
    .od-split-bar {
        display: flex;
        height: 14px;
        border-radius: 999px;
        overflow: hidden;
        background: #e2e8f0;
        margin: 0.75rem 0;
    }

    .od-split-segment {
        height: 100%;
        transition: width 0.3s ease;
    }

    .od-split-segment.collected { background: var(--od-success); }
    .od-split-segment.outstanding { background: var(--od-warning); }
    .od-split-segment.overdue { background: var(--od-danger); }

    /* Tables */
    .od-table-wrap {
        overflow-x: auto;
    }

    .od-table {
        width: 100%;
        margin-bottom: 0;
    }

    .od-table thead th {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--od-border);
        background: #f8fafc;
        color: var(--od-text-muted);
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .od-table tbody td {
        padding: 0.95rem 1rem;
        border-color: #f1f5f9;
        font-size: 0.86rem;
    }

    html[data-theme="dark"] .owner-dashboard {
        --od-bg-card: #151922;
        --od-border: #29303d;
        --od-text-main: #f1f5f9;
        --od-text-muted: #94a3b8;
    }

    html[data-theme="dark"] .od-filter-bar,
    html[data-theme="dark"] .od-kpi-card,
    html[data-theme="dark"] .od-card {
        background: #151922;
        border-color: #29303d;
    }

    html[data-theme="dark"] .od-time-pills {
        background: #0f172a;
    }

    html[data-theme="dark"] .od-time-pill {
        color: #94a3b8;
    }

    html[data-theme="dark"] .od-time-pill.active {
        background: #1e293b;
        color: #38bdf8;
    }

    html[data-theme="dark"] .od-curr-btn {
        background: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }

    html[data-theme="dark"] .od-progress-ring::before {
        background: #151922;
    }

    html[data-theme="dark"] .od-table thead th {
        background: #0f172a;
        border-color: #29303d;
        color: #94a3b8;
    }

    html[data-theme="dark"] .od-table tbody td {
        border-color: #252c38;
        color: #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid owner-dashboard px-0">

    {{-- Hero Section --}}
    <div class="od-hero mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 position-relative z-1">
            <div>
                <div class="small text-teal-400 fw-semibold mb-1" style="color: #2dd4bf;">
                    <i class="bi bi-buildings me-1"></i> Owner Portfolio & Financial Analytics
                </div>
                <h1 class="h2 mb-1 fw-bold text-white">
                    {{ $owner ? $owner->full_name : 'Portfolio Overview' }}
                </h1>
                <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">
                    Real-time expected vs. collected revenue, occupancy statistics, and portfolio health.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(auth()->user()->isAdmin() && $ownersList->count() > 0)
                    <form action="{{ route('owner.dashboard') }}" method="GET" class="d-inline-block">
                        <input type="hidden" name="timeframe" value="{{ $timeframe }}">
                        <select name="owner_id" class="form-select form-select-sm border-0 shadow-sm" onchange="this.form.submit()" style="min-width: 200px; font-weight: 600;">
                            @foreach($ownersList as $oItem)
                                <option value="{{ $oItem->id }}" {{ $owner && $owner->id == $oItem->id ? 'selected' : '' }}>
                                    {{ $oItem->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if($owner)
                    <a href="{{ route('owners.show', $owner) }}" class="btn btn-sm btn-outline-light rounded-2 fw-semibold">
                        <i class="bi bi-person-badge me-1"></i> Owner Profile
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Filter & Timeframe Toolbar --}}
    <div class="od-filter-bar mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold small text-muted me-1">
                <i class="bi bi-calendar3 me-1"></i> Timeframe:
            </span>

            <div class="od-time-pills">
                @php
                    $tfParams = fn($tf) => array_merge(request()->query(), ['timeframe' => $tf]);
                @endphp
                <a href="{{ route('owner.dashboard', $tfParams('today')) }}" class="od-time-pill {{ $timeframe === 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('owner.dashboard', $tfParams('this_week')) }}" class="od-time-pill {{ $timeframe === 'this_week' ? 'active' : '' }}">This Week</a>
                <a href="{{ route('owner.dashboard', $tfParams('this_month')) }}" class="od-time-pill {{ $timeframe === 'this_month' ? 'active' : '' }}">This Month</a>
                <a href="{{ route('owner.dashboard', $tfParams('last_month')) }}" class="od-time-pill {{ $timeframe === 'last_month' ? 'active' : '' }}">Last Month</a>
                <a href="{{ route('owner.dashboard', $tfParams('this_quarter')) }}" class="od-time-pill {{ $timeframe === 'this_quarter' ? 'active' : '' }}">This Quarter</a>
                <a href="{{ route('owner.dashboard', $tfParams('this_year')) }}" class="od-time-pill {{ $timeframe === 'this_year' ? 'active' : '' }}">This Year</a>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="small text-muted fw-semibold d-none d-sm-block">
                <i class="bi bi-clock-history me-1"></i> {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
            </div>

            <div class="od-currency-pills">
                @foreach($supportedCurrencies as $curr)
                    <button type="button" class="od-curr-btn {{ $loop->first ? 'active' : '' }}" onclick="switchCurrency('{{ $curr }}', this)">
                        {{ $curr }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main Portfolio KPI Cards --}}
    <div class="row g-3 mb-4">

        {{-- Properties & Occupancy --}}
        <div class="col-md-6 col-xl-3">
            <div class="od-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="od-kpi-icon teal">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <span class="badge bg-teal-subtle text-teal border rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(13,148,136,0.1); color: #0d9488;">
                        {{ $totalProperties }} {{ $totalProperties === 1 ? 'Property' : 'Properties' }}
                    </span>
                </div>

                <div class="od-meter-wrap">
                    <div class="od-progress-ring" style="--pct: {{ min(100, max(0, $occupancyRate)) }};">
                        <span class="od-progress-text">{{ number_format($occupancyRate, 1) }}%</span>
                    </div>
                    <div>
                        <div class="od-kpi-label">Portfolio Occupancy</div>
                        <div class="od-kpi-subtext mt-1">
                            <strong class="text-success">{{ $occupiedUnits }} Occupied</strong> · <span class="text-amber-600 fw-semibold">{{ $vacantUnits }} Vacant</span>
                        </div>
                        <div class="small text-muted mt-0.5">Total {{ $totalUnits }} Units</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expected Rent --}}
        <div class="col-md-6 col-xl-3">
            <div class="od-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="od-kpi-icon blue">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <span class="badge bg-blue-subtle text-blue border rounded-pill px-2 py-0.5 small fw-semibold" style="background: rgba(37,99,235,0.1); color: #2563eb;">
                        Billed Target
                    </span>
                </div>
                <div class="od-kpi-label">Expected Rent</div>
                @foreach($supportedCurrencies as $curr)
                    <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                        <div class="od-kpi-value text-primary">
                            {{ Currency::format($financialByCurrency[$curr]['expected_rent'] ?? 0, $curr) }}
                        </div>
                    </div>
                @endforeach
                <div class="od-kpi-subtext">Total rent invoiced in selected timeframe</div>
            </div>
        </div>

        {{-- Collected Rent --}}
        <div class="col-md-6 col-xl-3">
            <div class="od-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="od-kpi-icon green">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    @foreach($supportedCurrencies as $curr)
                        <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                            <span class="badge bg-success-subtle text-success border rounded-pill px-2.5 py-1 small fw-bold" style="background: rgba(22,163,74,0.1); color: #16a34a;">
                                {{ $financialByCurrency[$curr]['collection_rate'] }}% Rate
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="od-kpi-label">Collected Rent</div>
                @foreach($supportedCurrencies as $curr)
                    <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                        <div class="od-kpi-value text-success">
                            {{ Currency::format($financialByCurrency[$curr]['collected_rent'] ?? 0, $curr) }}
                        </div>
                    </div>
                @endforeach
                <div class="od-kpi-subtext">Actual payments received in timeframe</div>
            </div>
        </div>

        {{-- Outstanding & Overdue Rent --}}
        <div class="col-md-6 col-xl-3">
            <div class="od-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="od-kpi-icon red">
                        <i class="bi bi-exclamation-diamond"></i>
                    </div>
                    @foreach($supportedCurrencies as $curr)
                        <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                            <span class="badge bg-danger-subtle text-danger border rounded-pill px-2 py-0.5 small fw-semibold" style="background: rgba(220,38,38,0.1); color: #dc2626;">
                                {{ $financialByCurrency[$curr]['overdue_count'] }} Overdue
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="od-kpi-label">Outstanding / Overdue</div>
                @foreach($supportedCurrencies as $curr)
                    <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                        <div class="od-kpi-value text-danger">
                            {{ Currency::format($financialByCurrency[$curr]['outstanding_rent'] ?? 0, $curr) }}
                        </div>
                        <div class="od-kpi-subtext mt-1 text-danger fw-semibold">
                            Overdue: {{ Currency::format($financialByCurrency[$curr]['overdue_rent'] ?? 0, $curr) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Financial Breakdown Bar (Expected vs Collected vs Outstanding) --}}
    <div class="od-card mb-4">
        <div class="od-card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-steps text-teal fs-5" style="color: #0d9488;"></i>
                <h6 class="fw-bold mb-0">Financial Status Breakdown (Expected vs Paid vs Outstanding)</h6>
            </div>
            <span class="badge text-bg-light border">Selected Timeframe</span>
        </div>
        <div class="od-card-body">
            @foreach($supportedCurrencies as $curr)
                <div class="curr-val curr-{{ $curr }} {{ !$loop->first ? 'd-none' : '' }}">
                    @php
                        $exp = $financialByCurrency[$curr]['expected_rent'] ?? 0;
                        $col = $financialByCurrency[$curr]['collected_rent'] ?? 0;
                        $out = $financialByCurrency[$curr]['outstanding_rent'] ?? 0;
                        $ovd = $financialByCurrency[$curr]['overdue_rent'] ?? 0;
                        $colPct = $exp > 0 ? min(100, round(($col / $exp) * 100, 1)) : 0;
                        $outPct = $exp > 0 ? min(100 - $colPct, round(($out / $exp) * 100, 1)) : 0;
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-1 small fw-bold">
                        <span>Collection Progress</span>
                        <span>{{ $colPct }}% Collected</span>
                    </div>

                    <div class="od-split-bar">
                        <div class="od-split-segment collected" style="width: {{ $colPct }}%;" title="Collected: {{ Currency::format($col, $curr) }}"></div>
                        <div class="od-split-segment outstanding" style="width: {{ $outPct }}%;" title="Outstanding: {{ Currency::format($out, $curr) }}"></div>
                    </div>

                    <div class="row g-3 text-center mt-2">
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-light-subtle">
                                <div class="small text-muted fw-semibold">EXPECTED</div>
                                <div class="fw-bold text-dark fs-6">{{ Currency::format($exp, $curr) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-success-subtle">
                                <div class="small text-success fw-semibold">COLLECTED (PAID)</div>
                                <div class="fw-bold text-success fs-6">{{ Currency::format($col, $curr) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-warning-subtle">
                                <div class="small text-warning-emphasis fw-semibold">PARTIALLY PAID BALANCE</div>
                                <div class="fw-bold text-warning-emphasis fs-6">{{ Currency::format($financialByCurrency[$curr]['partially_paid_amount'], $curr) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-danger-subtle">
                                <div class="small text-danger fw-semibold">OVERDUE BALANCE</div>
                                <div class="fw-bold text-danger fs-6">{{ Currency::format($ovd, $curr) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row g-3 mb-4">

        {{-- 12-Month Revenue Trend --}}
        <div class="col-lg-7">
            <div class="od-card">
                <div class="od-card-header">
                    <div>
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-graph-up-arrow me-1 text-primary"></i> 12-Month Revenue Trend
                        </h6>
                        <small class="text-muted">Expected Invoiced vs Actual Collected</small>
                    </div>
                </div>
                <div class="od-card-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="ownerRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue Share By Property --}}
        <div class="col-lg-5">
            <div class="od-card">
                <div class="od-card-header">
                    <div>
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-pie-chart me-1 text-teal" style="color: #0d9488;"></i> Revenue by Property
                        </h6>
                        <small class="text-muted">Portfolio income distribution</small>
                    </div>
                </div>
                <div class="od-card-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="ownerPropertyShareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Transactions & Maintenance --}}
    <div class="row g-3 mb-4">

        {{-- Recent Payments --}}
        <div class="col-lg-7">
            <div class="od-card">
                <div class="od-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-success fs-5"></i>
                        <h6 class="fw-bold mb-0">Recent Tenant Payments</h6>
                    </div>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="od-table-wrap">
                    @if($recentPayments->count())
                        <table class="table align-middle od-table">
                            <thead>
                                <tr>
                                    <th>Tenant / Property</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                    @php
                                        $pLease = $payment->rentInvoice->lease ?? null;
                                        $pTenant = $pLease->tenant ?? null;
                                        $pProperty = $pLease->unit->property ?? null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pTenant->full_name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $pProperty->name ?? 'Property' }} (Unit {{ $pLease->unit->unit_number ?? '—' }})</div>
                                        </td>
                                        <td>{{ $payment->payment_date ? Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '—' }}</td>
                                        <td>
                                            <span class="badge text-bg-light border">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?: 'Payment')) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ Currency::format((float) $payment->amount, $payment->currency) }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-sm btn-light border" title="View Receipt">
                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-4 text-center text-muted">No recent payments recorded.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Active Maintenance Requests --}}
        <div class="col-lg-5">
            <div class="od-card">
                <div class="od-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0">Maintenance Requests</h6>
                    </div>
                    <a href="{{ route('maintenance-requests.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="od-table-wrap">
                    @if($recentMaintenanceRequests->count())
                        <table class="table align-middle od-table">
                            <thead>
                                <tr>
                                    <th>Issue / Unit</th>
                                    <th>Priority</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMaintenanceRequests as $mReq)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $mReq->title }}</div>
                                            <div class="small text-muted">{{ $mReq->unit->property->name ?? '' }} · Unit {{ $mReq->unit->unit_number ?? '' }}</div>
                                        </td>
                                        <td>
                                            @if($mReq->priority === 'urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                            @elseif($mReq->priority === 'high')
                                                <span class="badge bg-warning text-dark">High</span>
                                            @else
                                                <span class="badge bg-secondary">Normal</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-info text-dark">
                                                {{ ucfirst(str_replace('_', ' ', $mReq->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-4 text-center text-muted">No maintenance requests reported.</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const revenueData = @json($monthlyRevenueTrend ?? []);
    const propertyShareData = @json($propertyRevenueShare ?? []);
    let activeCurrency = "{{ $supportedCurrencies[0] ?? 'USD' }}";

    let revenueChart = null;
    let propertyShareChart = null;

    // Switch Currency View Function
    window.switchCurrency = function(curr, btn) {
        activeCurrency = curr;

        // Toggle buttons
        document.querySelectorAll('.od-curr-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        // Toggle currency elements
        document.querySelectorAll('.curr-val').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.curr-' + curr).forEach(el => el.classList.remove('d-none'));

        // Re-render charts
        renderRevenueChart(curr);
        renderPropertyShareChart(curr);
    };

    // Render Revenue Chart
    function renderRevenueChart(curr) {
        const canvas = document.getElementById('ownerRevenueChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const data = revenueData[curr] || { labels: [], invoiced: [], collected: [] };

        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Collected Rent (' + curr + ')',
                        data: data.collected,
                        backgroundColor: 'rgba(22, 163, 74, 0.85)',
                        borderColor: '#16a34a',
                        borderWidth: 1,
                        borderRadius: 5
                    },
                    {
                        label: 'Expected Invoiced (' + curr + ')',
                        data: data.invoiced,
                        backgroundColor: 'rgba(37, 99, 235, 0.35)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        type: 'line',
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2})
                        }
                    }
                }
            }
        });
    }

    // Render Property Share Chart
    function renderPropertyShareChart(curr) {
        const canvas = document.getElementById('ownerPropertyShareChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const pData = propertyShareData[curr] || { labels: [], values: [] };

        if (propertyShareChart) propertyShareChart.destroy();

        propertyShareChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: pData.labels,
                datasets: [{
                    data: pData.values,
                    backgroundColor: ['#0d9488', '#2563eb', '#d97706', '#dc2626', '#8b5cf6', '#06b6d4', '#16a34a'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10 } }
                }
            }
        });
    }

    // Initial Chart Render
    renderRevenueChart(activeCurrency);
    renderPropertyShareChart(activeCurrency);
});
</script>
@endpush
