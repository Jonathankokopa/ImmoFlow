@extends('layouts.app')

@section('title', 'Maintenance Expenses')
@section('page-heading', 'Maintenance Expenses')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Maintenance Expenses</h1>

            <p class="text-muted mb-0">
                Track and manage expenses related to maintenance requests.
            </p>
        </div>

        <a href="{{ route('maintenance-expenses.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Expense
        </a>

    </div>

    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success border-0 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>

    @endif

    @php
        use App\Support\Currency;

        $totalExpenses = $expenses->count();

        $expenseTypes = $expenses
            ->pluck('expense_type')
            ->filter()
            ->unique()
            ->count();

        $latestExpenseDate = $expenses->max('expense_date');

        /*
        |--------------------------------------------------------------------------
        | Currency Totals
        |--------------------------------------------------------------------------
        |
        | Never add USD + KES + CDF together.
        |
        */

        $amountsByCurrency = [];

        foreach (Currency::SUPPORTED as $currency) {
            $amountsByCurrency[$currency] = 0.00;
        }

        foreach ($expenses as $expense) {
            $currency = strtoupper((string) $expense->currency);

            if (!in_array($currency, Currency::SUPPORTED, true)) {
                continue;
            }

            $amountsByCurrency[$currency] += (float) $expense->amount;
        }

        foreach ($amountsByCurrency as $currency => $amount) {
            $amountsByCurrency[$currency] = round($amount, 2);
        }
    @endphp

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Expenses --}}
        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="text-muted small mb-1">
                                Total Expenses
                            </div>

                            <h4 class="mb-0">
                                {{ $totalExpenses }}
                            </h4>

                            <small class="text-muted">
                                Maintenance expense records
                            </small>

                        </div>

                        <div class="text-primary fs-4">
                            <i class="bi bi-receipt"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Total Amount By Currency --}}
        <div class="col-sm-6 col-xl-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div class="w-100">

                            <div class="text-muted small mb-2">
                                Total Maintenance Spending
                            </div>

                            @php
                                $hasCurrencyTotals = false;

                                foreach ($amountsByCurrency as $amount) {
                                    if ($amount != 0) {
                                        $hasCurrencyTotals = true;
                                        break;
                                    }
                                }
                            @endphp

                            @if($hasCurrencyTotals)

                                <div class="d-flex flex-wrap gap-2">

                                    @foreach($amountsByCurrency as $currency => $amount)

                                        @if($amount != 0)

                                            <div class="border rounded px-3 py-2 bg-light">

                                                <div class="small text-muted">
                                                    {{ Currency::name($currency) }}
                                                </div>

                                                <div class="fw-semibold">
                                                    {{ Currency::format($amount, $currency) }}
                                                </div>

                                            </div>

                                        @endif

                                    @endforeach

                                </div>

                            @else

                                <h4 class="mb-0">
                                    -
                                </h4>

                                <small class="text-muted">
                                    No financial data recorded
                                </small>

                            @endif

                        </div>

                        <div class="text-success fs-4 ms-3">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Expense Types --}}
        <div class="col-sm-6 col-xl-2">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="text-muted small mb-1">
                                Expense Types
                            </div>

                            <h4 class="mb-0">
                                {{ $expenseTypes }}
                            </h4>

                            <small class="text-muted">
                                Categories
                            </small>

                        </div>

                        <div class="text-info fs-4">
                            <i class="bi bi-tags"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Latest Expense --}}
        <div class="col-sm-6 col-xl-2">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="text-muted small mb-1">
                                Latest Expense
                            </div>

                            <h5 class="mb-0">
                                {{ $latestExpenseDate?->format('d M Y') ?? '-' }}
                            </h5>

                            <small class="text-muted">
                                Most recent expense date
                            </small>

                        </div>

                        <div class="text-warning fs-4">
                            <i class="bi bi-calendar-event"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Expense List --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                <div>

                    <h5 class="mb-1">
                        <i class="bi bi-cash-stack me-2"></i>
                        Expense List
                    </h5>

                    <small class="text-muted">
                        Maintenance expenses recorded in the system.
                    </small>

                </div>

                <span class="badge bg-light text-dark border">
                    {{ $totalExpenses }} Expense(s)
                </span>

            </div>

        </div>

        <div class="card-body">

            @if($expenses->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Maintenance Request</th>
                                <th>Property</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th class="text-end">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($expenses as $expense)

                                @php
                                    $expenseCurrency = strtoupper(
                                        (string) $expense->currency
                                    );

                                    $hasSupportedCurrency = in_array(
                                        $expenseCurrency,
                                        Currency::SUPPORTED,
                                        true
                                    );
                                @endphp

                                <tr>

                                    <td class="fw-semibold">
                                        {{ $expense->id }}
                                    </td>

                                    {{-- Maintenance Request --}}
                                    <td>

                                        @if($expense->maintenanceRequest)

                                            <a href="{{ route(
                                                'maintenance-requests.show',
                                                $expense->maintenanceRequest
                                            ) }}"
                                               class="text-decoration-none fw-semibold">

                                                {{ $expense->maintenanceRequest->title }}

                                            </a>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Property --}}
                                    <td>

                                        @if($expense->maintenanceRequest?->unit?->property)

                                            <a href="{{ route(
                                                'properties.show',
                                                $expense->maintenanceRequest->unit->property
                                            ) }}"
                                               class="text-decoration-none">

                                                {{ $expense->maintenanceRequest->unit->property->name }}

                                            </a>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Unit --}}
                                    <td>

                                        @if($expense->maintenanceRequest?->unit)

                                            <a href="{{ route(
                                                'units.show',
                                                $expense->maintenanceRequest->unit
                                            ) }}"
                                               class="text-decoration-none">

                                                {{ $expense->maintenanceRequest->unit->unit_number }}

                                            </a>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Expense Type --}}
                                    <td>

                                        <span class="badge bg-light text-dark border">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $expense->expense_type
                                                )
                                            ) }}

                                        </span>

                                    </td>

                                    {{-- Description --}}
                                    <td>
                                        {{ $expense->description ?: '-' }}
                                    </td>

                                    {{-- Currency --}}
                                    <td>

                                        @if($hasSupportedCurrency)

                                            <span class="badge bg-light text-dark border">
                                                {{ $expenseCurrency }}
                                            </span>

                                        @else

                                            <span class="badge bg-warning text-dark">
                                                Currency Required
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Amount --}}
                                    <td class="fw-semibold">

                                        @if($hasSupportedCurrency)

                                            {{ Currency::format(
                                                (float) $expense->amount,
                                                $expenseCurrency
                                            ) }}

                                        @else

                                            <span class="text-warning">
                                                {{ number_format(
                                                    (float) $expense->amount,
                                                    2
                                                ) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Expense Date --}}
                                    <td>
                                        {{ $expense->expense_date?->format('d M Y') ?? '-' }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end">

                                        <div class="d-flex justify-content-end gap-1">

                                            <a href="{{ route(
                                                'maintenance-expenses.show',
                                                $expense
                                            ) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="View Expense">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                            <a href="{{ route(
                                                'maintenance-expenses.edit',
                                                $expense
                                            ) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Edit Expense">

                                                <i class="bi bi-pencil"></i>

                                            </a>

                                            <form action="{{ route(
                                                'maintenance-expenses.destroy',
                                                $expense
                                            ) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this maintenance expense?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Delete Expense">

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

            @else

                <div class="text-center py-5">

                    <div class="text-muted fs-1 mb-3">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <h6 class="mb-1">
                        No Maintenance Expenses
                    </h6>

                    <p class="text-muted mb-3">
                        No maintenance expenses have been recorded yet.
                    </p>

                    <a href="{{ route('maintenance-expenses.create') }}"
                       class="btn btn-primary">

                        <i class="bi bi-plus-lg me-1"></i>
                        Add Expense

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection