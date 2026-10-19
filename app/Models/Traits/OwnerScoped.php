<?php

namespace App\Models\Traits;

use App\Models\Lease;
use App\Models\MaintenanceExpense;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait OwnerScoped
{
    /**
     * Scope a query to only include records accessible by the specified or authenticated user.
     */
    public function scopeForUser(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query;
        }

        // Administrators have global access across all records
        if ($user->isAdmin()) {
            return $query;
        }

        $ownerId = $user->owner_id;

        // Owner or Staff assigned to a specific Owner
        if (($user->isOwner() || $user->isStaff()) && $ownerId) {
            return match (static::class) {
                Property::class => $query->where('owner_id', $ownerId),
                Unit::class => $query->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId)),
                Tenant::class => $query->where(function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId)
                      ->orWhereHas('leases.unit.property', fn ($p) => $p->where('owner_id', $ownerId));
                }),
                Lease::class => $query->whereHas('unit.property', fn ($q) => $q->where('owner_id', $ownerId)),
                RentInvoice::class => $query->whereHas('lease.unit.property', fn ($q) => $q->where('owner_id', $ownerId)),
                Payment::class => $query->whereHas('rentInvoice.lease.unit.property', fn ($q) => $q->where('owner_id', $ownerId)),
                MaintenanceRequest::class => $query->whereHas('unit.property', fn ($q) => $q->where('owner_id', $ownerId)),
                MaintenanceExpense::class => $query->whereHas('maintenanceRequest.unit.property', fn ($q) => $q->where('owner_id', $ownerId)),
                default => $query,
            };
        }

        // Property Manager / Staff not explicitly assigned to an owner can manage all properties assigned to them as manager
        if ($user->isStaff() && !$ownerId) {
            if ($user->role === 'property_manager') {
                return match (static::class) {
                    Property::class => $query->where('manager_id', $user->id),
                    Unit::class => $query->whereHas('property', fn ($q) => $q->where('manager_id', $user->id)),
                    Tenant::class => $query->whereHas('leases.unit.property', fn ($q) => $q->where('manager_id', $user->id)),
                    Lease::class => $query->whereHas('unit.property', fn ($q) => $q->where('manager_id', $user->id)),
                    RentInvoice::class => $query->whereHas('lease.unit.property', fn ($q) => $q->where('manager_id', $user->id)),
                    Payment::class => $query->whereHas('rentInvoice.lease.unit.property', fn ($q) => $q->where('manager_id', $user->id)),
                    MaintenanceRequest::class => $query->whereHas('unit.property', fn ($q) => $q->where('manager_id', $user->id)),
                    default => $query,
                };
            }
            return $query;
        }

        // Tenants can only access records related to their tenant_id
        if ($user->isTenant() && $user->tenant_id) {
            $tenantId = $user->tenant_id;
            return match (static::class) {
                Tenant::class => $query->where('id', $tenantId),
                Lease::class => $query->where('tenant_id', $tenantId),
                RentInvoice::class => $query->whereHas('lease', fn ($q) => $q->where('tenant_id', $tenantId)),
                Payment::class => $query->whereHas('rentInvoice.lease', fn ($q) => $q->where('tenant_id', $tenantId)),
                MaintenanceRequest::class => $query->where('tenant_id', $tenantId),
                default => $query,
            };
        }

        return $query;
    }
}
