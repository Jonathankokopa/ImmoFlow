<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use App\Support\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use OwnerScoped;
    /**
     * Payment statuses.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    /**
     * Payment methods.
     */
    public const METHOD_CASH = 'cash';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_MPESA = 'mpesa';
    public const METHOD_AIRTEL_MONEY = 'airtel_money';
    public const METHOD_CARD = 'card';
    public const METHOD_OTHER = 'other';

    /**
     * Payment providers.
     */
    public const PROVIDER_MPESA = 'mpesa';
    public const PROVIDER_AIRTEL_MONEY = 'airtel_money';
    public const PROVIDER_STRIPE = 'stripe';

    protected $fillable = [
        'rent_invoice_id',
        'amount',
        'currency',
        'payment_date',
        'payment_method',
        'provider',
        'transaction_reference',
        'status',
        'metadata',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Payment belongs to a rent invoice.
     *
     * This relationship is retained for compatibility with
     * the current database structure.
     *
     * The payment_allocations relationship is the proper
     * accounting relationship when one payment can cover
     * multiple invoices.
     */
    public function rentInvoice(): BelongsTo
    {
        return $this->belongsTo(
            RentInvoice::class
        );
    }

    /**
     * Payment can be allocated across multiple invoices.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
    }

    /**
     * Tenant credits created from this payment.
     *
     * A payment may create a tenant credit when the payment
     * is larger than the outstanding invoice balance.
     */
    public function tenantCredits(): HasMany
    {
        return $this->hasMany(
            TenantCredit::class
        );
    }

    /**
     * Payment was recorded by a user.
     *
     * Provider-created payments may have no recorded user.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    /**
     * Official receipt issued for this payment.
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(
            PaymentReceipt::class
        );
    }

    /**
     * Get the formatted payment amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return Currency::format(
            (float) $this->amount,
            $this->currency
        );
    }

    /**
     * Determine whether the payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Determine whether the payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Determine whether the payment failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Determine whether the payment was cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Determine whether the payment was refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Determine whether the payment is in a final state.
     */
    public function isFinal(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_COMPLETED,
                self::STATUS_FAILED,
                self::STATUS_CANCELLED,
                self::STATUS_REFUNDED,
            ],
            true
        );
    }

    /**
     * Determine whether the payment represents a successful
     * collected payment.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Determine whether this payment was created through
     * an online payment provider.
     */
    public function hasProvider(): bool
    {
        return !empty($this->provider);
    }

    /**
     * Determine whether this is a manually recorded payment.
     */
    public function isManual(): bool
    {
        return empty($this->provider);
    }

    /**
     * Determine whether this payment belongs to a specific provider.
     */
    public function isProvider(string $provider): bool
    {
        return $this->provider === $provider;
    }

    /**
     * Determine whether this payment belongs to M-Pesa.
     */
    public function isMpesa(): bool
    {
        return $this->provider === self::PROVIDER_MPESA;
    }

    /**
     * Determine whether this payment belongs to Airtel Money.
     */
    public function isAirtelMoney(): bool
    {
        return $this->provider === self::PROVIDER_AIRTEL_MONEY;
    }

    /**
     * Determine whether this payment belongs to Stripe.
     */
    public function isStripe(): bool
    {
        return $this->provider === self::PROVIDER_STRIPE;
    }
}