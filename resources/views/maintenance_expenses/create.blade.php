@extends('layouts.app')

@section('title', 'Add Maintenance Expense')

@section('page-heading', 'Add Maintenance Expense')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Add Maintenance Expense
            </h1>

            <p class="text-muted mb-0">
                Record an expense associated with a maintenance request.
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

    <form action="{{ route('maintenance-expenses.store') }}"
          method="POST">

        @csrf

        {{-- Maintenance Request --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-1">

                    <i class="bi bi-tools me-2"></i>
                    Maintenance Request

                </h5>

                <small class="text-muted">
                    Select the maintenance request this expense belongs to.
                    The expense currency will automatically come from its unit.
                </small>

            </div>

            <div class="card-body">

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
                                \App\Support\Currency::SUPPORTED,
                                true
                            );
                        @endphp

                        <option value="{{ $request->id }}"
                            {{ old('maintenance_request_id') == $request->id ? 'selected' : '' }}>

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

                    The expense currency is inherited automatically from the
                    selected unit. USD, KES, and CDF are supported.

                </div>

            </div>

        </div>

        {{-- Expense Details --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-1">

                    <i class="bi bi-cash-stack me-2"></i>
                    Expense Details

                </h5>

                <small class="text-muted">
                    Enter the type, amount, description, and date of the expense.
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
                                {{ old('expense_type') === 'labour' ? 'selected' : '' }}>
                                Labour
                            </option>

                            <option value="materials"
                                {{ old('expense_type') === 'materials' ? 'selected' : '' }}>
                                Materials
                            </option>

                            <option value="parts"
                                {{ old('expense_type') === 'parts' ? 'selected' : '' }}>
                                Parts
                            </option>

                            <option value="other"
                                {{ old('expense_type') === 'other' ? 'selected' : '' }}>
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

                        <input type="number"
                               name="amount"
                               id="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}"
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               required>

                        @error('amount')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="form-text">
                            Enter the amount in the currency of the selected unit.
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
                               value="{{ old('description') }}"
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
                               value="{{ old('expense_date', date('Y-m-d')) }}"
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

            <div class="card-header bg-white py-3">

                <h5 class="mb-1">

                    <i class="bi bi-currency-exchange me-2"></i>
                    Expense Currency

                </h5>

                <small class="text-muted">
                    Currency is determined automatically.
                </small>

            </div>

            <div class="card-body">

                <div class="alert alert-info mb-0">

                    <i class="bi bi-info-circle-fill me-2"></i>

                    <strong>No currency needs to be selected.</strong>

                    The maintenance expense automatically inherits the currency
                    of the unit associated with the maintenance request.

                    Supported currencies are
                    <strong>USD</strong>,
                    <strong>KES</strong>, and
                    <strong>CDF</strong>.

                </div>

            </div>

        </div>

        {{-- Additional Notes --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-1">

                    <i class="bi bi-journal-text me-2"></i>
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
                          placeholder="Additional notes">{{ old('notes') }}</textarea>

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

                <i class="bi bi-check-lg me-1"></i>
                Save Expense

            </button>

            <a href="{{ route('maintenance-expenses.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-x-lg me-1"></i>
                Cancel

            </a>

        </div>

    </form>

</div>

@endsection