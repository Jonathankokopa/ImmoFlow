<?php

namespace App\Models;

use App\Models\Traits\OwnerScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use OwnerScoped;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'owner_id',
        'manager_id',
        'property_type_id',
        'name',
        'address',
        'city',
        'description',
        'total_units',
        'status',
        'date_added',
    ];

    /**
     * The owner of this property.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * The property manager assigned to this property.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * The type of this property.
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * The units belonging to this property.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * All photos belonging to this property.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)
            ->orderBy('sort_order');
    }

    /**
     * The cover photo for this property.
     */
    public function coverPhoto()
    {
        return $this->hasOne(PropertyPhoto::class)
            ->where('is_cover', true);
    }
}