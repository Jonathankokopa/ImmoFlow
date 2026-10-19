<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use App\Support\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    use OwnerScoped;
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'currency',
        'security_deposit',
        'payment_due_day',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'payment_due_day' => 'integer',
    ];

    /**
     * Tenant associated with this lease.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Unit associated with this lease.
     *
     * The unit is the source of the lease currency.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Rent invoices generated from this lease.
     */
    public function rentInvoices(): HasMany
    {
        return $this->hasMany(RentInvoice::class);
    }

    /**
     * Check whether the lease currency is supported.
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
     * Get the currency symbol without falling back to USD.
     */
    public function getCurrencySymbolAttribute(): string
    {
        $currency = strtoupper((string) $this->currency);

        return $this->hasSupportedCurrency()
            ? Currency::symbol($currency)
            : '—';
    }

    /**
     * Get the currency name without falling back to USD.
     */
    public function getCurrencyNameAttribute(): string
    {
        $currency = strtoupper((string) $this->currency);

        return $this->hasSupportedCurrency()
            ? Currency::name($currency)
            : 'Currency not set';
    }
}
