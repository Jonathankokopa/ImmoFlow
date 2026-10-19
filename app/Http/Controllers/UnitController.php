<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use App\Support\Currency;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display all units.
     */
    public function index()
    {
        $units = Unit::forUser()
            ->with('property')
            ->latest()
            ->get();

        return view(
            'units.index',
            compact('units')
        );
    }

    /**
     * Show the create unit form.
     */
    public function create()
    {
        abort_if(auth()->user()->isOwner(), 403, 'Owners cannot create units.');

        $properties = Property::forUser()
            ->orderBy('name')
            ->get();

        return view(
            'units.create',
            compact('properties')
        );
    }

    /**
     * Store a new unit.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isOwner(), 403, 'Owners cannot create units.');
        $validated = $request->validate([
            'property_id' => [
                'required',
                'exists:properties,id',
            ],

            'unit_number' => [
                'required',
                'string',
                'max:255',
            ],

            'floor' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'bedrooms' => [
                'required',
                'integer',
                'min:0',
            ],

            'bathrooms' => [
                'required',
                'integer',
                'min:0',
            ],

            'monthly_rent' => [
                'required',
                'numeric',
                'min:0',
            ],

            'currency' => [
                'required',
                'in:' . implode(',', Currency::SUPPORTED),
            ],

            'status' => [
                'required',
                'in:vacant,occupied,reserved,maintenance',
            ],
        ]);

        $validated['monthly_rent'] = round(
            (float) $validated['monthly_rent'],
            2
        );

        Unit::create($validated);

        return redirect()
            ->route('units.index')
            ->with(
                'success',
                'Unit created successfully.'
            );
    }

    protected function authorizeUnitAccess(Unit $unit): void
    {
        if (!Unit::forUser()->where('id', $unit->id)->exists()) {
            abort(403, 'You do not have authorization to access this unit.');
        }
    }

    /**
     * Display a unit.
     */
    public function show(Unit $unit)
    {
        $this->authorizeUnitAccess($unit);

        $unit->load([
            'property',
            'leases',
            'maintenanceRequests',
        ]);

        return view(
            'units.show',
            compact('unit')
        );
    }

    /**
     * Show the edit unit form.
     */
    public function edit(Unit $unit)
    {
        abort_if(auth()->user()->isOwner(), 403, 'Owners cannot edit units.');
        $this->authorizeUnitAccess($unit);

        $properties = Property::forUser()->orderBy('name')->get();

        return view(
            'units.edit',
            compact(
                'unit',
                'properties'
            )
        );
    }

    /**
     * Update an existing unit.
     */
    public function update(
        Request $request,
        Unit $unit
    ) {
        abort_if($request->user()->isOwner(), 403, 'Owners cannot update units.');
        $this->authorizeUnitAccess($unit);
        $validated = $request->validate([
            'property_id' => [
                'required',
                'exists:properties,id',
            ],

            'unit_number' => [
                'required',
                'string',
                'max:255',
            ],

            'floor' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'bedrooms' => [
                'required',
                'integer',
                'min:0',
            ],

            'bathrooms' => [
                'required',
                'integer',
                'min:0',
            ],

            'monthly_rent' => [
                'required',
                'numeric',
                'min:0',
            ],

            'currency' => [
                'required',
                'in:' . implode(',', Currency::SUPPORTED),
            ],

            'status' => [
                'required',
                'in:vacant,occupied,reserved,maintenance',
            ],
        ]);

        $validated['monthly_rent'] = round(
            (float) $validated['monthly_rent'],
            2
        );

        $unit->update($validated);

        return redirect()
            ->route('units.index')
            ->with(
                'success',
                'Unit updated successfully.'
            );
    }

    /**
     * Delete a unit.
     */
    public function destroy(Unit $unit)
    {
        if ($unit->leases()->exists()) {
            return back()->with(
                'error',
                'This unit cannot be deleted because it has leases assigned to it.'
            );
        }

        if ($unit->maintenanceRequests()->exists()) {
            return back()->with(
                'error',
                'This unit cannot be deleted because it has maintenance requests assigned to it.'
            );
        }

        $unit->delete();

        return redirect()
            ->route('units.index')
            ->with(
                'success',
                'Unit deleted successfully.'
            );
    }
}