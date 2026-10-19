<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCategory;
use Illuminate\Http\Request;

class MaintenanceCategoryController extends Controller
{
    public function index()
    {
        $categories = MaintenanceCategory::latest()->get();

        return view('maintenance_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('maintenance_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:maintenance_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        MaintenanceCategory::create($validated);

        return redirect()
            ->route('maintenance-categories.index')
            ->with('success', 'Maintenance category created successfully.');
    }

    public function show(MaintenanceCategory $maintenanceCategory)
    {
        $maintenanceCategory->load('maintenanceRequests');

        return view(
            'maintenance_categories.show',
            compact('maintenanceCategory')
        );
    }

    public function edit(MaintenanceCategory $maintenanceCategory)
    {
        return view(
            'maintenance_categories.edit',
            compact('maintenanceCategory')
        );
    }

    public function update(
        Request $request,
        MaintenanceCategory $maintenanceCategory
    ) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:maintenance_categories,name,' . $maintenanceCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $maintenanceCategory->update($validated);

        return redirect()
            ->route('maintenance-categories.index')
            ->with('success', 'Maintenance category updated successfully.');
    }

    public function destroy(MaintenanceCategory $maintenanceCategory)
    {
        $maintenanceCategory->delete();

        return redirect()
            ->route('maintenance-categories.index')
            ->with('success', 'Maintenance category deleted successfully.');
    }
}