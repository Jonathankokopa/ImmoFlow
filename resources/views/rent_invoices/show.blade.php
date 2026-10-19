@extends('layouts.app')

@section('title', 'Rent Invoice #' . $rentInvoice->invoice_number)
@section('page-heading', 'Invoice Details')

@section('content')

@php
    use App\Support\Currency;

    $invoiceAmount = (float) ($rentInvoice->amount ?? 0);
    $calculatedPaid = (float) ($rentInvoice->total_paid);
    $calculatedBalance = (float) ($rentInvoice->outstanding_amount);
    $paymentCount = $rentInvoice->payments->count();

    $status = $rentInvoice->status ?? 'unpaid';

    /*
     * Currency is inherited from the lease when the invoice
     * is created. Keep the invoice currency as the source
     * of truth for displaying all invoice financial values.
     */
    $currency = strtoupper(
        (string) ($rentInvoice->currency ?? $rentInvoice->lease?->currency ?? 'USD')
    );

    $currencySymbol = Currency::symbol($currency);
    $currencyName = Currency::name($currency);

    $statusLabel = match ($status) {
        'paid' => 'Paid In Full',
        'partially_paid' => 'Partially Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
        default => 'Unpaid Statement',
    };

    $statusBg = match ($status) {
        'paid' => 'bg-success text-white',
        'partially_paid' => 'bg-warning text-dark',
        'overdue' => 'bg-danger text-white',
        'cancelled' => 'bg-secondary text-white',
        default => 'bg-dark text-white',
    };

    $tenant = $rentInvoice->lease?->tenant;
    $unit = $rentInvoice->lease?->unit;
    $property = $unit?->property;
@endphp

