<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Owner extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'national_id',
        'id_document_path',
        'phone',
        'email',
        'address',
        'notes',
    ];

    /**
     * Primary portal login account for this owner.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    /**
     * All users associated with this owner (owner account + staff).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Staff users under this owner.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class)->where('role', '!=', 'owner');
    }

    /**
     * The properties owned by this owner.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}