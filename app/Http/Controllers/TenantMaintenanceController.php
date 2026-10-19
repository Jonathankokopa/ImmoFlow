<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCategory;
use App\Models\MaintenanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantMaintenanceController extends Controller
{
    /**
     * Display the tenant's maintenance requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $requests = MaintenanceRequest::query()
            ->where('tenant_id', $user->tenant_id)
            ->with([
                'category',
                'unit',
            ])
            ->latest('requested_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('portal.maintenance.index', [
            'user' => $user,
            'tenant' => $user->tenant,
            'requests' => $requests,
        ]);
    }

    /**
     * Show the new maintenance request form.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $categories = MaintenanceCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('portal.maintenance.create', [
            'user' => $user,
            'tenant' => $user->tenant,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new tenant maintenance request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:maintenance_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'priority' => [
                'required',
                'in:low,medium,high,urgent',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        /*
         * Get the tenant's latest active lease.
         *
         * We determine the unit from the lease instead of accepting
         * unit_id from the browser.
         */
        $lease = $user->tenant
            ->leases()
            ->where('status', 'active')
            ->latest('id')
            ->first();

        abort_unless(
            $lease,
            422,
            'You do not currently have an active lease.'
        );

        /*
         * Make sure the category is currently active.
         */
        $category = MaintenanceCategory::query()
            ->whereKey($validated['category_id'])
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return back()
                ->withInput()
                ->withErrors([
                    'category_id' =>
                        'The selected maintenance category is not available.',
                ]);
        }

        $lease->load('unit.property.owner.user', 'unit.property.manager');

        $property = $lease->unit->property;
        $assignedToId = $property->manager_id;

        $maintenanceRequest = MaintenanceRequest::create([
            'unit_id' => $lease->unit_id,
            'tenant_id' => $user->tenant_id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
            'assigned_to' => $assignedToId,
            'requested_date' => now()->toDateString(),
        ]);

        // Send system notification to Property Manager, Owner, and Administrators
        try {
            $recipients = collect();

            if ($property->manager) {
                $recipients->push($property->manager);
            }

            if ($property->owner && $property->owner->user) {
                $recipients->push($property->owner->user);
            }

            // Also include Administrators
            $admins = \App\Models\User::where('role', 'administrator')->get();
            $recipients = $recipients->merge($admins)->unique('id');

            if ($recipients->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send(
                    $recipients,
                    new \App\Notifications\SystemNotification(
                        title: 'New Maintenance Request',
                        message: "Maintenance request '{$maintenanceRequest->title}' reported for unit {$lease->unit->unit_number} ({$property->name}).",
                        url: route('maintenance-requests.show', $maintenanceRequest->id),
                        icon: 'bi-tools',
                        badgeColor: 'bg-warning',
                        type: 'maintenance'
                    )
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('portal.maintenance')
            ->with(
                'success',
                'Your maintenance request has been submitted successfully.'
            );
    }

    /**
     * Display one maintenance request.
     */
    public function show(
        Request $request,
        MaintenanceRequest $maintenanceRequest
    ): View {
        $user = $request->user();

        $this->ensureTenant($user);

        /*
         * Prevent a tenant from viewing another tenant's request.
         */
        abort_unless(
            (int) $maintenanceRequest->tenant_id ===
                (int) $user->tenant_id,
            404
        );

        $maintenanceRequest->load([
            'category',
            'unit',
            'assignedTo',
        ]);

        return view('portal.maintenance.show', [
            'user' => $user,
            'tenant' => $user->tenant,
            'maintenanceRequest' => $maintenanceRequest,
        ]);
    }

    /**
     * Make sure the authenticated account is an active tenant.
     */
    private function ensureTenant($user): void
    {
        abort_unless(
            $user &&
            $user->is_active &&
            $user->isRole('tenant') &&
            $user->tenant_id,
            403
        );
    }
}