<style>
    .invoice-paper-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .invoice-banner {
        background: linear-gradient(
            135deg,
            #0f172a 0%,
            #1e293b 100%
        );
        color: #ffffff;
        padding: 28px 32px;
    }

    .billing-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .pro-inv-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background-color: #f8fafc;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .pro-inv-table td {
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

        .invoice-paper-card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="container-fluid px-4 py-3">

    {{-- Header Action Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">

        <div>

            <h1
                class="h3 fw-bold mb-1 text-dark"
                style="letter-spacing: -0.02em;"
            >
                Rent Invoice Statement
            </h1>

            <p class="text-muted small mb-0">
                Invoice Reference #{{ $rentInvoice->invoice_number }}
                &bull;
                Billing Period:
                {{ $rentInvoice->formatted_billing_period }}
            </p>

        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-outline-secondary rounded-3 px-3"
            >
                <i class="bi bi-printer me-1"></i>
                Print Invoice
            </button>

            @if($rentInvoice->lease?->tenant?->email)
                <form action="{{ route('rent-invoices.send-email', $rentInvoice) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary rounded-3 px-3">
                        <i class="bi bi-envelope me-1"></i>
                        Send Email
                    </button>
                </form>
            @endif

            @if($status !== 'paid' && $status !== 'cancelled')

                <a
                    href="{{ route('payments.create', ['rent_invoice_id' => $rentInvoice->id]) }}"
                    class="btn btn-success rounded-3 px-3 shadow-sm"
                >
                    <i class="bi bi-cash-card me-1"></i>
                    Record Payment
                </a>

            @endif

            @if($status !== 'cancelled')

                <a
                    href="{{ route('rent-invoices.edit', $rentInvoice) }}"
                    class="btn btn-warning rounded-3 px-3 fw-medium"
                >
                    <i class="bi bi-pencil me-1"></i>
                    Edit Statement
                </a>

            @endif

            <a
                href="{{ route('rent-invoices.index') }}"
                class="btn btn-light border rounded-3 px-3 text-muted"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>


    {{-- Flash Alerts --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">

            <i class="bi bi-check-circle-fill me-2 text-success"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">

            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>

        </div>

    @endif


    {{-- Official Invoice Paper Card --}}
    <div class="invoice-paper-card mb-4">

        {{-- Banner Header --}}
        <div class="invoice-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

            <div>

                <span
                    class="text-uppercase fw-bold text-teal mb-1 d-block"
                    style="
                        letter-spacing: 0.1em;
                        font-size: 0.75rem;
                        color: #14b8a6;
                    "
                >
                    Official Rental Statement
                </span>

                <h2 class="display-6 fw-bold mb-0 text-white">
                    {{ $rentInvoice->invoice_number }}
                </h2>

                <div class="text-white-50 small mt-1">

                    Issued:
                    <strong>
                        {{ $rentInvoice->invoice_date?->format('d M Y') ?? 'N/A' }}
                    </strong>

                    &bull;

                    Due Date:
                    <strong>
                        {{ $rentInvoice->due_date?->format('d M Y') ?? 'N/A' }}
                    </strong>

                </div>

            </div>


            <div class="text-md-end">

                <span
                    class="badge {{ $statusBg }} px-3 py-2 rounded-pill fs-6 shadow-sm mb-2 d-inline-block"
                >
                    <i class="bi bi-shield-check me-1"></i>
                    {{ $statusLabel }}
                </span>

                <div class="text-white fs-4 fw-bold">
                    Balance:
                    {{ $currencySymbol }}
                    {{ number_format($calculatedBalance, 2) }}
                    <span class="fs-6">
                        {{ $currency }}
                    </span>
                </div>

            </div>

        </div>


        {{-- Invoice Body Content --}}
        <div class="card-body p-4">

            {{-- Billing & Property Info --}}
            <div class="row g-4 mb-4">

                {{-- Billed To Tenant --}}
                <div class="col-md-6">

                    <div class="billing-box h-100">

                        <small
                            class="text-muted text-uppercase fw-bold d-block mb-2"
                            style="
                                font-size: 0.7rem;
                                letter-spacing: 0.05em;
                            "
                        >
                            Billed To Tenant
                        </small>

                        @if($tenant)

                            <div class="d-flex align-items-center gap-3">

                                <div
                                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                    style="
                                        width: 44px;
                                        height: 44px;
                                        font-size: 1.1rem;
                                    "
                                >
                                    {{ strtoupper(
                                        substr(
                                            $tenant->full_name ?? $tenant->name ?? 'T',
                                            0,
                                            2
                                        )
                                    ) }}
                                </div>

                                <div>

                                    <h5 class="fw-bold text-dark mb-0">

                                        <a
                                            href="{{ route('tenants.show', $tenant) }}"
                                            class="text-decoration-none text-dark hover-primary"
                                        >
                                            {{ $tenant->full_name ?? $tenant->name }}
                                        </a>

                                    </h5>

                                    <div class="text-muted small">

                                        <i class="bi bi-telephone me-1"></i>

                                        {{ $tenant->phone ?? 'No phone listed' }}

                                    </div>

                                    <div class="text-muted small">

                                        <i class="bi bi-envelope me-1"></i>

                                        {{ $tenant->email ?? 'No email listed' }}

                                    </div>

                                </div>

                            </div>

                        @else

                            <span class="text-muted">
                                Unassigned Tenant
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Property & Unit Details --}}
                <div class="col-md-6">

                    <div class="billing-box h-100">

                        <small
                            class="text-muted text-uppercase fw-bold d-block mb-2"
                            style="
                                font-size: 0.7rem;
                                letter-spacing: 0.05em;
                            "
                        >
                            Property & Unit Details
                        </small>

                        @if($property)

                            <h5 class="fw-bold text-dark mb-1">

                                <a
                                    href="{{ route('properties.show', $property) }}"
                                    class="text-decoration-none text-dark hover-primary"
                                >
                                    {{ $property->name }}
                                </a>

                            </h5>

                            <div class="text-muted small mb-2">

                                <i class="bi bi-geo-alt me-1"></i>

                                {{ $property->address ?? 'Location' }}

                            </div>

                            @if($unit)

                                <span class="badge bg-dark text-white px-2 py-1">
                                    Unit {{ $unit->unit_number }}
                                </span>

                            @endif

                        @else

                            <span class="text-muted">
                                Unassigned Property
                            </span>

                        @endif

                    </div>

                </div>

            </div>


            {{-- Currency Information --}}
            <div class="alert alert-light border rounded-3 mb-4">

                <div class="d-flex align-items-start gap-3">

                    <div class="text-primary fs-4">
                        <i class="bi bi-currency-exchange"></i>
                    </div>

                    <div>

                        <div class="fw-bold text-dark">
                            Invoice Currency:
                            {{ $currencySymbol }}
                            {{ $currency }}
                        </div>

                        <div class="text-muted small">
                            {{ $currencyName }}
                        </div>

                        <div class="text-muted small mt-1">
                            All amounts on this invoice and its payment
                            records are recorded in {{ $currency }}.
                            No automatic currency conversion is performed.
                        </div>

                    </div>

                </div>

            </div>


            {{-- Invoice Line Items & Financial Summary --}}
            <div class="border rounded-3 overflow-hidden mb-4">

                <div class="table-responsive">

                    <table class="table pro-inv-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Item Description
                                </th>

                                <th>
                                    Billing Period
                                </th>

                                <th class="text-end">
                                    Base Rent
                                </th>

                                <th class="text-end">
                                    Total Charge
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>

                                    <strong class="text-dark d-block">
                                        Monthly Rent Charge
                                    </strong>

                                    <span class="text-muted small">
                                        Standard lease agreement rent fee
                                    </span>

                                </td>

                                <td>
                                    {{ $rentInvoice->formatted_billing_period }}
                                </td>

                                <td class="text-end fw-semibold">

                                    {{ $currencySymbol }}
                                    {{ number_format($invoiceAmount, 2) }}

                                    <span class="small text-muted">
                                        {{ $currency }}
                                    </span>

                                </td>

                                <td class="text-end fw-bold text-dark">

                                    {{ $currencySymbol }}
                                    {{ number_format($invoiceAmount, 2) }}

                                    <span class="small text-muted">
                                        {{ $currency }}
                                    </span>

                                </td>

                            </tr>

                        </tbody>


                        <tfoot class="table-light border-top">

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-end text-muted fw-bold"
                                >
                                    Total Invoiced Amount:
                                </td>

                                <td class="text-end fw-bold text-dark fs-6">

                                    {{ $currencySymbol }}
                                    {{ number_format($invoiceAmount, 2) }}

                                    <span class="small">
                                        {{ $currency }}
                                    </span>

                                </td>

                            </tr>


                            <tr>

                                <td
                                    colspan="3"
                                    class="text-end text-muted fw-bold"
                                >
                                    Total Paid to Date:
                                </td>

                                <td class="text-end fw-bold text-success fs-6">

                                    +{{ $currencySymbol }}
                                    {{ number_format($calculatedPaid, 2) }}

                                    <span class="small">
                                        {{ $currency }}
                                    </span>

                                </td>

                            </tr>


                            <tr class="table-active">

                                <td
                                    colspan="3"
                                    class="text-end fw-bold text-dark fs-6"
                                >
                                    Remaining Outstanding Balance:
                                </td>

                                <td
                                    class="text-end fw-bold {{ $calculatedBalance > 0 ? 'text-danger' : 'text-success' }} fs-5"
                                >

                                    {{ $currencySymbol }}
                                    {{ number_format($calculatedBalance, 2) }}

                                    <span class="small">
                                        {{ $currency }}
                                    </span>

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>


            {{-- Payments Audit Trail --}}
            <div class="border rounded-3 overflow-hidden">

                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">

                    <h5 class="mb-0 fw-bold text-dark fs-6">

                        <i class="bi bi-clock-history me-2 text-primary"></i>

                        Payment Transactions History

                    </h5>

                    <span class="badge bg-primary rounded-pill px-3 py-1">
                        {{ $paymentCount }}
                        Payment Log(s)
                    </span>

                </div>


                <div class="table-responsive">

                    @if($rentInvoice->payments->count())

                        <table class="table pro-inv-table align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Method
                                    </th>

                                    <th>
                                        Provider
                                    </th>

                                    <th>
                                        Tx Reference
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th class="text-end">
                                        Amount Paid
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($rentInvoice->payments as $payment)

                                    <tr>

                                        <td>
                                            {{ $payment->payment_date?->format('d M Y') ?? '-' }}
                                        </td>

                                        <td>

                                            <span class="badge bg-light text-dark border">

                                                {{ ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->payment_method ?: 'Manual'
                                                    )
                                                ) }}

                                            </span>

                                        </td>

                                        <td>

                                            {{ $payment->provider
                                                ? ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->provider
                                                    )
                                                )
                                                : 'Manual'
                                            }}

                                        </td>

                                        <td>

                                            <code>
                                                {{ $payment->transaction_reference ?: '-' }}
                                            </code>

                                        </td>

                                        <td>

                                            <span class="badge bg-success-subtle text-success border border-success-subtle">

                                                {{ ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->status
                                                    )
                                                ) }}

                                            </span>

                                        </td>

                                        <td class="text-end fw-bold text-success">

                                            +{{ $currencySymbol }}
                                            {{ number_format(
                                                (float) $payment->amount,
                                                2
                                            ) }}

                                            <span class="small">
                                                {{ $currency }}
                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else

                        <div class="p-4 text-center text-muted">

                            <i class="bi bi-info-circle fs-3 d-block mb-2 text-warning"></i>

                            No payments have been recorded for this statement yet.

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection