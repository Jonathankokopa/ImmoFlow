<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class LeaseController extends Controller
{
    public function index()
    {
        $leases = Lease::forUser()
            ->with([
                'tenant',
                'unit.property',
            ])
            ->latest()
            ->get();

        return view('leases.index', compact('leases'));
    }

    public function create()
    {
        $tenants = Tenant::forUser()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $units = Unit::forUser()
            ->where('status', 'vacant')
            ->with('property')
            ->orderBy('unit_number')
            ->get();

        return view('leases.create', compact('tenants', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => [
                'required',
                'exists:tenants,id',
            ],

            'unit_id' => [
                'required',
                'exists:units,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            /*
             * monthly_rent is intentionally NOT accepted from
             * the browser.
             *
             * The Unit is the authoritative source of:
             * - monthly_rent
             * - currency
             */

            'security_deposit' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_due_day' => [
                'required',
                'integer',
                'min:1',
                'max:31',
            ],

            'status' => [
                'required',
                'in:active,expired,terminated,pending',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            /*
             * Lock the unit so two users cannot create
             * leases against the same unit simultaneously.
             */
            $unit = Unit::lockForUpdate()
                ->findOrFail($validated['unit_id']);

            /*
             * Currency is controlled by the unit.
             *
             * The user never submits lease currency.
             */
            $unitCurrency = strtoupper((string) $unit->currency);

            if (!in_array($unitCurrency, Currency::SUPPORTED, true)) {
                throw ValidationException::withMessages([
                    'unit_id' => 'The selected unit does not have a supported currency.',
                ]);
            }

            /*
             * The monthly rent is also controlled by the unit.
             *
             * Never trust a monthly_rent value submitted
             * by the browser.
             */
            $unitMonthlyRent = round(
                (float) $unit->monthly_rent,
                2
            );

            if ($unitMonthlyRent < 0) {
                throw ValidationException::withMessages([
                    'unit_id' => 'The selected unit has an invalid monthly rent.',
                ]);
            }

            /*
             * Active leases require a vacant unit.
             */
            if ($validated['status'] === 'active') {
                if ($unit->status !== 'vacant') {
                    throw ValidationException::withMessages([
                        'unit_id' => 'The selected unit is not available.',
                    ]);
                }

                $hasActiveLease = Lease::where('unit_id', $unit->id)
                    ->where('status', 'active')
                    ->exists();

                if ($hasActiveLease) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'The selected unit already has an active lease.',
                    ]);
                }
            }

            /*
             * Normalize the security deposit.
             */
            $securityDeposit = round(
                (float) $validated['security_deposit'],
                2
            );

            /*
             * Build the lease data explicitly.
             *
             * This prevents unexpected request fields from
             * being written into the Lease model.
             */
            $leaseData = [
                'tenant_id' => $validated['tenant_id'],
                'unit_id' => $unit->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],

                /*
                 * IMPORTANT:
                 * Rent comes from the Unit.
                 */
                'monthly_rent' => $unitMonthlyRent,

                /*
                 * IMPORTANT:
                 * Currency comes from the Unit.
                 */
                'currency' => $unitCurrency,

                'security_deposit' => $securityDeposit,
                'payment_due_day' => $validated['payment_due_day'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ];

            $lease = Lease::create($leaseData);

            /*
             * An active lease makes the unit occupied and tenant active.
             */
            if ($validated['status'] === 'active') {
                $unit->update([
                    'status' => 'occupied',
                ]);

                if ($lease->tenant) {
                    $lease->tenant->update(['status' => 'active']);
                    if ($lease->tenant->user) {
                        $lease->tenant->user->update(['is_active' => true]);
                    }
                }
            }

            /*
             * Notify system users about the new lease.
             *
             * Notification failure must not cancel the lease.
             */
            try {
                $tenantName = $lease->tenant?->full_name ?? 'Tenant';
                $unitNumber = $lease->unit?->unit_number ?? '';

                Notification::send(
                    \App\Models\User::all(),
                    new \App\Notifications\SystemNotification(
                        title: 'New Lease Agreement Created',
                        message: "A new lease was created for {$tenantName} (Unit {$unitNumber}).",
                        url: route('leases.show', $lease->id),
                        icon: 'bi-file-earmark-text',
                        badgeColor: 'bg-info',
                        type: 'lease'
                    )
                );
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return redirect()
            ->route('leases.index')
            ->with('success', 'Lease created successfully.');
    }

    protected function authorizeLeaseAccess(Lease $lease): void
    {
        if (!Lease::forUser()->where('id', $lease->id)->exists()) {
            abort(403, 'You do not have authorization to access or manage this lease.');
        }
    }

    public function show(Lease $lease)
    {
        $this->authorizeLeaseAccess($lease);

        $lease->load([
            'tenant',
            'unit.property',
            'rentInvoices.payments',
        ]);

        return view('leases.show', compact('lease'));
    }

    public function edit(Lease $lease)
    {
        $this->authorizeLeaseAccess($lease);

        $tenants = Tenant::forUser()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        /*
         * On edit, allow the current unit even if it is occupied.
         * Other units must be vacant.
         */
        $units = Unit::forUser()
            ->with('property')
            ->where(function ($query) use ($lease) {
                $query->where('status', 'vacant')
                    ->orWhere('id', $lease->unit_id);
            })
            ->orderBy('unit_number')
            ->get();

        return view(
            'leases.edit',
            compact('lease', 'tenants', 'units')
        );
    }

    public function update(Request $request, Lease $lease)
    {
        $this->authorizeLeaseAccess($lease);
        $validated = $request->validate([
            'tenant_id' => [
                'required',
                'exists:tenants,id',
            ],

            'unit_id' => [
                'required',
                'exists:units,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            /*
             * monthly_rent is intentionally NOT accepted
             * from the browser.
             *
             * The selected Unit controls the rent.
             */

            'security_deposit' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_due_day' => [
                'required',
                'integer',
                'min:1',
                'max:31',
            ],

            'status' => [
                'required',
                'in:active,expired,terminated,pending',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($validated, $lease) {
            $oldUnitId = (int) $lease->unit_id;
            $newUnitId = (int) $validated['unit_id'];

            /*
             * Lock both units involved in the change.
             */
            $oldUnit = Unit::lockForUpdate()
                ->find($oldUnitId);

            $newUnit = Unit::lockForUpdate()
                ->findOrFail($newUnitId);

            $newUnitCurrency = strtoupper(
                (string) $newUnit->currency
            );

            /*
             * Currency must always be supported.
             */
            if (!in_array($newUnitCurrency, Currency::SUPPORTED, true)) {
                throw ValidationException::withMessages([
                    'unit_id' => 'The selected unit does not have a supported currency.',
                ]);
            }

            /*
             * Get the selected unit's authoritative monthly rent.
             */
            $newUnitMonthlyRent = round(
                (float) $newUnit->monthly_rent,
                2
            );

            if ($newUnitMonthlyRent < 0) {
                throw ValidationException::withMessages([
                    'unit_id' => 'The selected unit has an invalid monthly rent.',
                ]);
            }

            /*
             * Once invoices exist, the lease's financial history
             * must remain stable.
             */
            $hasFinancialHistory = $lease->rentInvoices()->exists();

            if ($hasFinancialHistory) {
                /*
                 * Existing invoices are tied to this lease.
                 * Do not move the lease to another unit.
                 */
                if ($newUnitId !== $oldUnitId) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'The unit cannot be changed because this lease already has rent invoices.',
                    ]);
                }

                /*
                 * Do not move an invoiced lease to another tenant.
                 */
                if (
                    (int) $validated['tenant_id']
                    !== (int) $lease->tenant_id
                ) {
                    throw ValidationException::withMessages([
                        'tenant_id' => 'The tenant cannot be changed because this lease already has rent invoices.',
                    ]);
                }

                /*
                 * Preserve the historical lease currency.
                 */
                $leaseCurrency = strtoupper(
                    (string) $lease->currency
                );

                if (!in_array($leaseCurrency, Currency::SUPPORTED, true)) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'This lease has an unsupported historical currency and cannot be updated until it is corrected.',
                    ]);
                }

                $currency = $leaseCurrency;

                /*
                 * IMPORTANT:
                 *
                 * Because invoices already exist, do not
                 * silently change the lease's financial basis
                 * to another unit.
                 *
                 * The unit itself cannot change anyway.
                 *
                 * We therefore keep the existing lease rent.
                 */
                $monthlyRent = round(
                    (float) $lease->monthly_rent,
                    2
                );
            } else {
                /*
                 * No invoices exist yet, so the lease follows
                 * the selected unit's rent and currency.
                 */
                $currency = $newUnitCurrency;
                $monthlyRent = $newUnitMonthlyRent;
            }

            /*
             * Normalize security deposit.
             */
            $securityDeposit = round(
                (float) $validated['security_deposit'],
                2
            );

            /*
             * If changing to active, the unit must be available.
             */
            if ($validated['status'] === 'active') {
                if (
                    $newUnitId !== $oldUnitId
                    && $newUnit->status !== 'vacant'
                ) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'The selected unit is not available.',
                    ]);
                }

                /*
                 * Prevent multiple active leases on one unit.
                 */
                $hasAnotherActiveLease = Lease::where(
                    'unit_id',
                    $newUnitId
                )
                    ->where('status', 'active')
                    ->where('id', '!=', $lease->id)
                    ->exists();

                if ($hasAnotherActiveLease) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'The selected unit already has another active lease.',
                    ]);
                }
            }

            /*
             * Update the lease explicitly.
             */
            $lease->update([
                'tenant_id' => $validated['tenant_id'],
                'unit_id' => $newUnitId,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'monthly_rent' => $monthlyRent,
                'currency' => $currency,
                'security_deposit' => $securityDeposit,
                'payment_due_day' => $validated['payment_due_day'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            /*
             * If the lease moved to another unit, release
             * the previous unit when appropriate.
             */
            if ($oldUnitId !== $newUnitId && $oldUnit) {
                $oldUnitHasActiveLease = Lease::where(
                    'unit_id',
                    $oldUnitId
                )
                    ->where('status', 'active')
                    ->where('id', '!=', $lease->id)
                    ->exists();

                if (!$oldUnitHasActiveLease) {
                    $oldUnit->update([
                        'status' => 'vacant',
                    ]);
                }
            }

            /*
             * Synchronize the selected unit and tenant status.
             */
            if ($validated['status'] === 'active') {
                $newUnit->update([
                    'status' => 'occupied',
                ]);

                if ($lease->tenant) {
                    $lease->tenant->update(['status' => 'active']);
                    if ($lease->tenant->user) {
                        $lease->tenant->user->update(['is_active' => true]);
                    }
                }
            } else {
                $newUnitHasActiveLease = Lease::where(
                    'unit_id',
                    $newUnitId
                )
                    ->where('status', 'active')
                    ->where('id', '!=', $lease->id)
                    ->exists();

                if (!$newUnitHasActiveLease) {
                    $newUnit->update([
                        'status' => 'vacant',
                    ]);
                }

                $tenant = $lease->tenant;
                if ($tenant) {
                    $tenantHasActiveLease = Lease::where('tenant_id', $tenant->id)
                        ->where('status', 'active')
                        ->where('id', '!=', $lease->id)
                        ->exists();

                    if (!$tenantHasActiveLease) {
                        $tenant->update(['status' => 'inactive']);
                        if ($tenant->user) {
                            $tenant->user->update(['is_active' => false]);
                        }
                    }
                }
            }
        });

        return redirect()
            ->route('leases.index')
            ->with('success', 'Lease updated successfully.');
    }

    public function destroy(Lease $lease)
    {
        $this->authorizeLeaseAccess($lease);
        /*
         * Never delete a lease with financial history.
         */
        if ($lease->rentInvoices()->exists()) {
            return back()->with(
                'error',
                'This lease cannot be deleted because it has rent invoices recorded against it.'
            );
        }

        DB::transaction(function () use ($lease) {
            $unit = Unit::lockForUpdate()
                ->find($lease->unit_id);

            $lease->delete();

            /*
             * Return the unit to vacant if no active lease remains.
             */
            if ($unit) {
                $hasActiveLease = Lease::where(
                    'unit_id',
                    $unit->id
                )
                    ->where('status', 'active')
                    ->exists();

                if (!$hasActiveLease) {
                    $unit->update([
                        'status' => 'vacant',
                    ]);
                }
            }
        });

        return redirect()
            ->route('leases.index')
            ->with('success', 'Lease deleted successfully.');
    }

    /**
     * Check out a tenant from a lease:
     * - Terminate the lease
     * - Make the unit vacant
     * - Deactivate tenant portal access
     */
    public function checkout(Lease $lease)
    {
        $this->authorizeLeaseAccess($lease);
        DB::transaction(function () use ($lease) {
            $unit = Unit::lockForUpdate()->find($lease->unit_id);

            $lease->update([
                'status' => 'terminated',
            ]);

            if ($unit) {
                $hasOtherActiveLease = Lease::where('unit_id', $unit->id)
                    ->where('status', 'active')
                    ->where('id', '!=', $lease->id)
                    ->exists();

                if (!$hasOtherActiveLease) {
                    $unit->update([
                        'status' => 'vacant',
                    ]);
                }
            }

            $tenant = $lease->tenant;
            if ($tenant) {
                $hasOtherActiveLease = Lease::where('tenant_id', $tenant->id)
                    ->where('status', 'active')
                    ->where('id', '!=', $lease->id)
                    ->exists();

                if (!$hasOtherActiveLease) {
                    $tenant->update(['status' => 'inactive']);
                    if ($tenant->user) {
                        $tenant->user->update(['is_active' => false]);
                    }
                }
            }
        });

        return back()->with('success', 'Tenant checked out successfully. Unit is now vacant and tenant portal access has been deactivated.');
    }
}