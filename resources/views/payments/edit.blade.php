@extends('layouts.app')

@section('title', 'Edit Payment')

@section('page-heading', 'Edit Payment')

@section('content')

@php
    use App\Models\Payment;
    use App\Support\Currency;

    $invoice = $payment->rentInvoice;

    /*
     * Invoice currency is the source of truth.
     * Fall back to the payment currency only for legacy records.
     */
    $currency = strtoupper(
        (string) (
            $invoice?->currency
            ?? $payment->currency
            ?? 'USD'
        )
    );

    $symbol = Currency::symbol($currency);
    $currencyName = Currency::name($currency);

    $tenantName =
        $invoice?->lease?->tenant?->full_name
        ?? $invoice?->lease?->tenant?->name
        ?? 'Tenant';

    $propertyName =
        $invoice?->lease?->unit?->property?->name
        ?? 'Property';

    $unitNumber =
        $invoice?->lease?->unit?->unit_number
        ?? 'N/A';

    /*
     * Calculate the invoice balance while excluding
     * the payment currently being edited.
     *
     * Only completed payments in the invoice currency
     * are included.
     */
    $otherPaymentsTotal = $invoice
        ? $invoice->payments()
            ->where('id', '!=', $payment->id)
            ->where('currency', $currency)
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount')
        : 0;

    $otherPaymentsTotal = round(
        (float) $otherPaymentsTotal,
        2
    );

    $invoiceAmount = $invoice
        ? (float) $invoice->amount
        : 0;

    $availableBalance = max(
        0,
        round($invoiceAmount - $otherPaymentsTotal, 2)
    );

    $currentPaymentAmount = round(
        (float) old('amount', $payment->amount),
        2
    );
@endphp

