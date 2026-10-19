<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index()
    {
        $propertyTypes = PropertyType::latest()->get();

        return view('property_types.index', compact('propertyTypes'));
    }

    public function create()
    {
        return view('property_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_types,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PropertyType::create($validated);

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property type created successfully.');
    }

    public function show(PropertyType $propertyType)
    {
        $propertyType->load('properties');

        return view('property_types.show', compact('propertyType'));
    }

    public function edit(PropertyType $propertyType)
    {
        return view('property_types.edit', compact('propertyType'));
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_types,name,' . $propertyType->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $propertyType->update($validated);

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property type updated successfully.');
    }

    public function destroy(PropertyType $propertyType)
    {
        $propertyType->delete();

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property type deleted successfully.');
    }
}