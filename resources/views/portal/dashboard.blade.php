@extends('portal.layout')

@section('title', 'Dashboard')

@section('page-heading', 'Dashboard')

@section('content')

@php
    use App\Support\Currency;

    $dashboardCurrencyCode = strtoupper(
        (string) ($dashboardCurrency ?? '')
    );

    $hasDashboardCurrency = in_array(
        $dashboardCurrencyCode,
        Currency::SUPPORTED,
        true
    );

    $formatDashboardCurrency = function ($amount) use (
        $dashboardCurrencyCode,
        $hasDashboardCurrency
    ) {
        if (!$hasDashboardCurrency) {
            return number_format((float) $amount, 2);
        }

        return Currency::format(
            (float) $amount,
            $dashboardCurrencyCode
        );
    };
@endphp

<div class="container-fluid">

    {{-- ================================================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ================================================================ --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Welcome, {{ $tenant->full_name }}
            </h2>

            <p class="text-muted mb-0">
                Here's an overview of your tenancy and account.
            </p>
        </div>

        @if($activeLease)

            <span class="badge bg-success px-3 py-2">
                {{ ucfirst(str_replace('_', ' ', $activeLease->status)) }}
            </span>

        @else

            <span class="badge bg-secondary px-3 py-2">
                No Active Lease
            </span>

        @endif

    </div>


    {{-- ================================================================ --}}
    {{-- PROPERTY / LEASE SUMMARY --}}
    {{-- ================================================================ --}}

    <div class="row g-4 mb-4">

        {{-- PROPERTY / UNIT --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                        >
                            <i class="bi bi-house-door fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Current Property
                            </h6>

                            <small class="text-muted">
                                Your current home
                            </small>
                        </div>

                    </div>

                    @if($activeLease && $activeLease->unit)

                        <h5 class="fw-bold mb-2">
                            {{ $activeLease->unit->property->name ?? 'N/A' }}
                        </h5>

                        <p class="mb-0 text-muted">

                            <i class="bi bi-door-open me-1"></i>

                            Unit:

                            {{
                                $activeLease->unit->name
                                ?? $activeLease->unit->unit_number
                                ?? 'N/A'
                            }}

                        </p>

                    @else

                        <p class="text-muted mb-0">
                            No active tenancy found.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- MONTHLY RENT --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-success bg-opacity-10 text-success rounded p-3 me-3"
                        >
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Monthly Rent
                            </h6>

                            <small class="text-muted">
                                Current lease amount
                            </small>
                        </div>

                    </div>

                    @if($activeLease)

                        @php
                            $leaseCurrency = strtoupper(
                                (string) ($activeLease->currency ?? '')
                            );

                            $hasLeaseCurrency = in_array(
                                $leaseCurrency,
                                Currency::SUPPORTED,
                                true
                            );
                        @endphp

                        <h3 class="fw-bold mb-1">

                            @if($hasLeaseCurrency)

                                {{ Currency::format(
                                    (float) $activeLease->monthly_rent,
                                    $leaseCurrency
                                ) }}

                            @else

                                {{ number_format(
                                    (float) $activeLease->monthly_rent,
                                    2
                                ) }}

                            @endif

                        </h3>

                        <small class="text-muted">
                            Per month
                        </small>

                    @else

                        <p class="text-muted mb-0">
                            No active lease.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- CURRENT RENT / BALANCE --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3"
                        >
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Current Rent
                            </h6>

                            <small class="text-muted">
                                {{ $currentMonthName ?? now()->format('F Y') }}
                            </small>
                        </div>

                    </div>


                    @if($currentInvoice)

                        {{-- PAY THIS MONTH --}}
                        <div class="mb-3">

                            <small class="text-muted d-block mb-1">
                                Pay this month
                            </small>

                            <h4 class="fw-bold mb-0">

                                {{ $formatDashboardCurrency(
                                    $currentInvoiceAmount ?? $currentInvoice->amount
                                ) }}

                            </h4>

                            @if($hasDashboardCurrency)

                                <small class="text-muted">
                                    {{ $dashboardCurrencyCode }}
                                </small>

                            @endif

                        </div>


                        {{-- OUTSTANDING BALANCE --}}
                        <div class="border rounded p-3 mb-3">

                            <div
                                class="d-flex justify-content-between align-items-center"
                            >

                                <span class="text-muted">
                                    Outstanding balance
                                </span>

                                <strong
                                    class="{{
                                        (float) ($currentInvoiceOutstanding ?? 0) > 0
                                            ? 'text-danger'
                                            : 'text-success'
                                    }}"
                                >

                                    {{ $formatDashboardCurrency(
                                        $currentInvoiceOutstanding
                                        ?? $currentInvoice->outstanding_amount
                                        ?? 0
                                    ) }}

                                </strong>

                            </div>

                        </div>


                        {{-- PAYMENT STATUS --}}
                        @if(
                            (float) (
                                $currentInvoiceOutstanding
                                ?? $currentInvoice->outstanding_amount
                                ?? 0
                            ) > 0
                        )

                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >

                                <span class="badge bg-warning text-dark">
                                    Amount Due
                                </span>

                                @if(
                                    (float) (
                                        $currentInvoicePaid
                                        ?? $currentInvoice->total_paid
                                        ?? 0
                                    ) > 0
                                )

                                    <small class="text-muted">

                                        Paid:

                                        {{ $formatDashboardCurrency(
                                            $currentInvoicePaid
                                            ?? $currentInvoice->total_paid
                                        ) }}

                                    </small>

                                @endif

                            </div>

                            <a
                                href="{{ route('portal.payments') }}"
                                class="btn btn-primary btn-sm w-100"
                            >
                                <i class="bi bi-credit-card me-1"></i>
                                Pay Now
                            </a>

                        @else

                            <div class="mb-3">

                                <span class="badge bg-success">
                                    Paid in Full
                                </span>

                            </div>

                        @endif


                        {{-- OVERPAYMENT / TENANT CREDIT --}}
                        @if(
                            isset($tenantCreditAmount) &&
                            (float) $tenantCreditAmount > 0
                        )

                            <div class="alert alert-info mb-0 py-2">

                                <div class="d-flex align-items-start">

                                    <i class="bi bi-wallet2 me-2 mt-1"></i>

                                    <div>

                                        <strong>
                                            Overpayment / Credit
                                        </strong>

                                        <div class="small mt-1">

                                            You have a credit of

                                            <strong>
                                                {{ $formatDashboardCurrency(
                                                    $tenantCreditAmount
                                                ) }}
                                            </strong>

                                            available.

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endif

                    @else

                        <div class="text-muted">

                            <p class="mb-2">
                                No invoice found for
                                {{ $currentMonthName ?? now()->format('F Y') }}.
                            </p>

                            @if($activeLease)

                                @php
                                    $leaseCurrency = strtoupper(
                                        (string) ($activeLease->currency ?? '')
                                    );

                                    $hasLeaseCurrency = in_array(
                                        $leaseCurrency,
                                        Currency::SUPPORTED,
                                        true
                                    );
                                @endphp

                                @if($hasLeaseCurrency)

                                    <small>
                                        Monthly rent:
                                        {{ Currency::format(
                                            (float) $activeLease->monthly_rent,
                                            $leaseCurrency
                                        ) }}
                                    </small>

                                @else

                                    <small>
                                        Monthly rent:
                                        {{ number_format(
                                            (float) $activeLease->monthly_rent,
                                            2
                                        ) }}
                                    </small>

                                @endif

                            @endif

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- NEXT PAYMENT --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-danger bg-opacity-10 text-danger rounded p-3 me-3"
                        >
                            <i class="bi bi-calendar-event fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Next Payment
                            </h6>

                            <small class="text-muted">
                                Upcoming due date
                            </small>
                        </div>

                    </div>

                    @if(isset($nextPaymentDue) && $nextPaymentDue)

                        <h5 class="fw-bold mb-1">
                            {{ $nextPaymentDue->format('d M Y') }}
                        </h5>

                        <small class="text-muted">
                            Payment due date
                        </small>

                    @elseif($activeLease && $activeLease->payment_due_day)

                        <h5 class="fw-bold mb-1">
                            Day {{ $activeLease->payment_due_day }}
                        </h5>

                        <small class="text-muted">
                            Monthly payment due date
                        </small>

                    @else

                        <p class="text-muted mb-0">
                            No upcoming payment found.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- LEASE STATUS --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-info bg-opacity-10 text-info rounded p-3 me-3"
                        >
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Lease Status
                            </h6>

                            <small class="text-muted">
                                Current tenancy
                            </small>
                        </div>

                    </div>

                    @if($activeLease)

                        <h5 class="fw-bold mb-1">
                            {{ ucfirst(str_replace('_', ' ', $activeLease->status)) }}
                        </h5>

                        <small class="text-muted">

                            {{ $activeLease->start_date
                                ? $activeLease->start_date->format('d M Y')
                                : 'N/A'
                            }}

                            —

                            {{ $activeLease->end_date
                                ? $activeLease->end_date->format('d M Y')
                                : 'N/A'
                            }}

                        </small>

                    @else

                        <p class="text-muted mb-0">
                            No active lease.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- SECURITY DEPOSIT --}}
        <div class="col-xl-4 col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-secondary bg-opacity-10 text-secondary rounded p-3 me-3"
                        >
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold">
                                Security Deposit
                            </h6>

                            <small class="text-muted">
                                Lease deposit
                            </small>
                        </div>

                    </div>

                    @if($activeLease)

                        @php
                            $securityCurrency = strtoupper(
                                (string) ($activeLease->currency ?? '')
                            );

                            $hasSecurityCurrency = in_array(
                                $securityCurrency,
                                Currency::SUPPORTED,
                                true
                            );
                        @endphp

                        <h5 class="fw-bold mb-1">

                            @if($hasSecurityCurrency)

                                {{ Currency::format(
                                    (float) $activeLease->security_deposit,
                                    $securityCurrency
                                ) }}

                            @else

                                {{ number_format(
                                    (float) $activeLease->security_deposit,
                                    2
                                ) }}

                            @endif

                        </h5>

                        <small class="text-muted">
                            Security deposit
                        </small>

                    @else

                        <p class="text-muted mb-0">
                            No lease information.
                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- RECENT PAYMENTS + MAINTENANCE --}}
    {{-- ================================================================ --}}

    <div class="row g-4 mb-4">

        {{-- RECENT PAYMENTS --}}
        <div class="col-xl-7">

            <div class="card portal-card h-100">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="mb-0 fw-bold">

                            <i class="bi bi-credit-card me-2 text-primary"></i>

                            Recent Payments

                        </h5>

                        <a
                            href="{{ route('portal.payments') }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            View All
                        </a>

                    </div>

                </div>


                <div class="card-body p-0">

                    @if(isset($recentPayments) && $recentPayments->count())

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th class="px-4">
                                            Date
                                        </th>

                                        <th>
                                            Amount
                                        </th>

                                        <th>
                                            Method
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($recentPayments as $payment)

                                        @php
                                            $paymentCurrency = strtoupper(
                                                (string) ($payment->currency ?? '')
                                            );

                                            $hasPaymentCurrency = in_array(
                                                $paymentCurrency,
                                                Currency::SUPPORTED,
                                                true
                                            );

                                            $paymentStatus = strtolower(
                                                (string) $payment->status
                                            );
                                        @endphp

                                        <tr>

                                            <td class="px-4">

                                                {{ $payment->payment_date
                                                    ? \Carbon\Carbon::parse(
                                                        $payment->payment_date
                                                    )->format('d M Y')
                                                    : 'N/A'
                                                }}

                                            </td>

                                            <td class="fw-semibold">

                                                @if($hasPaymentCurrency)

                                                    {{ Currency::format(
                                                        (float) $payment->amount,
                                                        $paymentCurrency
                                                    ) }}

                                                @else

                                                    {{ number_format(
                                                        (float) $payment->amount,
                                                        2
                                                    ) }}

                                                @endif

                                            </td>

                                            <td>

                                                {{ $payment->payment_method
                                                    ? ucfirst(str_replace(
                                                        '_',
                                                        ' ',
                                                        $payment->payment_method
                                                    ))
                                                    : 'N/A'
                                                }}

                                            </td>

                                            <td>

                                                @if(in_array(
                                                    $paymentStatus,
                                                    [
                                                        'completed',
                                                        'paid',
                                                        'success',
                                                        'successful'
                                                    ]
                                                ))

                                                    <span class="badge bg-success">
                                                        {{ ucfirst($paymentStatus) }}
                                                    </span>

                                                @elseif(in_array(
                                                    $paymentStatus,
                                                    [
                                                        'pending',
                                                        'processing'
                                                    ]
                                                ))

                                                    <span class="badge bg-warning text-dark">
                                                        {{ ucfirst($paymentStatus) }}
                                                    </span>

                                                @else

                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst(
                                                            $paymentStatus ?: 'Unknown'
                                                        ) }}
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="p-4 text-center">

                            <i class="bi bi-credit-card fs-1 text-muted"></i>

                            <p class="text-muted mt-2 mb-0">
                                No recent payments found.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- OPEN MAINTENANCE --}}
        <div class="col-xl-5">

            <div class="card portal-card h-100">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="mb-0 fw-bold">

                            <i class="bi bi-tools me-2 text-primary"></i>

                            Open Maintenance

                        </h5>

                        <a
                            href="{{ route('portal.maintenance') }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            View All
                        </a>

                    </div>

                </div>


                <div class="card-body">

                    @if(isset($openMaintenance) && $openMaintenance->count())

                        @foreach($openMaintenance as $request)

                            <a
                                href="{{ route(
                                    'portal.maintenance.show',
                                    $request
                                ) }}"
                                class="text-decoration-none"
                            >

                                <div class="border rounded p-3 mb-3">

                                    <div class="d-flex justify-content-between">

                                        <h6 class="fw-bold text-dark mb-1">

                                            {{ $request->title }}

                                        </h6>

                                        <span class="badge bg-info text-dark">

                                            {{ ucfirst(str_replace(
                                                '_',
                                                ' ',
                                                $request->status
                                            )) }}

                                        </span>

                                    </div>

                                    <small class="text-muted">

                                        {{ $request->requested_date
                                            ? $request->requested_date->format('d M Y')
                                            : $request->created_at->format('d M Y')
                                        }}

                                    </small>

                                </div>

                            </a>

                        @endforeach

                    @else

                        <div class="text-center py-4">

                            <i class="bi bi-tools fs-1 text-muted"></i>

                            <p class="text-muted mt-2 mb-3">
                                No open maintenance requests.
                            </p>

                            <a
                                href="{{ route('portal.maintenance.create') }}"
                                class="btn btn-primary btn-sm"
                            >
                                <i class="bi bi-plus-circle me-1"></i>
                                Submit Request
                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- QUICK ACTIONS --}}
    {{-- ================================================================ --}}

    <div class="card portal-card mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">

                <i class="bi bi-lightning-charge me-2 text-primary"></i>

                Quick Actions

            </h5>

        </div>


        <div class="card-body">

            <div class="row g-3">

                {{-- MY TENANCY --}}
                <div class="col-xl-3 col-md-6">

                    <a
                        href="{{ route('portal.tenancy') }}"
                        class="btn btn-outline-primary w-100 py-3"
                    >

                        <i class="bi bi-house-door d-block fs-4 mb-1"></i>

                        My Tenancy

                    </a>

                </div>


                {{-- PAYMENTS --}}
                <div class="col-xl-3 col-md-6">

                    <a
                        href="{{ route('portal.payments') }}"
                        class="btn btn-outline-success w-100 py-3"
                    >

                        <i class="bi bi-credit-card d-block fs-4 mb-1"></i>

                        Payments

                    </a>

                </div>


                {{-- MAINTENANCE --}}
                <div class="col-xl-3 col-md-6">

                    <a
                        href="{{ route('portal.maintenance.create') }}"
                        class="btn btn-outline-warning w-100 py-3"
                    >

                        <i class="bi bi-tools d-block fs-4 mb-1"></i>

                        Report Maintenance

                    </a>

                </div>


                {{-- PROFILE --}}
                <div class="col-xl-3 col-md-6">

                    <a
                        href="{{ route('portal.profile') }}"
                        class="btn btn-outline-primary w-100 py-3"
                    >

                        <i class="bi bi-person d-block fs-4 mb-1"></i>

                        My Profile

                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- ACCOUNT / NOTIFICATIONS --}}
    {{-- ================================================================ --}}

    <div class="row g-4">

        {{-- ACCOUNT SECURITY --}}
        <div class="col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3"
                        >

                            <i class="bi bi-key fs-4"></i>

                        </div>

                        <div>

                            <h6 class="mb-1 fw-bold">
                                Account Security
                            </h6>

                            <small class="text-muted">
                                Manage your account
                            </small>

                        </div>

                    </div>

                    <p class="text-muted">
                        Keep your password secure and update it regularly.
                    </p>

                    <a
                        href="{{ route('portal.password') }}"
                        class="btn btn-outline-primary btn-sm"
                    >

                        <i class="bi bi-key me-1"></i>

                        Change Password

                    </a>

                </div>

            </div>

        </div>


        {{-- NOTIFICATIONS --}}
        <div class="col-md-6">

            <div class="card portal-card h-100">

                <div class="card-body p-4">

                    @php
                        $dashboardUnreadCount = auth()
                            ->user()
                            ->unreadNotifications()
                            ->count();
                    @endphp

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="bg-danger bg-opacity-10 text-danger rounded p-3 me-3"
                        >

                            <i class="bi bi-bell fs-4"></i>

                        </div>

                        <div>

                            <h6 class="mb-1 fw-bold">
                                Notifications
                            </h6>

                            <small class="text-muted">
                                Important account updates
                            </small>

                        </div>

                    </div>

                    @if($dashboardUnreadCount > 0)

                        <p class="mb-3">

                            You have

                            <strong>
                                {{
                                    $dashboardUnreadCount > 99
                                        ? '99+'
                                        : $dashboardUnreadCount
                                }}
                            </strong>

                            unread notification{{ $dashboardUnreadCount === 1 ? '' : 's' }}.

                        </p>

                    @else

                        <p class="text-muted mb-3">
                            You have no unread notifications.
                        </p>

                    @endif

                    <a
                        href="{{ route('portal.notifications') }}"
                        class="btn btn-outline-primary btn-sm"
                    >

                        <i class="bi bi-bell me-1"></i>

                        View Notifications

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection