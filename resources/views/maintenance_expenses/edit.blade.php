@extends('layouts.app')

@section('title', 'Edit Maintenance Expense')

@section('page-heading', 'Edit Maintenance Expense')

@section('content')

<div class="container-fluid">

    @php
        use App\Support\Currency;

        $currentRequest = $maintenanceExpense->maintenanceRequest;

        $storedCurrency = strtoupper(
            (string) $maintenanceExpense->currency
        );

        $hasStoredCurrency = in_array(
            $storedCurrency,
            Currency::SUPPORTED,
            true
        );
    @endphp

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Edit Maintenance Expense
            </h1>

            <p class="text-muted mb-0">
                Update the details of this maintenance expense.
            </p>

        </div>

        <a href="{{ route('maintenance-expenses.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back to Expenses

        </a>

    </div>

    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger border-0 shadow-sm">

            <div class="d-flex align-items-start">

                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>

                <div>

                    <strong>
                        Please correct the following errors:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif

    {{-- Historical Currency Notice --}}
    <div class="alert alert-info border-0 shadow-sm mb-4">

        <div class="d-flex align-items-start">

            <i class="bi bi-info-circle-fill me-2 mt-1"></i>

            <div>

                <strong>Currency is inherited from the unit.</strong>

                <div class="mt-1">

                    This expense does not have an independent currency selector.
                    The currency is determined by the unit associated with the
                    maintenance request.

                    @if($hasStoredCurrency)

                        This expense was recorded in
                        <strong>{{ $storedCurrency }}</strong>.

                    @else

                        <span class="text-warning">
                            This expense does not currently have a valid recorded
                            currency and should be reviewed.
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>

    <form action="{{ route('maintenance-expenses.update', $maintenanceExpense) }}"
          method="POST">

        @csrf
        @method('PUT')

        {{-- Maintenance Request --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="mb-1">

                    <i class="bi bi-tools me-2"></i>
                    Maintenance Request

                </h5>

                <small class="text-muted">
                    Select the maintenance request this expense belongs to.
                </small>

            </div>

            <div class="card-body">

                <div class="mb-3">

                    <label for="maintenance_request_id"
                           class="form-label">

                        Maintenance Request
                        <span class="text-danger">*</span>

                    </label>

                    <select name="maintenance_request_id"
                            id="maintenance_request_id"
                            class="form-select @error('maintenance_request_id') is-invalid @enderror"
                            required>

                        <option value="">
                            Select Maintenance Request
                        </option>

                        @foreach($requests as $request)

                            @php
                                $requestCurrency = strtoupper(
                                    (string) ($request->unit?->currency ?? '')
                                );

                                $hasCurrency = in_array(
                                    $requestCurrency,
                                    Currency::SUPPORTED,
                                    true
                                );
                            @endphp

                            <option value="{{ $request->id }}"
                                {{ old(
                                    'maintenance_request_id',
                                    $maintenanceExpense->maintenance_request_id
                                ) == $request->id ? 'selected' : '' }}>

                                {{ $request->title }}

                                -
                                {{ $request->unit?->property?->name ?? 'Unknown Property' }}

                                -
                                Unit {{ $request->unit?->unit_number ?? '-' }}

                                @if($hasCurrency)

                                    - {{ $requestCurrency }}

                                @else

                                    - Currency unavailable

                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('maintenance_request_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                    <div class="form-text mt-2">

                        <i class="bi bi-info-circle me-1"></i>

                        The expense currency is inherited automatically from
                        the selected unit.

                    </div>

                </div>

            </div>

        </div>

        {{-- Expense Details --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="mb-1">

                    <i class="bi bi-receipt me-2"></i>
                    Expense Details

                </h5>

                <small class="text-muted">
                    Update the type, amount, description, and date.
                </small>

            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Expense Type --}}
                    <div class="col-md-6 mb-3">

                        <label for="expense_type"
                               class="form-label">

                            Expense Type
                            <span class="text-danger">*</span>

                        </label>

                        <select name="expense_type"
                                id="expense_type"
                                class="form-select @error('expense_type') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Expense Type
                            </option>

                            <option value="labour"
                                {{ old(
                                    'expense_type',
                                    $maintenanceExpense->expense_type
                                ) === 'labour' ? 'selected' : '' }}>
                                Labour
                            </option>

                            <option value="materials"
                                {{ old(
                                    'expense_type',
                                    $maintenanceExpense->expense_type
                                ) === 'materials' ? 'selected' : '' }}>
                                Materials
                            </option>

                            <option value="parts"
                                {{ old(
                                    'expense_type',
                                    $maintenanceExpense->expense_type
                                ) === 'parts' ? 'selected' : '' }}>
                                Parts
                            </option>

                            <option value="other"
                                {{ old(
                                    'expense_type',
                                    $maintenanceExpense->expense_type
                                ) === 'other' ? 'selected' : '' }}>
                                Other
                            </option>

                        </select>

                        @error('expense_type')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                    {{-- Amount --}}
                    <div class="col-md-6 mb-3">

                        <label for="amount"
                               class="form-label">

                            Amount
                            <span class="text-danger">*</span>

                        </label>

                        <div class="input-group">

                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $maintenanceExpense->amount) }}"
                                   min="0"
                                   step="0.01"
                                   required>

                            @if($hasStoredCurrency)

                                <span class="input-group-text">
                                    {{ $storedCurrency }}
                                </span>

                            @else

                                <span class="input-group-text text-warning">
                                    Currency Required
                                </span>

                            @endif

                        </div>

                        @error('amount')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="form-text">

                            Enter the amount in the expense's recorded currency.

                            @if($hasStoredCurrency)
                                Currency: <strong>{{ $storedCurrency }}</strong>
                            @endif

                        </div>

                    </div>

                    {{-- Description --}}
                    <div class="col-md-12 mb-3">

                        <label for="description"
                               class="form-label">

                            Description
                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               name="description"
                               id="description"
                               class="form-control @error('description') is-invalid @enderror"
                               value="{{ old(
                                   'description',
                                   $maintenanceExpense->description
                               ) }}"
                               placeholder="e.g. Replaced leaking kitchen tap"
                               required>

                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                    {{-- Expense Date --}}
                    <div class="col-md-6 mb-3">

                        <label for="expense_date"
                               class="form-label">

                            Expense Date
                            <span class="text-danger">*</span>

                        </label>

                        <input type="date"
                               name="expense_date"
                               id="expense_date"
                               class="form-control @error('expense_date') is-invalid @enderror"
                               value="{{ old(
                                   'expense_date',
                                   $maintenanceExpense->expense_date?->format('Y-m-d')
                               ) }}"
                               required>

                        @error('expense_date')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- Currency Information --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="mb-1">

                    <i class="bi bi-currency-exchange me-2"></i>
                    Expense Currency

                </h5>

                <small class="text-muted">
                    Currency is inherited from the unit.
                </small>

            </div>

            <div class="card-body">

                @if($hasStoredCurrency)

                    <div class="d-flex align-items-center gap-3">

                        <div class="bg-light border rounded p-3">

                            <div class="text-muted small">
                                Recorded Currency
                            </div>

                            <div class="fs-4 fw-bold">
                                {{ $storedCurrency }}
                            </div>

                        </div>

                        <div>

                            <div class="fw-semibold">
                                {{ Currency::name($storedCurrency) }}
                            </div>

                            <div class="text-muted small">
                                {{ Currency::symbol($storedCurrency) }}
                            </div>

                        </div>

                    </div>

                    <div class="alert alert-warning mt-3 mb-0">

                        <i class="bi bi-lock-fill me-2"></i>

                        The recorded currency is historical and cannot be changed
                        by editing the expense. If you select a maintenance
                        request belonging to a unit with a different currency,
                        the update will be rejected.

                    </div>

                @else

                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-exclamation-triangle-fill me-2"></i>

                        <strong>Currency Required.</strong>

                        This expense does not have a valid historical currency.
                        Select a maintenance request belonging to a supported
                        currency so the system can establish the expense currency.

                    </div>

                @endif

            </div>

        </div>

        {{-- Additional Notes --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="mb-1">

                    <i class="bi bi-sticky me-2"></i>
                    Additional Notes

                </h5>

                <small class="text-muted">
                    Add any additional information about this expense.
                </small>

            </div>

            <div class="card-body">

                <label for="notes"
                       class="form-label">

                    Notes

                </label>

                <textarea name="notes"
                          id="notes"
                          class="form-control @error('notes') is-invalid @enderror"
                          rows="4"
                          placeholder="Add any additional notes about this expense...">{{ old(
                              'notes',
                              $maintenanceExpense->notes
                          ) }}</textarea>

                @error('notes')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>

        {{-- Form Actions --}}
        <div class="d-flex flex-wrap gap-2 mb-4">

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-check-circle me-1"></i>
                Update Expense

            </button>

            <a href="{{ route(
                'maintenance-expenses.show',
                $maintenanceExpense
            ) }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-x-circle me-1"></i>
                Cancel

            </a>

        </div>

    </form>

</div>

@endsection