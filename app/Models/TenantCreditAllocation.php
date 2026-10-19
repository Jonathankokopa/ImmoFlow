<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantCreditAllocation extends Model
{
    protected $fillable = [
        'tenant_credit_id',
        'rent_invoice_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function tenantCredit(): BelongsTo
    {
        return $this->belongsTo(
            TenantCredit::class
        );
    }

    public function rentInvoice(): BelongsTo
    {
        return $this->belongsTo(
            RentInvoice::class
        );
    }
}