<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceExpense extends Model
{
    use OwnerScoped;
    protected $fillable = [
        'maintenance_request_id',
        'expense_type',
        'description',
        'amount',
        'currency',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceRequest::class
        );
    }
}