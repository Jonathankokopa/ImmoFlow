<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use App\Support\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class RentInvoice extends Model
{
    use OwnerScoped;
    /**
     * Invoice statuses.
     */
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'lease_id',
        'invoice_number',
        'billing_period',
        'invoice_date',
        'due_date',
        'amount',
        'penalty_amount',
        'amount_paid',
        'currency',
        'status',
        'notes',
    ];

    protected $casts = [
        'billing_period' => 'date',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Invoice belongs to a lease.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Direct payment relationship.
     *
     * Retained for compatibility and for detecting
     * pending or completed payment activity.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Payment allocations applied to this invoice.
     *
     * A single payment may be allocated across
     * multiple invoices.
     */
    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /**
     * Tenant credit allocations applied to this invoice.
     */
    public function creditAllocations(): HasMany
    {
        return $this->hasMany(
            TenantCreditAllocation::class
        );
    }

    /**
     * Determine whether this invoice currency is supported.
     */
    public function hasSupportedCurrency(): bool
    {
        return in_array(
            strtoupper((string) $this->currency),
            Currency::SUPPORTED,
            true
        );
    }

    /**
     * Ensure this invoice has a supported currency.
     *
     * This does not change the currency and never
     * falls back to USD.
     */
    public function ensureSupportedCurrency(): void
    {
        $currency = strtoupper((string) $this->currency);

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            throw new InvalidArgumentException(
                'The invoice has an unsupported currency.'
            );
        }
    }

    /**
     * Calculate the total amount paid toward this invoice.
     *
     * Includes:
     * - completed payment allocations
     * - valid tenant credit allocations
     *
     * Refunded or cancelled tenant credits are excluded.
     */
    public function getTotalPaid(): float
    {
        $completedPayments = $this->paymentAllocations()
            ->whereHas('payment', function ($query) {
                $query->where(
                    'status',
                    Payment::STATUS_COMPLETED
                );
            })
            ->sum('amount');

        $tenantCredits = $this->creditAllocations()
            ->whereHas('tenantCredit', function ($query) {
                $query->whereIn('status', [
                    TenantCredit::STATUS_AVAILABLE,
                    TenantCredit::STATUS_PARTIALLY_USED,
                    TenantCredit::STATUS_USED,
                ]);
            })
            ->sum('amount');

        return round(
            (float) $completedPayments
            + (float) $tenantCredits,
            2
        );
    }

    /**
     * Get total paid in the invoice currency.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->getTotalPaid();
    }

    /**
     * Calculate the outstanding balance.
     */
    public function getOutstandingAmountAttribute(): float
    {
        $invoiceAmount = (float) $this->amount;
        $totalPaid = $this->getTotalPaid();

        return round(
            max(
                0,
                $invoiceAmount - $totalPaid
            ),
            2
        );
    }

    /**
     * Determine whether the invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->getTotalPaid()
            >= (float) $this->amount;
    }

    /**
     * Determine whether the invoice has any payment activity.
     *
     * This includes:
     * - pending provider payments
     * - completed payments
     * - tenant credit allocations
     *
     * An invoice with any financial activity should not
     * have its billing history freely modified or deleted.
     */
    public function hasPayments(): bool
    {
        return $this->payments()->exists()
            || $this->creditAllocations()->exists();
    }

    /**
     * Determine whether the invoice is overdue.
     */
    public function isOverdue(): bool
    {
        if (
            $this->status ===
            self::STATUS_CANCELLED
        ) {
            return false;
        }

        if ($this->isFullyPaid()) {
            return false;
        }

        if (!$this->due_date) {
            return false;
        }

        return now()
            ->startOfDay()
            ->gt(
                $this->due_date
                    ->copy()
                    ->startOfDay()
            );
    }

    /**
     * Calculate the correct invoice status without saving.
     */
    public function calculateStatus(): string
    {
        /*
         * Cancellation always takes priority.
         */
        if (
            $this->status ===
            self::STATUS_CANCELLED
        ) {
            return self::STATUS_CANCELLED;
        }

        $totalPaid = $this->getTotalPaid();
        $invoiceAmount = (float) $this->amount;

        /*
         * Fully paid takes priority over overdue.
         */
        if ($totalPaid >= $invoiceAmount) {
            return self::STATUS_PAID;
        }

        /*
         * Outstanding balance with passed due date.
         */
        if (
            $this->due_date &&
            now()
                ->startOfDay()
                ->gt(
                    $this->due_date
                        ->copy()
                        ->startOfDay()
                )
        ) {
            return self::STATUS_OVERDUE;
        }

        /*
         * Some amount has been paid.
         */
        if ($totalPaid > 0) {
            return self::STATUS_PARTIALLY_PAID;
        }

        return self::STATUS_UNPAID;
    }

    /**
     * Recalculate amount_paid and status, then save.
     *
     * amount_paid is stored in the invoice currency.
     */
    public function recalculatePaymentStatus(): self
    {
        $totalPaid = $this->getTotalPaid();
        $invoiceAmount = (float) $this->amount;

        $this->amount_paid = round(
            min(
                $totalPaid,
                $invoiceAmount
            ),
            2
        );

        $this->status = $this->calculateStatus();

        $this->save();

        return $this;
    }

    /**
     * Get a formatted invoice amount.
     *
     * Never falls back to USD.
     */
    public function getFormattedAmountAttribute(): string
    {
        $currency = strtoupper((string) $this->currency);

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            return number_format(
                (float) $this->amount,
                2
            );
        }

        return Currency::format(
            (float) $this->amount,
            $currency
        );
    }

    /**
     * Get a formatted paid amount.
     *
     * Never falls back to USD.
     */
    public function getFormattedPaidAttribute(): string
    {
        $currency = strtoupper((string) $this->currency);

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            return number_format(
                $this->getTotalPaid(),
                2
            );
        }

        return Currency::format(
            $this->getTotalPaid(),
            $currency
        );
    }

    /**
     * Get a formatted outstanding amount.
     *
     * Never falls back to USD.
     */
    public function getFormattedOutstandingAttribute(): string
    {
        $currency = strtoupper((string) $this->currency);

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            return number_format(
                $this->outstanding_amount,
                2
            );
        }

        return Currency::format(
            $this->outstanding_amount,
            $currency
        );
    }

    /**
     * Get a human-readable billing period.
     */
    public function getFormattedBillingPeriodAttribute(): string
    {
        return $this->billing_period
            ? $this->billing_period->format('F Y')
            : '';
    }
}