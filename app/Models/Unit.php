<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use App\Support\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use OwnerScoped;
    protected $fillable = [
        'property_id',
        'unit_number',
        'floor',
        'bedrooms',
        'bathrooms',
        'monthly_rent',
        'currency',
        'status',
    ];

    protected $casts = [
        'floor' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'monthly_rent' => 'decimal:2',
    ];

    /**
     * Normalize currency codes before saving.
     */
    public function setCurrencyAttribute(?string $value): void
    {
        $this->attributes['currency'] = $value !== null
            ? strtoupper(trim($value))
            : null;
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Get formatted monthly rent.
     *
     * Example:
     * KSh 50,000.00 KES
     * $ 1,200.00 USD
     * FC 2,500,000.00 CDF
     */
    public function getFormattedRentAttribute(): string
    {
        return Currency::format(
            (float) $this->monthly_rent,
            $this->currency
        );
    }

    /**
     * Get the full currency name.
     */
    public function getCurrencyNameAttribute(): string
    {
        return Currency::name($this->currency);
    }

    /**
     * Get the currency symbol.
     */
    public function getCurrencySymbolAttribute(): string
    {
        return Currency::symbol($this->currency);
    }
}