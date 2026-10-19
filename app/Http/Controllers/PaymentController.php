<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\RentInvoice;
use App\Services\MpesaService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly MpesaService $mpesaService
    ) {}

    /**
     * Display a listing of payments.
     */
    public function index(Request $request): View
    {
        $payments = Payment::forUser()
            ->with([
                'rentInvoice.lease.tenant',
                'rentInvoice.lease.unit.property',
                'recordedBy',
            ])
            ->latest('payment_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the manual payment form.
     */
    public function create(): View
    {
        $rentInvoices = RentInvoice::forUser()
            ->with([
                'lease.tenant',
                'lease.unit.property',
            ])
            ->whereIn('status', [
                RentInvoice::STATUS_UNPAID,
                RentInvoice::STATUS_PARTIALLY_PAID,
                RentInvoice::STATUS_OVERDUE,
            ])
            ->get()
            ->filter(function (RentInvoice $invoice): bool {
                return $invoice->outstanding_amount > 0;
            });

        return view('payments.create', compact('rentInvoices'));
    }

    /**
     * Store a manually recorded payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->paymentValidationRules()
        );

        $invoice = RentInvoice::forUser()->findOrFail(
            $validated['rent_invoice_id']
        );

        try {
            $payment = $this->paymentService->createManualPayment(
                $invoice,
                $validated,
                auth()->id()
            );

            return redirect()
                ->route('payments.show', $payment)
                ->with(
                    'success',
                    'Payment recorded successfully.'
                );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The payment could not be recorded. Please try again.'
                );
        }
    }

    /**
     * Initiate an M-Pesa STK Push.
     *
     * The local payment is created as pending.
     * It is NOT completed here.
     *
     * Safaricom's callback is responsible for confirming the payment,
     * after which MpesaService calls PaymentService::completePayment().
     */
    public function initiateMpesa(
        Request $request,
        RentInvoice $rentInvoice
    ): RedirectResponse {
        if (!RentInvoice::forUser()->where('id', $rentInvoice->id)->exists()) {
            abort(403, 'You do not have authorization to process payment for this invoice.');
        }
        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
            ],
        ]);

        try {
            /*
             * Cancelled invoices cannot receive payments.
             */
            if (
                $rentInvoice->status ===
                RentInvoice::STATUS_CANCELLED
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'This invoice has been cancelled and cannot receive payment.'
                    );
            }

            /*
             * Recalculate from the actual payment/allocation system.
             */
            $outstanding = $rentInvoice->outstanding_amount;

            if ($outstanding <= 0) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'This invoice has no outstanding balance.'
                    );
            }

            /*
             * M-Pesa is currently supported for KES only.
             */
            if (
                strtoupper((string) $rentInvoice->currency) !== 'KES'
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'M-Pesa payments are currently available only for KES invoices.'
                    );
            }

            $amount = round(
                (float) $validated['amount'],
                2
            );

            if ($amount <= 0) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Payment amount must be greater than zero.'
                    );
            }

            /*
             * Safaricom Daraja STK Push expects a whole-number amount.
             */
            if (floor($amount) !== $amount) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'M-Pesa payment amounts must be whole numbers.'
                    );
            }

            /*
             * MpesaService performs the actual phone normalization,
             * provider request, and pending payment creation.
             */
            $this->mpesaService->initiateStkPush(
                $rentInvoice,
                $amount,
                $validated['phone'],
                auth()->id()
            );

            return redirect()
                ->route(
                    'rent-invoices.show',
                    $rentInvoice
                )
                ->with(
                    'success',
                    'M-Pesa STK Push sent successfully. Check the phone and enter the M-Pesa PIN to complete the payment.'
                );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The M-Pesa STK Push could not be initiated. Please check the phone number and try again.'
                );
        }
    }

    protected function authorizePaymentAccess(Payment $payment): void
    {
        if (!Payment::forUser()->where('id', $payment->id)->exists()) {
            abort(403, 'You do not have authorization to access or manage this payment.');
        }
    }

    /**
     * Display a payment.
     */
    public function show(Payment $payment): View
    {
        $this->authorizePaymentAccess($payment);
        $payment->load([
            'rentInvoice.lease.tenant',
            'rentInvoice.lease.unit.property',
            'allocations',
            'tenantCredits',
            'recordedBy',
            'receipt',
        ]);

        return view(
            'payments.show',
            compact('payment')
        );
    }

    /**
     * Show the payment edit form.
     */
    public function edit(Payment $payment): View
    {
        $this->authorizePaymentAccess($payment);
        $payment->load([
            'rentInvoice.lease.tenant',
            'rentInvoice.lease.unit.property',
        ]);

        /*
         * Provider payments must be controlled by the provider
         * callback rather than normal CRUD editing.
         */
        if ($payment->hasProvider()) {
            abort(
                403,
                'Provider payments cannot be edited manually.'
            );
        }

        /*
         * Completed payments affect accounting records and receipts.
         * They should not be casually edited through CRUD.
         */
        if ($payment->isCompleted()) {
            abort(
                403,
                'Completed payments cannot be edited.'
            );
        }

        return view(
            'payments.edit',
            compact('payment')
        );
    }

    /**
     * Update a manually recorded payment.
     *
     * Pending provider payments are intentionally excluded.
     */
    public function update(
        Request $request,
        Payment $payment
    ): RedirectResponse {
        $this->authorizePaymentAccess($payment);
        if ($payment->hasProvider()) {
            return back()
                ->with(
                    'error',
                    'Provider payments cannot be edited manually.'
                );
        }

        if ($payment->isCompleted()) {
            return back()
                ->with(
                    'error',
                    'Completed payments cannot be edited.'
                );
        }

        $validated = $request->validate([
            'payment_date' => [
                'required',
                'date',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:' . implode(',', [
                    Payment::METHOD_CASH,
                    Payment::METHOD_BANK_TRANSFER,
                    Payment::METHOD_MPESA,
                    Payment::METHOD_AIRTEL_MONEY,
                    Payment::METHOD_CARD,
                    Payment::METHOD_OTHER,
                ]),
            ],
            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::transaction(function () use (
                $payment,
                $validated
            ): void {
                $payment->update([
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_reference' =>
                        $validated['transaction_reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            });

            return redirect()
                ->route(
                    'payments.show',
                    $payment
                )
                ->with(
                    'success',
                    'Payment updated successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The payment could not be updated.'
                );
        }
    }

    /**
     * Delete a payment.
     *
     * Completed/provider payments are protected.
     */
    public function destroy(
        Payment $payment
    ): RedirectResponse {
        $this->authorizePaymentAccess($payment);
        if ($payment->hasProvider()) {
            return back()
                ->with(
                    'error',
                    'Provider payments cannot be deleted manually.'
                );
        }

        if ($payment->isCompleted()) {
            return back()
                ->with(
                    'error',
                    'Completed payments cannot be deleted. Use the appropriate accounting reversal/refund process instead.'
                );
        }

        try {
            DB::transaction(function () use (
                $payment
            ): void {
                $payment->delete();
            });

            return redirect()
                ->route('payments.index')
                ->with(
                    'success',
                    'Payment deleted successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with(
                    'error',
                    'The payment could not be deleted.'
                );
        }
    }

    /**
     * Validation rules for manually recorded payments.
     */
    private function paymentValidationRules(): array
    {
        return [
            'rent_invoice_id' => [
                'required',
                'integer',
                'exists:rent_invoices,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:' . implode(',', [
                    Payment::METHOD_CASH,
                    Payment::METHOD_BANK_TRANSFER,
                    Payment::METHOD_MPESA,
                    Payment::METHOD_AIRTEL_MONEY,
                    Payment::METHOD_CARD,
                    Payment::METHOD_OTHER,
                ]),
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}