<div class="container-fluid pm-page px-0">

    {{-- Page Header --}}
    <div class="pm-header mb-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

            <div>

                <div class="pm-eyebrow">
                    <i class="bi bi-pencil-square me-1"></i>
                    Payment Management
                </div>

                <h1 class="h3 mb-1 fw-bold text-slate-900">
                    Edit Payment
                </h1>

                <p class="text-muted mb-0">
                    Update the details of this manual payment.
                    The payment remains attached to its original invoice
                    and currency.
                </p>

            </div>

            <div>

                <a href="{{ route('payments.index') }}"
                   class="btn pm-btn-outline">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Payments

                </a>

            </div>

        </div>

    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="alert alert-danger mb-4">

            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Please correct the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    {{-- Payment Form --}}
    <form
        action="{{ route('payments.update', $payment) }}"
        method="POST"
        id="paymentEditForm"
    >

        @csrf
        @method('PUT')

        {{-- Payment Information --}}
        <div class="card pm-card mb-4">

            <div class="pm-card-header">

                <div>

                    <h5 class="card-title mb-1">

                        <i class="bi bi-cash-stack me-2 text-primary"></i>
                        Payment Information

                    </h5>

                    <div class="card-subtitle">
                        Update the amount, date, payment method,
                        reference, or notes.
                    </div>

                </div>

            </div>

            <div class="pm-card-body">

                {{-- Manual Payment Notice --}}
                <div class="alert alert-info mb-4">

                    <div class="d-flex align-items-start gap-2">

                        <i class="bi bi-info-circle fs-5"></i>

                        <div>

                            <strong>
                                Manual payment
                            </strong>

                            <div class="small mt-1">
                                This form is only for manually recorded
                                completed payments. Online provider payments
                                such as M-Pesa or Airtel Money cannot be edited
                                through this form.
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Original Invoice --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Invoice
                    </label>

                    {{-- Required by the controller, but cannot be changed --}}
                    <input
                        type="hidden"
                        name="rent_invoice_id"
                        value="{{ $invoice?->id }}"
                    >

                    <div class="card border bg-light">

                        <div class="card-body">

                            @if ($invoice)

                                <div class="d-flex align-items-start gap-3">

                                    <div class="flex-shrink-0">

                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">

                                            <i class="bi bi-receipt text-primary fs-4"></i>

                                        </div>

                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="fw-bold mb-1">
                                            {{ $invoice->invoice_number }}
                                        </div>

                                        <div class="text-muted small mb-2">

                                            {{ $tenantName }}
                                            —
                                            {{ $propertyName }}
                                            —
                                            Unit {{ $unitNumber }}

                                        </div>

                                        <div class="d-flex flex-wrap gap-2">

                                            <span class="badge text-bg-light border">

                                                Invoice:
                                                {{ Currency::format(
                                                    (float) $invoice->amount,
                                                    $currency
                                                ) }}

                                            </span>

                                            <span class="badge text-bg-light border">

                                                Currency:
                                                {{ $currency }}
                                                — {{ $currencyName }}

                                            </span>

                                            <span class="badge text-bg-light border">

                                                Available:
                                                {{ Currency::format(
                                                    $availableBalance,
                                                    $currency
                                                ) }}

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @else

                                <div class="text-danger">

                                    <i class="bi bi-exclamation-triangle me-1"></i>

                                    The invoice associated with this payment
                                    could not be found.

                                </div>

                            @endif

                        </div>

                    </div>

                    <div class="form-text">

                        An existing payment cannot be moved to another invoice.
                        This protects the accounting history of the invoice.

                    </div>

                </div>

                {{-- Invoice Balance --}}
                <div
                    id="invoiceBalanceBox"
                    class="alert alert-info mb-4"
                >

                    <div class="d-flex align-items-center gap-2">

                        <i class="bi bi-wallet2"></i>

                        <div>

                            <strong>
                                Available Invoice Balance:
                            </strong>

                            <span
                                id="invoiceBalance"
                                class="fw-bold"
                            >
                                {{ Currency::format(
                                    $availableBalance,
                                    $currency
                                ) }}
                            </span>

                        </div>

                    </div>

                </div>

                {{-- Payment Amount --}}
                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label
                            for="amount"
                            class="form-label fw-semibold"
                        >

                            Payment Amount

                            <span class="text-muted">
                                ({{ $currency }})
                            </span>

                            <span class="text-danger">*</span>

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                {{ $symbol }}
                            </span>

                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                class="form-control @error('amount') is-invalid @enderror"
                                step="0.01"
                                min="0.01"
                                max="{{ number_format($availableBalance, 2, '.', '') }}"
                                value="{{ number_format($currentPaymentAmount, 2, '.', '') }}"
                                required
                            >

                            <span class="input-group-text">
                                {{ $currency }}
                            </span>

                        </div>

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">

                            Enter the payment amount in
                            {{ $currencyName }} ({{ $currency }}).

                        </small>

                    </div>

                </div>

                {{-- Payment Application Preview --}}
                <div class="alert alert-secondary mb-4">

                    <div class="d-flex align-items-start gap-2">

                        <i class="bi bi-wallet2 mt-1"></i>

                        <div>

                            <strong>
                                Amount Applied to Invoice:
                            </strong>

                            <span
                                id="appliedAmount"
                                class="fw-bold"
                            >
                                {{ Currency::format(
                                    $currentPaymentAmount,
                                    $currency
                                ) }}
                            </span>

                            <div class="small text-muted mt-1">

                                The updated amount will be applied to the
                                original invoice.

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Currency Notice --}}
                <div class="alert alert-light border mb-4">

                    <div class="d-flex align-items-start gap-2">

                        <i class="bi bi-currency-exchange text-primary mt-1"></i>

                        <div class="small">

                            <strong>
                                Payment Currency:
                            </strong>

                            This payment is recorded in

                            <strong>
                                {{ $currencyName }} ({{ $currency }})
                            </strong>

                            because that is the currency of the invoice.

                            The currency cannot be changed while editing
                            this payment.

                            No currency conversion or exchange rate is
                            applied.

                        </div>

                    </div>

                </div>

                {{-- Payment Date --}}
                <div class="mb-3">

                    <label
                        for="payment_date"
                        class="form-label fw-semibold"
                    >

                        Payment Date
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        id="payment_date"
                        class="form-control @error('payment_date') is-invalid @enderror"
                        value="{{ old(
                            'payment_date',
                            $payment->payment_date?->format('Y-m-d')
                        ) }}"
                        required
                    >

                    @error('payment_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Payment Method --}}
                <div class="mb-3">

                    <label
                        for="payment_method"
                        class="form-label fw-semibold"
                    >

                        Payment Method
                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="payment_method"
                        id="payment_method"
                        class="form-select @error('payment_method') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select method
                        </option>

                        <option
                            value="{{ Payment::METHOD_CASH }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_CASH
                                ? 'selected'
                                : '' }}
                        >
                            Cash
                        </option>

                        <option
                            value="{{ Payment::METHOD_BANK_TRANSFER }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_BANK_TRANSFER
                                ? 'selected'
                                : '' }}
                        >
                            Bank Transfer
                        </option>

                        <option
                            value="{{ Payment::METHOD_MPESA }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_MPESA
                                ? 'selected'
                                : '' }}
                        >
                            M-Pesa
                        </option>

                        <option
                            value="{{ Payment::METHOD_AIRTEL_MONEY }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_AIRTEL_MONEY
                                ? 'selected'
                                : '' }}
                        >
                            Airtel Money
                        </option>

                        <option
                            value="{{ Payment::METHOD_CARD }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_CARD
                                ? 'selected'
                                : '' }}
                        >
                            Card
                        </option>

                        <option
                            value="{{ Payment::METHOD_OTHER }}"
                            {{ old(
                                'payment_method',
                                $payment->payment_method
                            ) === Payment::METHOD_OTHER
                                ? 'selected'
                                : '' }}
                        >
                            Other
                        </option>

                    </select>

                    @error('payment_method')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Transaction Reference --}}
                <div class="mb-3">

                    <label
                        for="transaction_reference"
                        class="form-label fw-semibold"
                    >
                        Transaction Reference
                    </label>

                    <input
                        type="text"
                        name="transaction_reference"
                        id="transaction_reference"
                        class="form-control @error('transaction_reference') is-invalid @enderror"
                        value="{{ old(
                            'transaction_reference',
                            $payment->transaction_reference
                        ) }}"
                        maxlength="255"
                        placeholder="e.g. payment or transaction reference"
                    >

                    @error('transaction_reference')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Optional reference number or transaction code.
                    </div>

                </div>

                {{-- Notes --}}
                <div class="mb-3">

                    <label
                        for="notes"
                        class="form-label fw-semibold"
                    >
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="form-control @error('notes') is-invalid @enderror"
                        placeholder="Optional payment notes"
                    >{{ old('notes', $payment->notes) }}</textarea>

                    @error('notes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">

            <button
                type="submit"
                class="btn btn-primary"
                id="updatePaymentButton"
            >

                <i class="bi bi-check-circle me-1"></i>
                Update Payment

            </button>

            <a
                href="{{ route('payments.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const amountInput =
        document.getElementById('amount');

    const invoiceBalance =
        document.getElementById('invoiceBalance');

    const appliedAmount =
        document.getElementById('appliedAmount');

    const form =
        document.getElementById('paymentEditForm');

    const updateButton =
        document.getElementById('updatePaymentButton');

    const currency =
        @json($currency);

    const symbol =
        @json($symbol);

    const balance =
        Number(
            @json(number_format(
                $availableBalance,
                2,
                '.',
                ''
            ))
        );

    function formatMoney(amount) {

        const value = Number(amount || 0);

        return (
            symbol +
            ' ' +
            value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) +
            ' ' +
            currency
        );
    }

    function updateInvoiceBalance() {

        invoiceBalance.textContent =
            formatMoney(balance);
    }

    function updateAppliedAmount() {

        const amount =
            parseFloat(amountInput.value || 0);

        if (!Number.isFinite(amount)) {

            appliedAmount.textContent =
                formatMoney(0);

            return;
        }

        appliedAmount.textContent =
            formatMoney(amount);
    }

    function validateAmount() {

        const amount =
            parseFloat(amountInput.value || 0);

        return (
            Number.isFinite(amount) &&
            amount >= 0.01 &&
            amount <= balance
        );
    }

    amountInput.addEventListener(
        'input',
        updateAppliedAmount
    );

    form.addEventListener(
        'submit',
        function (event) {

            const amount =
                parseFloat(amountInput.value || 0);

            if (
                !Number.isFinite(amount) ||
                amount < 0.01
            ) {

                event.preventDefault();

                amountInput.focus();

                alert(
                    'Please enter a valid payment amount.'
                );

                return;
            }

            if (!validateAmount()) {

                event.preventDefault();

                amountInput.focus();

                alert(
                    'The payment amount cannot exceed the available invoice balance of ' +
                    formatMoney(balance) +
                    '.'
                );

                return;
            }

            updateButton.disabled = true;

            updateButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                'Updating Payment...';
        }
    );

    updateInvoiceBalance();
    updateAppliedAmount();

});
</script>

@endsection