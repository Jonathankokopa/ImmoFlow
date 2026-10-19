<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\MaintenanceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::forUser()
            ->with([
                'unit.property',
                'tenant',
                'category',
                'assignedTo',
            ])
            ->latest()
            ->get();

        return view('maintenance_requests.index', compact('requests'));
    }

    public function create()
    {
        $units = Unit::forUser()->with('property')->orderBy('unit_number')->get();
        $tenants = Tenant::forUser()->where('status', 'active')->orderBy('full_name')->get();
        $categories = MaintenanceCategory::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('maintenance_requests.create', compact(
            'units',
            'tenants',
            'categories',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'category_id' => 'nullable|exists:maintenance_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'requested_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $maintenanceRequest = MaintenanceRequest::create($validated);

        try {
            $unitName = $maintenanceRequest->unit?->unit_number ? "Unit {$maintenanceRequest->unit->unit_number}" : "Property Unit";
            $priority = strtoupper($maintenanceRequest->priority);
            \Illuminate\Support\Facades\Notification::send(
                \App\Models\User::all(),
                new \App\Notifications\SystemNotification(
                    title: "New Maintenance Request ({$priority})",
                    message: "{$maintenanceRequest->title} reported for {$unitName}.",
                    url: route('maintenance-requests.show', $maintenanceRequest->id),
                    icon: 'bi-tools',
                    badgeColor: 'bg-warning',
                    type: 'maintenance'
                )
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'Maintenance request created successfully.');
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->load([
            'unit.property',
            'tenant',
            'category',
            'assignedTo',
            'expenses',
        ]);

        return view(
            'maintenance_requests.show',
            compact('maintenanceRequest')
        );
    }

    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        $units = Unit::with('property')->orderBy('unit_number')->get();
        $tenants = Tenant::where('status', 'active')
            ->orWhere('id', $maintenanceRequest->tenant_id)
            ->orderBy('full_name')
            ->get();

        $categories = MaintenanceCategory::where('is_active', true)
            ->orWhere('id', $maintenanceRequest->category_id)
            ->orderBy('name')
            ->get();

        $users = User::orderBy('name')->get();

        return view('maintenance_requests.edit', compact(
            'maintenanceRequest',
            'units',
            'tenants',
            'categories',
            'users'
        ));
    }

    public function update(
        Request $request,
        MaintenanceRequest $maintenanceRequest
    ) {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'category_id' => 'nullable|exists:maintenance_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'requested_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $maintenanceRequest->update($validated);

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'Maintenance request updated successfully.');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->delete();

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }
}