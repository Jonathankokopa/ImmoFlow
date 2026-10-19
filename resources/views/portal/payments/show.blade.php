@extends('portal.layout')

@section('title', 'Payment Details')

@section('content')

<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">

        <a
            href="{{ route('portal.payments') }}"
            class="text-decoration-none"
        >

            <i class="bi bi-arrow-left me-1"></i>

            Back to Payments

        </a>

        <h1 class="h3 mt-3 mb-1">
            Payment Details
        </h1>

        <p class="text-muted mb-0">
            Payment #{{ $payment->id }}
        </p>

    </div>


    @php

        $status = strtolower(
            (string) $payment->status
        );

        $statusClass = match($status) {

            'completed',
            'paid',
            'success',
            'successful'
                => 'success',

            'pending',
            'processing'
                => 'warning',

            'failed',
            'cancelled',
            'rejected'
                => 'danger',

            default
                => 'secondary',

        };

    @endphp


    <div class="row g-4">


        {{-- ============================================================ --}}
        {{-- PAYMENT DETAILS --}}
        {{-- ============================================================ --}}

        <div class="col-lg-8">

            <div class="card portal-card mb-4">

                <div class="card-body p-4">

                    <div
                        class="d-flex justify-content-between align-items-start mb-4"
                    >

                        <div>

                            <small class="text-muted d-block">
                                Payment Amount
                            </small>

                            <h2 class="mb-0">

                                {{
                                    number_format(
                                        (float) $payment->amount,
                                        2
                                    )
                                }}

                                {{ $payment->currency }}

                            </h2>

                        </div>


                        <span
                            class="badge bg-{{ $statusClass }} fs-6"
                        >

                            {{
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $payment->status
                                    )
                                )
                            }}

                        </span>

                    </div>


                    <div class="row g-4">

                        {{-- PAYMENT DATE --}}
                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Payment Date
                            </small>

                            <strong>

                                {{
                                    $payment->payment_date
                                        ? \Carbon\Carbon::parse(
                                            $payment->payment_date
                                        )->format('d M Y')
                                        : '—'
                                }}

                            </strong>

                        </div>


                        {{-- PAYMENT METHOD --}}
                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Payment Method
                            </small>

                            <strong>

                                {{
                                    $payment->payment_method
                                        ? ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $payment->payment_method
                                            )
                                        )
                                        : '—'
                                }}

                            </strong>

                        </div>


                        {{-- PROVIDER --}}
                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Provider
                            </small>

                            <strong>

                                {{ $payment->provider ?: '—' }}

                            </strong>

                        </div>


                        {{-- TRANSACTION REFERENCE --}}
                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Transaction Reference
                            </small>

                            <strong>

                                {{
                                    $payment->transaction_reference
                                        ?: '—'
                                }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ======================================================== --}}
            {{-- RECEIPT --}}
            {{-- ======================================================== --}}

            @if($payment->receipt)

                <div class="card portal-card mb-4">

                    <div class="card-body p-4">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <h5 class="mb-1">
                                    Payment Receipt
                                </h5>

                                <p class="text-muted mb-0">
                                    Your receipt is available.
                                </p>

                            </div>


                            <div class="d-flex gap-2">

                                <a
                                    href="{{ route(
                                        'payments.receipt',
                                        $payment
                                    ) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary"
                                >

                                    <i class="bi bi-receipt me-1"></i>

                                    View Receipt

                                </a>

                                <a
                                    href="{{ route(
                                        'payments.receipt.download',
                                        $payment
                                    ) }}"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-download me-1"></i>

                                    Download PDF

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ======================================================== --}}
            {{-- NOTES --}}
            {{-- ======================================================== --}}

            @if($payment->notes)

                <div class="card portal-card mb-4">

                    <div class="card-body p-4">

                        <h5 class="mb-3">
                            Notes
                        </h5>

                        <p
                            class="mb-0"
                            style="white-space: pre-line;"
                        >
                            {{ $payment->notes }}
                        </p>

                    </div>

                </div>

            @endif

        </div>


        {{-- ============================================================ --}}
        {{-- INVOICE INFORMATION --}}
        {{-- ============================================================ --}}

        <div class="col-lg-4">

            <div class="card portal-card">

                <div class="card-body p-4">

                    <h5 class="mb-4">
                        Invoice Information
                    </h5>


                    @if($payment->rentInvoice)

                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Invoice Number
                            </small>

                            <strong>
                                {{ $payment->rentInvoice->invoice_number }}
                            </strong>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Billing Period
                            </small>

                            <strong>
                                {{ $payment->rentInvoice->billing_period }}
                            </strong>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Invoice Date
                            </small>

                            <strong>

                                {{
                                    $payment->rentInvoice->invoice_date
                                        ? \Carbon\Carbon::parse(
                                            $payment->rentInvoice->invoice_date
                                        )->format('d M Y')
                                        : '—'
                                }}

                            </strong>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Due Date
                            </small>

                            <strong>

                                {{
                                    $payment->rentInvoice->due_date
                                        ? \Carbon\Carbon::parse(
                                            $payment->rentInvoice->due_date
                                        )->format('d M Y')
                                        : '—'
                                }}

                            </strong>

                        </div>


                        {{-- INVOICE AMOUNT --}}
                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Invoice Amount
                            </small>

                            <strong>

                                {{
                                    number_format(
                                        (float) $payment->rentInvoice->amount,
                                        2
                                    )
                                }}

                                {{ $payment->rentInvoice->currency }}

                            </strong>

                        </div>


                        {{-- AMOUNT PAID --}}
                        <div>

                            <small class="text-muted d-block">
                                Amount Paid
                            </small>

                            <strong class="text-success">

                                {{
                                    number_format(
                                        (float) $payment->rentInvoice->amount_paid,
                                        2
                                    )
                                }}

                                {{ $payment->rentInvoice->currency }}

                            </strong>

                        </div>


                    @else

                        <p class="text-muted mb-0">
                            No invoice information is available.
                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection