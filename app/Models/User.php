<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'owner_id',
        'is_active',
        'profile_photo',
        'language',
        'appearance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function managedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(
            MaintenanceRequest::class,
            'assigned_to'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'recorded_by'
        );
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function supportRequests(): HasMany
    {
        return $this->hasMany(SupportRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'administrator';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [
            'property_manager',
            'accountant',
            'reception_staff',
            'maintenance_staff',
        ], true);
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasRole($roles): bool
    {
        $roles = is_array($roles)
            ? $roles
            : func_get_args();

        return in_array($this->role, $roles, true);
    }
}