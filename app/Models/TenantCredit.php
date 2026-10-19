<?php

namespace App\Models;

use App\Support\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantCredit extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_PARTIALLY_USED = 'partially_used';
    public const STATUS_USED = 'used';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tenant_id',
        'payment_id',
        'amount',
        'remaining_amount',
        'currency',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(
            TenantCreditAllocation::class
        );
    }

    public function getFormattedAmountAttribute(): string
    {
        return Currency::format(
            (float) $this->amount,
            $this->currency
        );
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return Currency::format(
            (float) $this->remaining_amount,
            $this->currency
        );
    }

    public function isAvailable(): bool
    {
        return (float) $this->remaining_amount > 0
            && in_array(
                $this->status,
                [
                    self::STATUS_AVAILABLE,
                    self::STATUS_PARTIALLY_USED,
                ],
                true
            );
    }
}