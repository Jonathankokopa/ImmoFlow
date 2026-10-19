@extends('portal.layout')

@section('title', 'Payments')

@section('content')

<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Payments
            </h1>

            <p class="text-muted mb-0">
                View your rent payment history.
            </p>
        </div>

        {{-- Pay Rent can be added here later --}}
        {{-- <a href="{{ route('portal.payments.pay') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Pay Rent
        </a> --}}

    </div>


    @if($payments->count())

        <div class="card portal-card">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>

                                <th class="px-4">
                                    Payment
                                </th>

                                <th>
                                    Invoice
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Method
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end px-4">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($payments as $payment)

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

                                <tr>

                                    {{-- PAYMENT --}}
                                    <td class="px-4">

                                        <div class="fw-semibold">

                                            {{
                                                $payment->transaction_reference
                                                    ?: 'Payment #' . $payment->id
                                            }}

                                        </div>

                                        <small class="text-muted">
                                            #{{ $payment->id }}
                                        </small>

                                    </td>


                                    {{-- INVOICE --}}
                                    <td>

                                        {{
                                            $payment->rentInvoice?->invoice_number
                                                ?? '—'
                                        }}

                                    </td>


                                    {{-- AMOUNT --}}
                                    <td>

                                        <strong>

                                            {{
                                                number_format(
                                                    (float) $payment->amount,
                                                    2
                                                )
                                            }}

                                            {{ $payment->currency }}

                                        </strong>

                                    </td>


                                    {{-- DATE --}}
                                    <td>

                                        {{
                                            $payment->payment_date
                                                ? \Carbon\Carbon::parse(
                                                    $payment->payment_date
                                                )->format('d M Y')
                                                : '—'
                                        }}

                                    </td>


                                    {{-- METHOD --}}
                                    <td>

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

                                    </td>


                                    {{-- STATUS --}}
                                    <td>

                                        <span
                                            class="badge bg-{{ $statusClass }}"
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

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="text-end px-4">

                                        <a
                                            href="{{
                                                route(
                                                    'portal.payments.show',
                                                    $payment
                                                )
                                            }}"
                                            class="btn btn-sm btn-outline-primary"
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

            </div>

        </div>


        {{-- PAGINATION --}}
        <div class="mt-4">

            {{ $payments->links() }}

        </div>


    @else

        <div class="card portal-card">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i
                        class="bi bi-credit-card"
                        style="font-size: 3rem;"
                    ></i>

                </div>

                <h5>
                    No payments found
                </h5>

                <p class="text-muted mb-0">
                    Your payment history will appear here once payments
                    are recorded.
                </p>

            </div>

        </div>

    @endif

</div>

@endsection