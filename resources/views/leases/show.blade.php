@extends('layouts.app')

@section('title', 'Lease Details')
@section('page-heading', 'Lease Details')

@push('styles')

<style>
    .lease-show {
        --lease-border: #e5e7eb;
        --lease-surface: #ffffff;
        --lease-soft: #f8fafc;
        --lease-muted: #64748b;
        --lease-primary: #2563eb;
    }

    .lease-detail-header {
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

    .lease-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .6rem;
    }

    .lease-status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
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

    .lease-summary-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--lease-border);
        border-radius: 1rem;
        background: var(--lease-surface);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .lease-summary-body {
        display: flex;
        align-items: flex-start;
        gap: .9rem;
        padding: 1.2rem;
    }

    .lease-summary-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        font-size: 1.1rem;
    }

    .lease-summary-icon.blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .lease-summary-icon.cyan {
        background: #cffafe;
        color: #0e7490;
    }

    .lease-summary-icon.green {
        background: #dcfce7;
        color: #15803d;
    }

    .lease-summary-icon.purple {
        background: #ede9fe;
        color: #6d28d9;
    }

    .lease-label {
        margin-bottom: .25rem;
        color: var(--lease-muted);
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .lease-summary-value {
        color: #0f172a;
        font-weight: 800;
        line-height: 1.25;
    }

    .lease-summary-link {
        color: #0f172a;
        text-decoration: none;
        font-weight: 800;
    }

    .lease-summary-link:hover {
        color: var(--lease-primary);
    }

    .lease-card {
        overflow: hidden;
        border: 1px solid var(--lease-border);
        border-radius: 1rem;
        background: var(--lease-surface);
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .lease-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--lease-border);
        background: var(--lease-soft);
    }

    .lease-card-body {
        padding: 1.35rem;
    }

    .lease-detail-item {
        min-height: 72px;
        padding: .9rem;
        border: 1px solid #eef2f7;
        border-radius: .75rem;
        background: #fafafa;
    }

    .lease-detail-label {
        margin-bottom: .3rem;
        color: #64748b;
        font-size: .72rem;
        font-weight: 700;
    }

    .lease-detail-value {
        color: #0f172a;
        font-size: .9rem;
        font-weight: 700;
    }

    .lease-detail-value a {
        color: inherit;
        text-decoration: none;
    }

    .lease-detail-value a:hover {
        color: var(--lease-primary);
    }

    .lease-notes {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        background: #f8fafc;
        color: #475569;
        line-height: 1.65;
        white-space: pre-line;
    }

    .lease-table {
        min-width: 850px;
    }

    .lease-table thead th {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--lease-border);
        background: #f8fafc;
        color: #64748b;
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
    }

    .lease-table tbody td {
        padding: .95rem 1rem;
        border-color: #eef2f7;
        vertical-align: middle;
    }

    .invoice-number {
        font-weight: 800;
        color: #0f172a;
    }

    .invoice-money {
        color: #0f172a;
        font-weight: 700;
        white-space: nowrap;
    }

    .invoice-status {
        display: inline-flex;
        padding: .35rem .6rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-status.paid {
        background: #dcfce7;
        color: #166534;
    }

    .invoice-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .invoice-status.overdue {
        background: #fee2e2;
        color: #991b1b;
    }

    .invoice-status.partially {
        background: #cffafe;
        color: #155e75;
    }

    .invoice-status.other {
        background: #e2e8f0;
        color: #475569;
    }

    .lease-empty {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .lease-empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .9rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.45rem;
    }

    @media (max-width: 767.98px) {
        .lease-detail-header {
            padding: 1.15rem;
        }

        .lease-card-header,
        .lease-card-body {
            padding: 1rem;
        }
    }

    html[data-theme="dark"] .lease-detail-header,
    html[data-theme="dark"] .lease-summary-card,
    html[data-theme="dark"] .lease-card {
        --lease-border: #334155;
        --lease-surface: #111827;
        --lease-soft: #1e293b;
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .lease-detail-header {
        background: linear-gradient(135deg, #111827 0%, #172033 100%);
    }

    html[data-theme="dark"] .lease-card-header,
    html[data-theme="dark"] .lease-table thead th {
        background: #1e293b;
        border-color: #334155;
    }

    html[data-theme="dark"] .lease-summary-value,
    html[data-theme="dark"] .lease-summary-link,
    html[data-theme="dark"] .lease-detail-value,
    html[data-theme="dark"] .invoice-number,
    html[data-theme="dark"] .invoice-money {
        color: #f8fafc;
    }

    html[data-theme="dark"] .lease-detail-item,
    html[data-theme="dark"] .lease-notes {
        border-color: #334155;
        background: #172033;
    }

    html[data-theme="dark"] .lease-detail-value a:hover,
    html[data-theme="dark"] .lease-summary-link:hover {
        color: #93c5fd;
    }

    html[data-theme="dark"] .lease-table tbody td {
        border-color: #263449;
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

    html[data-theme="dark"] .invoice-status.other {
        background: #334155;
        color: #cbd5e1;
    }

    html[data-theme="dark"] .lease-empty-icon {
        background: rgba(37, 99, 235, .15);
        color: #93c5fd;
    }
</style>

@endpush

@section('content')

<div class="container-fluid lease-show px-0">

@php
    use App\Support\Currency;

    $leaseCurrency = strtoupper((string) ($lease->currency ?? ''));

    if (!in_array($leaseCurrency, Currency::SUPPORTED, true)) {
        $leaseCurrency = '';
    }

    $statusClass = match ($lease->status) {
        'active' => 'active',
        'pending' => 'pending',
        'expired' => 'expired',
        'terminated' => 'terminated',
        default => 'terminated',
    };

    $statusLabel = match ($lease->status) {
        'active' => 'Active',
        'pending' => 'Pending',
        'expired' => 'Expired',
        'terminated' => 'Terminated',
        default => ucfirst(str_replace('_', ' ', $lease->status ?? 'Unknown')),
    };

    $tenantName = $lease->tenant?->full_name ?? 'Tenant';

    $invoiceCount = $lease->rentInvoices->count();
@endphp

{{-- Header --}}
<div class="lease-detail-header mb-4">

    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">

        <div>
            <div class="lease-eyebrow">
                <i class="bi bi-file-earmark-text"></i>
                Lease Agreement
            </div>

            <div class="lease-title-row">
                <h1 class="h3 mb-0 fw-bold text-slate-900">
                    Lease #{{ $lease->id }}
                </h1>

                <span class="lease-status {{ $statusClass }}">
                    <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                    {{ $statusLabel }}
                </span>
            </div>

            <p class="text-muted mb-0 mt-2">
                {{ $tenantName }}
                <span class="mx-1">&bull;</span>
                Unit {{ $lease->unit?->unit_number ?? 'N/A' }}
                <span class="mx-1">&bull;</span>
                {{ $lease->unit?->property?->name ?? 'Property' }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            @if($lease->status === 'active')
                <form
                    method="POST"
                    action="{{ route('leases.checkout', $lease) }}"
                    class="d-inline"
                    onsubmit="return confirm('Are you sure you want to check out this tenant? The unit will become vacant and tenant portal access will be deactivated.')"
                >
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-outline-danger"
                    >
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Check Out Tenant
                    </button>
                </form>
            @endif

            <a
                href="{{ route('leases.edit', $lease) }}"
                class="btn pm-btn-outline-warning"
            >
                <i class="bi bi-pencil-square me-1"></i>
                Edit Lease
            </a>

            <a
                href="{{ route('leases.index') }}"
                class="btn pm-btn-outline"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Leases
            </a>

        </div>

    </div>

</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

    {{-- Tenant --}}
    <div class="col-md-6 col-xl-3">
        <div class="lease-summary-card">

            <div class="lease-summary-body">

                <span class="lease-summary-icon blue">
                    <i class="bi bi-person"></i>
                </span>

                <div class="min-w-0">
                    <div class="lease-label">Tenant</div>

                    @if($lease->tenant)

                        <a
                            href="{{ route('tenants.show', $lease->tenant) }}"
                            class="lease-summary-link d-block text-truncate"
                        >
                            {{ $lease->tenant->full_name }}
                        </a>

                        <div class="text-muted small text-truncate">
                            {{ $lease->tenant->phone ?? $lease->tenant->email ?? 'Contact not provided' }}
                        </div>

                    @else

                        <div class="lease-summary-value text-muted">
                            Unassigned
                        </div>

                    @endif
                </div>

            </div>

        </div>
    </div>

    {{-- Unit --}}
    <div class="col-md-6 col-xl-3">
        <div class="lease-summary-card">

            <div class="lease-summary-body">

                <span class="lease-summary-icon cyan">
                    <i class="bi bi-door-open"></i>
                </span>

                <div class="min-w-0">
                    <div class="lease-label">Unit & Property</div>

                    @if($lease->unit)

                        <a
                            href="{{ route('units.show', $lease->unit) }}"
                            class="lease-summary-link d-block text-truncate"
                        >
                            Unit {{ $lease->unit->unit_number }}
                        </a>

                        <div class="text-muted small text-truncate">
                            {{ $lease->unit->property?->name ?? 'Property not assigned' }}
                        </div>

                    @else

                        <div class="lease-summary-value text-muted">
                            Unassigned
                        </div>

                    @endif
                </div>

            </div>

        </div>
    </div>

    {{-- Monthly Rent --}}
    <div class="col-md-6 col-xl-3">
        <div class="lease-summary-card">

            <div class="lease-summary-body">

                <span class="lease-summary-icon green">
                    <i class="bi bi-cash-stack"></i>
                </span>

                <div class="min-w-0">
                    <div class="lease-label">Monthly Rent</div>

                    @if($leaseCurrency)

                        <div class="lease-summary-value">
                            {{ Currency::format((float) $lease->monthly_rent, $leaseCurrency) }}
                        </div>

                        <div class="text-muted small">
                            Due on day {{ $lease->payment_due_day }}
                        </div>

                    @else

                        <div class="lease-summary-value text-muted">
                            Currency not set
                        </div>

                    @endif
                </div>

            </div>

        </div>
    </div>

    {{-- Security Deposit --}}
    <div class="col-md-6 col-xl-3">
        <div class="lease-summary-card">

            <div class="lease-summary-body">

                <span class="lease-summary-icon purple">
                    <i class="bi bi-shield-check"></i>
                </span>

                <div class="min-w-0">
                    <div class="lease-label">Security Deposit</div>

                    @if($leaseCurrency)

                        <div class="lease-summary-value">
                            {{ Currency::format((float) $lease->security_deposit, $leaseCurrency) }}
                        </div>

                        <div class="text-muted small">
                            Held on file
                        </div>

                    @else

                        <div class="lease-summary-value text-muted">
                            Currency not set
                        </div>

                    @endif
                </div>

            </div>

        </div>
    </div>

</div>

{{-- Agreement Details --}}
<div class="lease-card mb-4">

    <div class="lease-card-header">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                Agreement Details
            </h5>

            <div class="text-muted small">
                Lease terms, payment schedule, currency, and contract information.
            </div>
        </div>
    </div>

    <div class="lease-card-body">

        <div class="row g-3">

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Tenant Name</div>

                    <div class="lease-detail-value">
                        @if($lease->tenant)
                            <a href="{{ route('tenants.show', $lease->tenant) }}">
                                {{ $lease->tenant->full_name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Property Name</div>

                    <div class="lease-detail-value">
                        @if($lease->unit?->property)
                            <a href="{{ route('properties.show', $lease->unit->property) }}">
                                {{ $lease->unit->property->name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Unit Number</div>

                    <div class="lease-detail-value">
                        @if($lease->unit)
                            <a href="{{ route('units.show', $lease->unit) }}">
                                Unit {{ $lease->unit->unit_number }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Start Date</div>

                    <div class="lease-detail-value">
                        {{ $lease->start_date?->format('d M Y') ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">End Date</div>

                    <div class="lease-detail-value">
                        {{ $lease->end_date?->format('d M Y') ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Payment Due Day</div>

                    <div class="lease-detail-value">
                        Day {{ $lease->payment_due_day }} of each month
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Monthly Rent</div>

                    <div class="lease-detail-value">
                        @if($leaseCurrency)
                            {{ Currency::format((float) $lease->monthly_rent, $leaseCurrency) }}
                        @else
                            <span class="text-muted">Currency not set</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Currency</div>

                    <div class="lease-detail-value">
                        @if($leaseCurrency)
                            {{ $leaseCurrency }}
                            <span class="text-muted fw-normal">
                                — {{ Currency::name($leaseCurrency) }}
                            </span>
                        @else
                            <span class="text-danger">Not set</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Security Deposit</div>

                    <div class="lease-detail-value">
                        @if($leaseCurrency)
                            {{ Currency::format((float) $lease->security_deposit, $leaseCurrency) }}
                        @else
                            <span class="text-muted">Currency not set</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Contract Status</div>

                    <div class="lease-detail-value">
                        <span class="lease-status {{ $statusClass }}">
                            <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="lease-detail-item">
                    <div class="lease-detail-label">Additional Notes</div>

                    <div class="lease-notes">
                        {{ $lease->notes ?: 'No additional notes provided for this lease agreement.' }}
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Rent Invoices --}}
<div class="lease-card mb-4">

    <div class="lease-card-header">

        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-receipt me-2 text-primary"></i>
                Rent Invoices
            </h5>

            <div class="text-muted small">
                Invoices generated under this lease agreement.
            </div>
        </div>

        <span class="pm-tag pm-tag-navy">
            {{ $invoiceCount }}
            {{ $invoiceCount === 1 ? 'Invoice' : 'Invoices' }}
        </span>

    </div>

    <div class="p-0">

        @if($invoiceCount)

            <div class="table-responsive">

                <table class="table lease-table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($lease->rentInvoices as $invoice)

                            @php
                                /*
                                 * Invoice currency is historical data.
                                 * Never rewrite it from the current lease currency.
                                 */
                                $invoiceCurrency = strtoupper(
                                    (string) ($invoice->currency ?? '')
                                );

                                if (!in_array($invoiceCurrency, Currency::SUPPORTED, true)) {
                                    $invoiceCurrency = '';
                                }

                                $invoiceStatus = $invoice->status;

                                $invoiceStatusClass = match ($invoiceStatus) {
                                    'paid' => 'paid',
                                    'pending' => 'pending',
                                    'overdue' => 'overdue',
                                    'partially_paid' => 'partially',
                                    default => 'other',
                                };

                                $invoiceStatusLabel = match ($invoiceStatus) {
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                    'overdue' => 'Overdue',
                                    'partially_paid' => 'Partially Paid',
                                    default => ucfirst(str_replace('_', ' ', $invoiceStatus ?? 'Unknown')),
                                };
                            @endphp

                            <tr>

                                <td>
                                    <div class="invoice-number">
                                        #{{ $invoice->invoice_number }}
                                    </div>
                                </td>

                                <td>
                                    {{ $invoice->invoice_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    @if($invoiceCurrency)
                                        <span class="invoice-money">
                                            {{ Currency::format((float) $invoice->amount, $invoiceCurrency) }}
                                        </span>
                                    @else
                                        <span class="text-danger small">
                                            Currency not set
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($invoiceCurrency)
                                        <span class="invoice-money">
                                            {{ Currency::format((float) $invoice->amount_paid, $invoiceCurrency) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="invoice-status {{ $invoiceStatusClass }}">
                                        {{ $invoiceStatusLabel }}
                                    </span>
                                </td>

                                <td class="text-end">

                                    <a
                                        href="{{ route('rent-invoices.show', $invoice) }}"
                                        class="btn btn-sm pm-btn-outline"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="lease-empty">

                <div class="lease-empty-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <h6 class="fw-bold text-slate-900 mb-1">
                    No Rent Invoices
                </h6>

                <p class="text-muted mb-0">
                    This lease does not have any rent invoices recorded yet.
                </p>

            </div>

        @endif

    </div>
</div>

</div>
@endsection
