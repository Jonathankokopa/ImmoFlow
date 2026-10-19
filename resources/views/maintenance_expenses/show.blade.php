@extends('layouts.app')

@section('title', 'Maintenance Expense Details')

@section('page-heading', 'Maintenance Expense Details')

@section('content')

<div class="container-fluid">

    @php
        use App\Support\Currency;

        $request = $maintenanceExpense->maintenanceRequest;
        $unit = $request?->unit;
        $property = $unit?->property;

        $expenseCurrency = strtoupper(
            (string) $maintenanceExpense->currency
        );

        $hasSupportedCurrency = in_array(
            $expenseCurrency,
            Currency::SUPPORTED,
            true
        );
    @endphp

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Maintenance Expense Details
            </h1>

            <p class="text-muted mb-0">
                View the details and related maintenance request information.
            </p>

        </div>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route(
                'maintenance-expenses.edit',
                $maintenanceExpense
            ) }}"
               class="btn btn-outline-primary">

                <i class="bi bi-pencil me-1"></i>
                Edit Expense

            </a>

            <a href="{{ route('maintenance-expenses.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Expenses

            </a>

        </div>

    </div>

    {{-- Currency Warning --}}
    @if(!$hasSupportedCurrency)

        <div class="alert alert-warning border-0 shadow-sm mb-4">

            <div class="d-flex align-items-start">

                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>

                <div>

                    <strong>Currency Required</strong>

                    <div class="mt-1">
                        This expense does not have a valid recorded currency.
                        The amount is therefore not being presented as USD, KES,
                        or CDF.
                    </div>

                </div>

            </div>

        </div>

    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Expense Amount --}}
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">

                            <i class="bi bi-cash-stack fs-4"></i>

                        </div>

                        <div>

                            <div class="text-muted small">
                                Expense Amount
                            </div>

                            @if($hasSupportedCurrency)

                                <div class="fs-4 fw-bold">

                                    {{ Currency::format(
                                        (float) $maintenanceExpense->amount,
                                        $expenseCurrency
                                    ) }}

                                </div>

                            @else

                                <div class="fs-4 fw-bold text-warning">

                                    {{ number_format(
                                        (float) $maintenanceExpense->amount,
                                        2
                                    ) }}

                                </div>

                                <small class="text-warning">
                                    Currency Required
                                </small>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Expense Type --}}
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">

                            <i class="bi bi-tag fs-4"></i>

                        </div>

                        <div>

                            <div class="text-muted small">
                                Expense Type
                            </div>

                            <div class="fs-5 fw-semibold">

                                {{ $maintenanceExpense->expense_type
                                    ? ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $maintenanceExpense->expense_type
                                        )
                                    )
                                    : '-' }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Expense Date --}}
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-3 me-3">

                            <i class="bi bi-calendar-event fs-4"></i>

                        </div>

                        <div>

                            <div class="text-muted small">
                                Expense Date
                            </div>

                            <div class="fs-5 fw-semibold">

                                {{ $maintenanceExpense->expense_date?->format('d M Y') ?? '-' }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Expense Information --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom py-3">

            <h5 class="mb-0">

                <i class="bi bi-receipt me-2"></i>
                Expense Information

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                {{-- Maintenance Request --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Maintenance Request
                    </div>

                    @if($request)

                        <a href="{{ route(
                            'maintenance-requests.show',
                            $request
                        ) }}"
                           class="fw-semibold text-decoration-none">

                            {{ $request->title }}

                        </a>

                    @else

                        <span>
                            -
                        </span>

                    @endif

                </div>

                {{-- Property --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Property
                    </div>

                    @if($property)

                        <a href="{{ route(
                            'properties.show',
                            $property
                        ) }}"
                           class="fw-semibold text-decoration-none">

                            {{ $property->name }}

                        </a>

                    @else

                        <span>
                            -
                        </span>

                    @endif

                </div>

                {{-- Unit --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Unit
                    </div>

                    @if($unit)

                        <a href="{{ route(
                            'units.show',
                            $unit
                        ) }}"
                           class="fw-semibold text-decoration-none">

                            {{ $unit->unit_number }}

                        </a>

                    @else

                        <span>
                            -
                        </span>

                    @endif

                </div>

                {{-- Currency --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Currency
                    </div>

                    @if($hasSupportedCurrency)

                        <div class="d-flex align-items-center gap-2">

                            <span class="badge bg-light text-dark border">
                                {{ $expenseCurrency }}
                            </span>

                            <span class="text-muted">
                                {{ Currency::name($expenseCurrency) }}
                            </span>

                        </div>

                    @else

                        <span class="badge bg-warning text-dark">
                            Currency Required
                        </span>

                    @endif

                </div>

                {{-- Expense Type --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Expense Type
                    </div>

                    <div class="fw-semibold">

                        {{ $maintenanceExpense->expense_type
                            ? ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $maintenanceExpense->expense_type
                                )
                            )
                            : '-' }}

                    </div>

                </div>

                {{-- Amount --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Amount
                    </div>

                    @if($hasSupportedCurrency)

                        <div class="fw-semibold fs-5">

                            {{ Currency::format(
                                (float) $maintenanceExpense->amount,
                                $expenseCurrency
                            ) }}

                        </div>

                    @else

                        <div class="fw-semibold fs-5 text-warning">

                            {{ number_format(
                                (float) $maintenanceExpense->amount,
                                2
                            ) }}

                        </div>

                    @endif

                </div>

                {{-- Expense Date --}}
                <div class="col-md-6 mb-4">

                    <div class="text-muted small mb-1">
                        Expense Date
                    </div>

                    <div class="fw-semibold">

                        {{ $maintenanceExpense->expense_date?->format('d M Y') ?? '-' }}

                    </div>

                </div>

            </div>

            {{-- Description --}}
            <div class="mb-4">

                <div class="text-muted small mb-2">
                    Description
                </div>

                <div class="bg-light border rounded p-3">

                    {{ $maintenanceExpense->description
                        ?: 'No description provided.' }}

                </div>

            </div>

            {{-- Notes --}}
            <div>

                <div class="text-muted small mb-2">
                    Notes
                </div>

                <div class="bg-light border rounded p-3">

                    {{ $maintenanceExpense->notes
                        ?: 'No additional notes.' }}

                </div>

            </div>

        </div>

    </div>

    {{-- Related Maintenance Request --}}
    @if($request)

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                    <h5 class="mb-0">

                        <i class="bi bi-tools me-2"></i>
                        Related Maintenance Request

                    </h5>

                    <a href="{{ route(
                        'maintenance-requests.show',
                        $request
                    ) }}"
                       class="btn btn-sm btn-outline-primary">

                        <i class="bi bi-eye me-1"></i>
                        View Request

                    </a>

                </div>

            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Request Title --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Request Title
                        </div>

                        <div class="fw-semibold">
                            {{ $request->title }}
                        </div>

                    </div>

                    {{-- Priority --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Priority
                        </div>

                        @switch($request->priority)

                            @case('urgent')

                                <span class="badge bg-danger">

                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Urgent

                                </span>

                                @break

                            @case('high')

                                <span class="badge bg-warning text-dark">

                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    High

                                </span>

                                @break

                            @case('medium')

                                <span class="badge bg-info text-dark">
                                    Medium
                                </span>

                                @break

                            @case('low')

                                <span class="badge bg-secondary">
                                    Low
                                </span>

                                @break

                            @default

                                <span class="badge bg-secondary">

                                    {{ ucfirst(
                                        $request->priority ?? 'Unknown'
                                    ) }}

                                </span>

                        @endswitch

                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Status
                        </div>

                        @switch($request->status)

                            @case('pending')

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                                @break

                            @case('assigned')

                                <span class="badge bg-info text-dark">
                                    Assigned
                                </span>

                                @break

                            @case('open')

                                <span class="badge bg-primary">
                                    Open
                                </span>

                                @break

                            @case('in_progress')

                                <span class="badge bg-info text-dark">
                                    In Progress
                                </span>

                                @break

                            @case('completed')

                                <span class="badge bg-success">
                                    Completed
                                </span>

                                @break

                            @case('cancelled')

                                <span class="badge bg-danger">
                                    Cancelled
                                </span>

                                @break

                            @default

                                <span class="badge bg-secondary">

                                    {{ ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $request->status ?? 'Unknown'
                                        )
                                    ) }}

                                </span>

                        @endswitch

                    </div>

                    {{-- Requested Date --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Requested Date
                        </div>

                        <div class="fw-semibold">

                            {{ $request->requested_date?->format('d M Y') ?? '-' }}

                        </div>

                    </div>

                    {{-- Property --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Property
                        </div>

                        <div>

                            @if($property)

                                <a href="{{ route(
                                    'properties.show',
                                    $property
                                ) }}"
                                   class="text-decoration-none fw-semibold">

                                    {{ $property->name }}

                                </a>

                            @else

                                -

                            @endif

                        </div>

                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Unit
                        </div>

                        <div>

                            @if($unit)

                                <a href="{{ route(
                                    'units.show',
                                    $unit
                                ) }}"
                                   class="text-decoration-none fw-semibold">

                                    {{ $unit->unit_number }}

                                </a>

                            @else

                                -

                            @endif

                        </div>

                    </div>

                    {{-- Unit Currency --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small mb-1">
                            Unit Currency
                        </div>

                        @php
                            $unitCurrency = strtoupper(
                                (string) ($unit?->currency ?? '')
                            );

                            $hasUnitCurrency = in_array(
                                $unitCurrency,
                                Currency::SUPPORTED,
                                true
                            );
                        @endphp

                        @if($hasUnitCurrency)

                            <span class="badge bg-light text-dark border">
                                {{ $unitCurrency }}
                            </span>

                        @else

                            <span class="badge bg-warning text-dark">
                                Currency Required
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>

@endsection