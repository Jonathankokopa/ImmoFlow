@extends('layouts.app')

@section('title', 'Payment Details #' . $payment->id)
@section('page-heading', 'Payment Details')

@section('content')

<style>
    .receipt-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .receipt-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 24px 30px;
    }

    .status-badge-lg {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .pro-table-show th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background-color: #f8fafc;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .pro-table-show td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .receipt-card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="container-fluid px-4 py-3">

    @php
        use App\Models\Payment;
        use App\Support\Currency;

        /*
         * Currency hierarchy:
         * Payment -> Invoice -> Lease
         *
         * Payments should normally already contain the invoice currency.
         * The fallback keeps this view safe for older records.
         */
        $invoice = $payment->rentInvoice;

        $paymentCurrency = strtoupper((string) (
            $payment->currency
            ?? $invoice?->currency
            ?? $invoice?->lease?->currency
            ?? 'USD'
        ));

        if (!in_array($paymentCurrency, Currency::SUPPORTED, true)) {
            $paymentCurrency = strtoupper((string) (
                $invoice?->currency
                ?? $invoice?->lease?->currency
                ?? 'USD'
            ));
        }

        $paymentAmount = round((float) $payment->amount, 2);
        $paymentIsProvider = $payment->provider !== null;

        /*
         * Only completed allocations count toward the payment's
         * allocated amount.
         */
        $allocations = $payment->allocations
            ->filter(function ($allocation) {
                return $allocation->payment
                    && $allocation->payment->status === Payment::STATUS_COMPLETED;
            })
            ->values();

        /*
         * Total allocated funds for this payment.
         *
         * Since the payment belongs to one currency, allocations should
         * also belong to that same currency. Only matching currencies are
         * included in this payment-level total.
         */
        $totalAllocated = round(
            (float) $allocations
                ->filter(function ($allocation) use ($paymentCurrency) {
                    $invoiceCurrency = strtoupper((string) (
                        $allocation->rentInvoice?->currency
                        ?? $allocation->payment?->currency
                        ?? $paymentCurrency
                    ));

                    return $invoiceCurrency === $paymentCurrency;
                })
                ->sum('amount'),
            2
        );

        $unallocatedAmount = round(
            max(0, $paymentAmount - $totalAllocated),
            2
        );

        $statusLabel = match ($payment->status) {
            Payment::STATUS_PENDING => 'Pending Clearance',
            Payment::STATUS_COMPLETED => 'Payment Verified & Completed',
            Payment::STATUS_FAILED => 'Failed Transaction',
            Payment::STATUS_CANCELLED => 'Cancelled',
            Payment::STATUS_REFUNDED => 'Refunded',
            default => ucwords(
                str_replace('_', ' ', $payment->status)
            ),
        };

        $statusBg = match ($payment->status) {
            Payment::STATUS_COMPLETED => 'bg-success text-white',
            Payment::STATUS_PENDING => 'bg-warning text-dark',
            Payment::STATUS_FAILED => 'bg-danger text-white',
            Payment::STATUS_CANCELLED => 'bg-secondary text-white',
            Payment::STATUS_REFUNDED => 'bg-info text-dark',
            default => 'bg-secondary text-white',
        };

        $invoiceStatusLabel = match ($invoice?->status) {
            'paid' => 'Paid',
            'partially_paid' => 'Partially Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => 'Unpaid',
        };

        $invoiceStatusClass = match ($invoice?->status) {
            'paid' => 'bg-success-subtle text-success border border-success-subtle',
            'partially_paid' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'overdue' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'cancelled' => 'bg-secondary-subtle text-secondary border',
            default => 'bg-light text-dark border',
        };
    @endphp

    {{-- Top Action & Header Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">

        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">
                Payment Record #{{ $payment->id }}
            </h1>

            <p class="text-muted small mb-0">
                Recorded on
                {{ $payment->payment_date?->format('F d, Y \a\t H:i') ?? 'N/A' }}
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            @if(Route::has('payments.receipt'))
                <a
                    href="{{ route('payments.receipt', $payment) }}"
                    target="_blank"
                    class="btn btn-outline-success rounded-3 px-3"
                >
                    <i class="bi bi-receipt-cutoff me-1"></i>
                    Official Receipt
                </a>
            @endif

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-outline-secondary rounded-3 px-3"
            >
                <i class="bi bi-printer me-1"></i>
                Print Voucher
            </button>

            @if (!$paymentIsProvider)
                <a
                    href="{{ route('payments.edit', $payment) }}"
                    class="btn btn-warning rounded-3 px-3 fw-medium"
                >
                    <i class="bi bi-pencil me-1"></i>
                    Edit Entry
                </a>
            @endif

            <a
                href="{{ route('payments.index') }}"
                class="btn btn-light border rounded-3 px-3 text-muted"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>
    </div>

    {{-- Main Receipt Card --}}
    <div class="receipt-card mb-4">

        {{-- Header Banner --}}
        <div class="receipt-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

            <div>
                <span
                    class="text-uppercase fw-bold mb-1 d-block"
                    style="letter-spacing: 0.1em; font-size: 0.75rem; color: #14b8a6;"
                >
                    Digital Payment Voucher
                </span>

                <h2 class="display-6 fw-bold mb-0 text-white">
                    {{ Currency::format($paymentAmount, $paymentCurrency) }}
                </h2>

                <span class="text-white-50 small">
                    {{ $paymentCurrency }}
                    ({{ Currency::name($paymentCurrency) }})
                </span>
            </div>

            <div class="text-md-end">

                <div class="status-badge-lg {{ $statusBg }} shadow-sm">
                    <i class="bi bi-shield-check"></i>
                    {{ $statusLabel }}
                </div>

                <div class="text-white-50 small mt-2">
                    Tx Ref:
                    <strong>
                        {{ $payment->transaction_reference ?? 'N/A' }}
                    </strong>
                </div>

            </div>
        </div>

        {{-- Details Body --}}
        <div class="card-body p-4">

            <div class="row g-4">

                {{-- Payment Metadata --}}
                <div class="col-md-6 border-end-md">

                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Transaction Details
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-borderless table-sm align-middle mb-0">

                            <tbody>

                                <tr>
                                    <td class="text-muted" style="width: 140px;">
                                        Payment Method
                                    </td>

                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-credit-card me-1 text-primary"></i>

                                        {{ $payment->payment_method
                                            ? ucwords(str_replace('_', ' ', $payment->payment_method))
                                            : 'Manual Cash' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">
                                        Payment Provider
                                    </td>

                                    <td>
                                        @if ($payment->provider)

                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                {{ ucwords(str_replace('_', ' ', $payment->provider)) }}
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                Direct / Manual Entry
                                            </span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">
                                        Transaction Date
                                    </td>

                                    <td class="fw-semibold text-dark">
                                        {{ $payment->payment_date?->format('d M Y, H:i') ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">
                                        Currency
                                    </td>

                                    <td class="fw-semibold text-dark">
                                        {{ $paymentCurrency }}
                                        <span class="text-muted fw-normal">
                                            — {{ Currency::name($paymentCurrency) }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">
                                        Recorded By
                                    </td>

                                    <td class="fw-medium text-dark">
                                        {{ $payment->recordedBy?->name ?? 'System Administrator' }}
                                    </td>
                                </tr>

                                @if($payment->notes)
                                    <tr>
                                        <td class="text-muted">
                                            Audit Notes
                                        </td>

                                        <td class="text-dark bg-light p-2 rounded">
                                            {{ $payment->notes }}
                                        </td>
                                    </tr>
                                @endif

                            </tbody>

                        </table>

                    </div>

                    {{-- Financial Breakdown Box --}}
                    <div class="p-3 rounded-3 mt-4 border bg-light">

                        <div class="d-flex justify-content-between mb-2">

                            <span class="text-muted small">
                                Total Paid Amount:
                            </span>

                            <strong class="text-dark">
                                {{ Currency::format($paymentAmount, $paymentCurrency) }}
                            </strong>

                        </div>

                        <div class="d-flex justify-content-between mb-2">

                            <span class="text-muted small">
                                Allocated to Invoices:
                            </span>

                            <strong class="text-success">
                                +{{ Currency::format($totalAllocated, $paymentCurrency) }}
                            </strong>

                        </div>

                        <div class="d-flex justify-content-between pt-2 border-top">

                            <span class="fw-bold text-dark small">
                                Unallocated Balance:
                            </span>

                            <strong class="{{ $unallocatedAmount > 0 ? 'text-warning' : 'text-success' }}">
                                {{ Currency::format($unallocatedAmount, $paymentCurrency) }}
                            </strong>

                        </div>

                    </div>

                </div>

                {{-- Target Invoice / Tenant Info --}}
                <div class="col-md-6">

                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-building me-2" style="color: #0d9488;"></i>
                        Associated Property & Tenant
                    </h5>

                    @if ($invoice)

                        <div class="p-3 border rounded-3 bg-white mb-3">

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="badge bg-light text-muted border">
                                    Starting Invoice
                                </span>

                                <span class="badge {{ $invoiceStatusClass }}">
                                    {{ $invoiceStatusLabel }}
                                </span>

                            </div>

                            <div class="fs-5 fw-bold text-dark mb-1">

                                <a
                                    href="{{ route('rent-invoices.show', $invoice) }}"
                                    class="text-decoration-none text-dark hover-primary"
                                >
                                    {{ $invoice->invoice_number }}
                                </a>

                            </div>

                            <div class="text-muted small">
                                Period:
                                <strong>
                                    {{ $invoice->formatted_billing_period }}
                                </strong>
                            </div>

                            <div class="text-muted small mt-1">
                                Invoice Amount:
                                <strong class="text-dark">
                                    {{ Currency::format(
                                        (float) $invoice->amount,
                                        $invoice->currency
                                    ) }}
                                </strong>
                            </div>

                        </div>

                        @if ($invoice->lease?->tenant)

                            @php
                                $tenantName =
                                    $invoice->lease->tenant->full_name
                                    ?? $invoice->lease->tenant->name
                                    ?? 'Tenant';
                            @endphp

                            <div class="d-flex align-items-center gap-3 p-3 border rounded-3 bg-light mb-3">

                                <div
                                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                    style="width: 44px; height: 44px; font-size: 1.1rem;"
                                >
                                    {{ strtoupper(substr($tenantName, 0, 2)) }}
                                </div>

                                <div>

                                    <div class="text-muted small">
                                        Tenant Name
                                    </div>

                                    <a
                                        href="{{ route('tenants.show', $invoice->lease->tenant) }}"
                                        class="fw-bold text-dark text-decoration-none hover-primary"
                                    >
                                        {{ $tenantName }}
                                    </a>

                                </div>

                            </div>

                        @endif

                        @if ($invoice->lease?->unit?->property)

                            <div class="row g-2">

                                <div class="col-6">

                                    <div class="p-2 border rounded bg-light text-center">

                                        <div class="text-muted small">
                                            Property
                                        </div>

                                        <a
                                            href="{{ route('properties.show', $invoice->lease->unit->property) }}"
                                            class="fw-semibold text-dark text-decoration-none"
                                        >
                                            {{ $invoice->lease->unit->property->name }}
                                        </a>

                                    </div>

                                </div>

                                <div class="col-6">

                                    <div class="p-2 border rounded bg-light text-center">

                                        <div class="text-muted small">
                                            Unit
                                        </div>

                                        <a
                                            href="{{ route('units.show', $invoice->lease->unit) }}"
                                            class="fw-semibold text-dark text-decoration-none"
                                        >
                                            Unit {{ $invoice->lease->unit->unit_number }}
                                        </a>

                                    </div>

                                </div>

                            </div>

                        @endif

                    @else

                        <div class="alert alert-warning border-0 rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Starting invoice information is missing or detached.
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- Invoice Allocation Breakdown --}}
    <div class="receipt-card">

        <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">

            <h5 class="mb-0 fw-bold text-dark fs-6">
                <i class="bi bi-list-check me-2 text-primary"></i>
                Invoice Allocation Breakdown
            </h5>

            <span class="badge bg-primary rounded-pill px-3 py-1">
                {{ $allocations->count() }}
                Invoice Allocation(s)
            </span>

        </div>

        <div class="table-responsive">

            @if ($allocations->isNotEmpty())

                <table class="table pro-table-show align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Billing Period</th>
                            <th>Tenant</th>
                            <th>Invoice Total</th>
                            <th>Amount Allocated</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($allocations as $allocation)

                            @php
                                $allocatedInvoice = $allocation->rentInvoice;

                                $allocatedInvoiceCurrency = strtoupper((string) (
                                    $allocatedInvoice?->currency
                                    ?? $allocation->payment?->currency
                                    ?? $paymentCurrency
                                ));

                                if (!in_array(
                                    $allocatedInvoiceCurrency,
                                    Currency::SUPPORTED,
                                    true
                                )) {
                                    $allocatedInvoiceCurrency = $paymentCurrency;
                                }

                                $allocatedAmount = round(
                                    (float) $allocation->amount,
                                    2
                                );

                                $allocatedInvoiceAmount = round(
                                    (float) ($allocatedInvoice?->amount ?? 0),
                                    2
                                );

                                $allocatedInvoiceBalance = round(
                                    (float) ($allocatedInvoice?->outstanding_amount ?? 0),
                                    2
                                );

                                $allocatedStatusLabel = match ($allocatedInvoice?->status) {
                                    'paid' => 'Paid',
                                    'partially_paid' => 'Partially Paid',
                                    'overdue' => 'Overdue',
                                    'cancelled' => 'Cancelled',
                                    default => 'Unpaid',
                                };

                                $allocatedStatusClass = match ($allocatedInvoice?->status) {
                                    'paid' => 'bg-success-subtle text-success border border-success-subtle',
                                    'partially_paid' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'overdue' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    'cancelled' => 'bg-secondary-subtle text-secondary border',
                                    default => 'bg-light text-dark border',
                                };
                            @endphp

                            <tr>

                                <td>
                                    @if($allocatedInvoice)

                                        <a
                                            href="{{ route('rent-invoices.show', $allocatedInvoice) }}"
                                            class="fw-bold text-primary text-decoration-none"
                                        >
                                            {{ $allocatedInvoice->invoice_number }}
                                        </a>

                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    {{ $allocatedInvoice?->formatted_billing_period ?? '—' }}
                                </td>

                                <td>
                                    {{ $allocatedInvoice?->lease?->tenant?->full_name ?? '—' }}
                                </td>

                                <td class="fw-semibold">
                                    {{ Currency::format(
                                        $allocatedInvoiceAmount,
                                        $allocatedInvoiceCurrency
                                    ) }}
                                </td>

                                <td>

                                    <span class="fw-bold text-success">
                                        +{{ Currency::format(
                                            $allocatedAmount,
                                            $allocatedInvoiceCurrency
                                        ) }}
                                    </span>

                                </td>

                                <td>
                                    {{ Currency::format(
                                        $allocatedInvoiceBalance,
                                        $allocatedInvoiceCurrency
                                    ) }}
                                </td>

                                <td>
                                    <span class="badge {{ $allocatedStatusClass }}">
                                        {{ $allocatedStatusLabel }}
                                    </span>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                    <tfoot class="table-light border-top">

                        <tr>

                            <td
                                colspan="4"
                                class="text-end fw-bold text-muted"
                            >
                                Total Funds Applied:
                            </td>

                            <td class="fw-bold text-success fs-6">
                                +{{ Currency::format(
                                    $totalAllocated,
                                    $paymentCurrency
                                ) }}
                            </td>

                            <td colspan="2"></td>

                        </tr>

                    </tfoot>

                </table>

            @else

                <div class="p-4 text-center text-muted">

                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-warning"></i>

                    No invoice allocations created for this payment.

                </div>

            @endif

        </div>

    </div>

</div>

@endsection