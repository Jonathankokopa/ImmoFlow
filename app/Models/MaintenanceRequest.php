<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use OwnerScoped;
    protected $fillable = [
        'unit_id',
        'tenant_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'requested_date',
        'completed_date',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'completed_date' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceCategory::class,
            'category_id'
        );
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(MaintenanceExpense::class);
    }
}