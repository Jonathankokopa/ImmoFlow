<?php

namespace App\Http\Controllers;

use App\Mail\RentInvoiceMail;
use App\Models\Lease;
use App\Models\RentInvoice;
use App\Services\TenantCreditService;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RentInvoiceController extends Controller
{
    public function __construct(
        private readonly TenantCreditService $tenantCreditService
    ) {
    }

    /**
     * Display all rent invoices.
     */
    public function index()
    {
        $invoices = RentInvoice::forUser()
            ->with([
                'lease.tenant',
                'lease.unit.property',
            ])
            ->latest('invoice_date')
            ->latest('id')
            ->get();

        return view(
            'rent_invoices.index',
            compact('invoices')
        );
    }

    /**
     * Show invoice creation form.
     */
    public function create()
    {
        $leases = Lease::forUser()
            ->with([
                'tenant',
                'unit.property',
            ])
            ->where('status', 'active')
            ->whereIn('currency', Currency::SUPPORTED)
            ->whereNotNull('monthly_rent')
            ->where('monthly_rent', '>', 0)
            ->latest()
            ->get();

        return view(
            'rent_invoices.create',
            compact('leases')
        );
    }

    /**
     * Store a new rent invoice.
     *
     * Invoice number is generated automatically by the server.
     *
     * Amount and currency are inherited from the lease.
     *
     * Any available tenant credit matching the invoice
     * currency is automatically applied after creation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lease_id' => [
                'required',
                'exists:leases,id',
            ],

            'billing_period' => [
                'required',
                'date_format:Y-m',
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:invoice_date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
         * Normally one attempt is enough.
         *
         * The retry protects invoice numbering in the rare case
         * where two requests create the first invoice for a year
         * at exactly the same time.
         */
        $maxAttempts = 5;
        $createdInvoice = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::transaction(function () use ($validated, &$createdInvoice) {
                    /*
                     * Lock the lease so its financial terms cannot
                     * change while this invoice is being created.
                     */
                    $lease = Lease::query()
                        ->lockForUpdate()
                        ->findOrFail($validated['lease_id']);

                    $this->ensureLeaseCanReceiveInvoice($lease);

                    $billingPeriod = $this->parseBillingPeriod(
                        $validated['billing_period']
                    );

                    $this->ensureBillingPeriodWithinLease(
                        $lease,
                        $billingPeriod
                    );

                    $this->ensureNoDuplicateBillingPeriod(
                        $lease,
                        $billingPeriod
                    );

                    /*
                     * Lease is the source of truth for:
                     *
                     * - monthly rent
                     * - currency
                     *
                     * These values are NEVER accepted from
                     * the browser.
                     */
                    $amount = round(
                        (float) $lease->monthly_rent,
                        2
                    );

                    $currency = strtoupper(
                        (string) $lease->currency
                    );

                    /*
                     * Generate the invoice number on the server.
                     */
                    $invoiceNumber = $this->generateInvoiceNumber();

                    $createdInvoice = RentInvoice::create([
                        'lease_id' => $lease->id,

                        'invoice_number' => $invoiceNumber,

                        'billing_period' => $billingPeriod->toDateString(),

                        'invoice_date' => $validated['invoice_date'],

                        'due_date' => $validated['due_date'],

                        'amount' => $amount,

                        'currency' => $currency,

                        'amount_paid' => 0,

                        'status' => RentInvoice::STATUS_UNPAID,

                        'notes' => $validated['notes'] ?? null,
                    ]);

                    /*
                     * Automatically apply existing tenant credit
                     * matching this invoice currency.
                     */
                    $this->tenantCreditService
                        ->applyCreditToInvoice($createdInvoice);
                });

                /*
                 * Transaction completed successfully.
                 */
                break;
            } catch (QueryException $e) {
                /*
                 * Only retry when the database reports a duplicate
                 * key/unique constraint violation.
                 */
                if (
                    !$this->isDuplicateKeyException($e)
                    || $attempt === $maxAttempts
                ) {
                    throw $e;
                }
            }
        }

        /*
         * Automatically send rent invoice email to tenant.
         */
        if ($createdInvoice) {
            try {
                $createdInvoice->load(['lease.tenant', 'lease.unit.property']);
                $tenantEmail = $createdInvoice->lease?->tenant?->email;
                if ($tenantEmail) {
                    Mail::to($tenantEmail)->send(new RentInvoiceMail(
                        invoice: $createdInvoice,
                        isReminder: false,
                        isOverdue: false
                    ));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('rent-invoices.index')
            ->with(
                'success',
                'Rent invoice created and emailed to tenant successfully.'
            );
    }

    protected function authorizeInvoiceAccess(RentInvoice $rentInvoice): void
    {
        if (!RentInvoice::forUser()->where('id', $rentInvoice->id)->exists()) {
            abort(403, 'You do not have authorization to access or manage this rent invoice.');
        }
    }

    /**
     * Display invoice details.
     */
    public function show(RentInvoice $rentInvoice)
    {
        $this->authorizeInvoiceAccess($rentInvoice);
        $rentInvoice->load([
            'lease.tenant',
            'lease.unit.property',
            'payments.recordedBy',
            'paymentAllocations.payment',
            'creditAllocations.tenantCredit',
        ]);

        return view(
            'rent_invoices.show',
            compact('rentInvoice')
        );
    }

    /**
     * Show invoice edit form.
     */
    public function edit(RentInvoice $rentInvoice)
    {
        $this->authorizeInvoiceAccess($rentInvoice);
        $rentInvoice->load([
            'lease.tenant',
            'lease.unit.property',
        ]);

        return view(
            'rent_invoices.edit',
            compact('rentInvoice')
        );
    }

    /**
     * Update rent invoice.
     *
     * Invoice number is intentionally NOT accepted from the
     * browser. It is generated once during invoice creation
     * and remains immutable.
     *
     * Financial values are inherited from the lease when the
     * invoice is created and remain unchanged afterward.
     *
     * Once payment activity exists:
     * - billing period cannot change
     * - amount cannot change
     * - currency cannot change
     */
    public function update(
        Request $request,
        RentInvoice $rentInvoice
    ) {
        $this->authorizeInvoiceAccess($rentInvoice);
        $validated = $request->validate([
            'billing_period' => [
                'required',
                'date_format:Y-m',
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:invoice_date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $rentInvoice
        ) {
            $invoice = RentInvoice::query()
                ->lockForUpdate()
                ->findOrFail($rentInvoice->id);

            if (
                $invoice->status ===
                RentInvoice::STATUS_CANCELLED
            ) {
                throw ValidationException::withMessages([
                    'invoice' =>
                        'A cancelled invoice cannot be edited.',
                ]);
            }

            /*
             * Lock the lease while validating the invoice.
             */
            $lease = Lease::query()
                ->lockForUpdate()
                ->findOrFail($invoice->lease_id);

            $this->ensureLeaseHasSupportedCurrency($lease);

            $billingPeriod = $this->parseBillingPeriod(
                $validated['billing_period']
            );

            $hasPayments = $invoice->hasPayments();

            /*
             * A payment or credit allocation means the invoice
             * has financial history.
             *
             * Its billing period cannot be moved.
             */
            if ($hasPayments) {
                $currentBillingPeriod = $invoice->billing_period;

                if (
                    !$currentBillingPeriod
                    || $billingPeriod->toDateString()
                        !== $currentBillingPeriod->format('Y-m-d')
                ) {
                    throw ValidationException::withMessages([
                        'billing_period' =>
                            'The billing period cannot be changed after payment activity has been recorded.',
                    ]);
                }
            } else {
                /*
                 * No financial activity exists, so the billing
                 * period may still be corrected.
                 */
                $this->ensureBillingPeriodWithinLease(
                    $lease,
                    $billingPeriod
                );

                $this->ensureNoDuplicateBillingPeriod(
                    $lease,
                    $billingPeriod,
                    $invoice->id
                );

                $invoice->billing_period =
                    $billingPeriod->toDateString();
            }

            /*
             * Financial values are immutable.
             *
             * Keep the amount and currency originally recorded
             * on the invoice.
             */
            $invoice->amount = round(
                (float) $invoice->amount,
                2
            );

            /*
             * DO NOT change:
             *
             * $invoice->invoice_number
             * $invoice->amount
             * $invoice->currency
             */
            $invoice->invoice_date =
                $validated['invoice_date'];

            $invoice->due_date =
                $validated['due_date'];

            $invoice->notes =
                $validated['notes'] ?? null;

            /*
             * Payments and credit allocations remain the source
             * of truth for amount paid.
             */
            $totalPaid = $invoice->getTotalPaid();

            $invoice->amount_paid = round(
                min(
                    $totalPaid,
                    (float) $invoice->amount
                ),
                2
            );

            $invoice->status =
                $invoice->calculateStatus();

            $invoice->save();
        });

        return redirect()
            ->route(
                'rent-invoices.show',
                $rentInvoice
            )
            ->with(
                'success',
                'Rent invoice updated successfully.'
            );
    }

    /**
     * Cancel a rent invoice.
     *
     * An invoice with any payment activity cannot be cancelled.
     * This includes pending provider payments and credit allocations.
     */
    public function cancel(RentInvoice $rentInvoice)
    {
        $this->authorizeInvoiceAccess($rentInvoice);
        DB::transaction(function () use ($rentInvoice) {
            $invoice = RentInvoice::query()
                ->lockForUpdate()
                ->findOrFail($rentInvoice->id);

            if (
                $invoice->status ===
                RentInvoice::STATUS_CANCELLED
            ) {
                return;
            }

            if ($invoice->hasPayments()) {
                throw ValidationException::withMessages([
                    'invoice' =>
                        'An invoice with payment activity cannot be cancelled. Reverse or refund the payments or credits first.',
                ]);
            }

            $invoice->status =
                RentInvoice::STATUS_CANCELLED;

            $invoice->save();
        });

        return redirect()
            ->route(
                'rent-invoices.show',
                $rentInvoice
            )
            ->with(
                'success',
                'Rent invoice cancelled successfully.'
            );
    }

    /**
     * Delete a rent invoice.
     *
     * Only invoices without payment activity may be deleted.
     */
    public function destroy(RentInvoice $rentInvoice)
    {
        DB::transaction(function () use ($rentInvoice) {
            $invoice = RentInvoice::query()
                ->lockForUpdate()
                ->findOrFail($rentInvoice->id);

            if ($invoice->hasPayments()) {
                throw ValidationException::withMessages([
                    'invoice' =>
                        'This rent invoice cannot be deleted because it has payment activity.',
                ]);
            }

            $invoice->delete();
        });

        return redirect()
            ->route('rent-invoices.index')
            ->with(
                'success',
                'Rent invoice deleted successfully.'
            );
    }

    /**
     * Generate the next invoice number.
     *
     * Format:
     *
     * INV-2026-000001
     * INV-2026-000002
     * INV-2026-000003
     *
     * The sequence restarts each calendar year.
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->year;

        /*
         * Existing invoice rows are locked when available.
         *
         * For the first invoice of a year there is no row to
         * lock, so the unique database constraint plus the
         * transaction retry in store() protects against a
         * simultaneous first invoice.
         */
        $latestInvoice = RentInvoice::query()
            ->where(
                'invoice_number',
                'like',
                "INV-{$year}-%"
            )
            ->orderByDesc('invoice_number')
            ->lockForUpdate()
            ->first();

        if (!$latestInvoice) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr(
                $latestInvoice->invoice_number,
                strrpos(
                    $latestInvoice->invoice_number,
                    '-'
                ) + 1
            );

            $nextNumber = $lastNumber + 1;
        }

        return sprintf(
            'INV-%d-%06d',
            $year,
            $nextNumber
        );
    }

    /**
     * Determine whether the exception represents a duplicate key.
     */
    private function isDuplicateKeyException(
        QueryException $exception
    ): bool {
        $sqlState = $exception->errorInfo[0]
            ?? $exception->getCode();

        if (
            !in_array(
                (string) $sqlState,
                ['23000', '23505'],
                true
            )
        ) {
            return false;
        }

        $message = strtolower(
            $exception->getMessage()
        );

        return str_contains($message, 'duplicate')
            || str_contains($message, 'unique');
    }

    /**
     * Ensure a lease is eligible for invoicing.
     */
    private function ensureLeaseCanReceiveInvoice(
        Lease $lease
    ): void {
        if ($lease->status !== 'active') {
            throw ValidationException::withMessages([
                'lease_id' =>
                    'Only active leases can receive rent invoices.',
            ]);
        }

        if (
            $lease->monthly_rent === null
            || (float) $lease->monthly_rent <= 0
        ) {
            throw ValidationException::withMessages([
                'lease_id' =>
                    'The lease must have a valid monthly rent before an invoice can be created.',
            ]);
        }

        $this->ensureLeaseHasSupportedCurrency($lease);
    }

    /**
     * Ensure the lease has one of the application's
     * supported currencies.
     */
    private function ensureLeaseHasSupportedCurrency(
        Lease $lease
    ): void {
        $currency = strtoupper(
            (string) $lease->currency
        );

        if (
            !in_array(
                $currency,
                Currency::SUPPORTED,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'lease_id' =>
                    'The lease must use a supported currency: USD, KES, or CDF.',
            ]);
        }
    }

    /**
     * Parse a YYYY-MM billing period.
     */
    private function parseBillingPeriod(
        string $billingPeriod
    ): Carbon {
        try {
            $date = Carbon::createFromFormat(
                'Y-m',
                $billingPeriod
            );

            if (!$date || $date->format('Y-m') !== $billingPeriod) {
                throw new \RuntimeException(
                    'Invalid billing period.'
                );
            }

            return $date->startOfMonth();
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'billing_period' =>
                    'The billing period must be a valid month.',
            ]);
        }
    }

    /**
     * Ensure the billing period falls within the lease period.
     */
    private function ensureBillingPeriodWithinLease(
        Lease $lease,
        Carbon $billingPeriod
    ): void {
        $leaseStart = $lease->start_date
            ->copy()
            ->startOfMonth();

        $leaseEnd = $lease->end_date
            ? $lease->end_date->copy()->startOfMonth()
            : null;

        if ($billingPeriod->lt($leaseStart)) {
            throw ValidationException::withMessages([
                'billing_period' =>
                    'The billing period cannot be before the lease start date.',
            ]);
        }

        if (
            $leaseEnd
            && $billingPeriod->gt($leaseEnd)
        ) {
            throw ValidationException::withMessages([
                'billing_period' =>
                    'The billing period cannot be after the lease end date.',
            ]);
        }
    }

    /**
     * Prevent duplicate invoices for the same lease
     * and billing period.
     */
    private function ensureNoDuplicateBillingPeriod(
        Lease $lease,
        Carbon $billingPeriod,
        ?int $ignoreInvoiceId = null
    ): void {
        $query = RentInvoice::query()
            ->where(
                'lease_id',
                $lease->id
            )
            ->where(
                'billing_period',
                $billingPeriod->toDateString()
            );

        if ($ignoreInvoiceId !== null) {
            $query->where(
                'id',
                '!=',
                $ignoreInvoiceId
            );
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'billing_period' =>
                    'An invoice already exists for this lease and billing period.',
            ]);
        }
    }

    /**
     * Manually send or resend invoice email to tenant.
     */
    public function sendEmail(RentInvoice $rentInvoice)
    {
        $this->authorizeInvoiceAccess($rentInvoice);
        $rentInvoice->load(['lease.tenant', 'lease.unit.property']);

        $tenantEmail = $rentInvoice->lease?->tenant?->email;
        if (!$tenantEmail) {
            return back()->with('error', 'The tenant for this lease does not have an email address.');
        }

        try {
            Mail::to($tenantEmail)->send(new RentInvoiceMail(
                invoice: $rentInvoice,
                isReminder: false,
                isOverdue: $rentInvoice->isOverdue() || (float) $rentInvoice->penalty_amount > 0
            ));

            return back()->with('success', 'Rent invoice email sent successfully to ' . $tenantEmail);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Could not send invoice email: ' . $e->getMessage());
        }
    }
}