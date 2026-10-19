<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use OwnerScoped;
    protected $fillable = [
        'owner_id',
        'full_name',
        'national_id',
        'id_document_path',
        'phone',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
    ];

    /**
     * Property owner associated with this tenant.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Portal login account.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(TenantCredit::class);
    }

    public function getAvailableCredit(string $currency): float
    {
        return round(
            (float) $this->credits()
                ->where('currency', $currency)
                ->whereIn('status', [
                    TenantCredit::STATUS_AVAILABLE,
                    TenantCredit::STATUS_PARTIALLY_USED,
                ])
                ->sum('remaining_amount'),
            2
        );
    }
}