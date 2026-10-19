<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties.
     */
    public function index()
    {
        $properties = Property::forUser()
            ->with([
                'owner',
                'manager',
                'propertyType',
                'coverPhoto',
            ])
            ->latest()
            ->get();

        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        abort_if(auth()->user()->isOwner(), 403, 'Owners cannot create properties.');

        $owners = Owner::orderBy('full_name')->get();

        $propertyManagers = User::where('role', 'property_manager')
            ->orderBy('name')
            ->get();

        $propertyTypes = PropertyType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('properties.create', compact(
            'owners',
            'propertyManagers',
            'propertyTypes'
        ));
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isOwner(), 403, 'Owners cannot create properties.');
        $validated = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'manager_id' => 'nullable|exists:users,id',
            'property_type_id' => 'required|exists:property_types,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_units' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'date_added' => 'nullable|date',

            // Property photos
            'photos' => 'nullable|array',
            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        // Remove photos before creating the property.
        unset($validated['photos']);

        // Create property.
        $property = Property::create($validated);

        /*
        |--------------------------------------------------------------------------
        | Upload Property Photos
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {

                // Store image in:
                // storage/app/public/properties/
                $path = $photo->store('properties', 'public');

                PropertyPhoto::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'category' => 'property',

                    // First uploaded photo becomes cover.
                    'is_cover' => $index === 0,

                    // Preserve upload order.
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property created successfully.');
    }

    protected function authorizePropertyAccess(Property $property): void
    {
        if (!Property::forUser()->where('id', $property->id)->exists()) {
            abort(403, 'You do not have authorization to access this property.');
        }
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        $this->authorizePropertyAccess($property);

        $property->load([
            'owner',
            'manager',
            'propertyType',
            'units',
            'photos',
        ]);

        return view('properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property)
    {
        abort_if(auth()->user()->isOwner(), 403, 'Owners cannot edit properties.');
        $this->authorizePropertyAccess($property);
        $owners = Owner::orderBy('full_name')->get();

        $propertyManagers = User::where('role', 'property_manager')
            ->orderBy('name')
            ->get();

        $propertyTypes = PropertyType::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load existing property photos.
        $property->load('photos');

        return view('properties.edit', compact(
            'property',
            'owners',
            'propertyManagers',
            'propertyTypes'
        ));
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, Property $property)
    {
        abort_if($request->user()->isOwner(), 403, 'Owners cannot update properties.');
        $this->authorizePropertyAccess($property);
        $validated = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'manager_id' => 'nullable|exists:users,id',
            'property_type_id' => 'required|exists:property_types,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_units' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'date_added' => 'nullable|date',

            // Property photos
            'photos' => 'nullable|array',
            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        // Remove photos from property data.
        unset($validated['photos']);

        // Update property details.
        $property->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Upload Newly Added Photos
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photos')) {

            // Find the last existing sort order.
            $lastOrder = $property->photos()
                ->max('sort_order') ?? -1;

            // Check whether a cover already exists.
            $hasCover = $property->photos()
                ->where('is_cover', true)
                ->exists();

            foreach ($request->file('photos') as $index => $photo) {

                // Store uploaded image.
                $path = $photo->store('properties', 'public');

                /*
                |--------------------------------------------------------------------------
                | Cover Photo Logic
                |--------------------------------------------------------------------------
                |
                | If the property has no cover photo, the first newly
                | uploaded photo automatically becomes the cover.
                |
                */

                $makeCover = !$hasCover && $index === 0;

                PropertyPhoto::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'category' => 'property',
                    'is_cover' => $makeCover,
                    'sort_order' => $lastOrder + $index + 1,
                ]);

                // Prevent the other uploaded photos from becoming covers.
                if ($makeCover) {
                    $hasCover = true;
                }
            }
        }

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Delete a property photo.
     */
    public function deletePhoto(
        Property $property,
        PropertyPhoto $photo
    ) {
        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        |
        | Make sure the photo actually belongs to this property.
        |
        */

        if ($photo->property_id !== $property->id) {
            abort(404);
        }

        // Remember whether the deleted photo was the cover.
        $wasCover = $photo->is_cover;

        /*
        |--------------------------------------------------------------------------
        | Delete Physical Image
        |--------------------------------------------------------------------------
        */

        if ($photo->path) {
            Storage::disk('public')->delete($photo->path);
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Database Record
        |--------------------------------------------------------------------------
        */

        $photo->delete();

        /*
        |--------------------------------------------------------------------------
        | Select New Cover
        |--------------------------------------------------------------------------
        |
        | If the deleted image was the cover, automatically select
        | the first remaining photo as the new cover.
        |
        */

        if ($wasCover) {

            $newCover = $property->photos()
                ->orderBy('sort_order')
                ->first();

            if ($newCover) {
                $newCover->update([
                    'is_cover' => true,
                ]);
            }
        }

        return back()->with(
            'success',
            'Property photo deleted successfully.'
        );
    }

    /**
     * Set a property photo as the cover photo.
     */
    public function setCoverPhoto(
        Property $property,
        PropertyPhoto $photo
    ) {
        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        */

        if ($photo->property_id !== $property->id) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Existing Cover
        |--------------------------------------------------------------------------
        */

        $property->photos()->update([
            'is_cover' => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Set Selected Photo as Cover
        |--------------------------------------------------------------------------
        */

        $photo->update([
            'is_cover' => true,
        ]);

        return back()->with(
            'success',
            'Cover photo updated successfully.'
        );
    }

    /**
     * Remove the specified property.
     */
    public function destroy(Property $property)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Deleting Properties With Units
        |--------------------------------------------------------------------------
        */

        if ($property->units()->exists()) {
            return back()->with(
                'error',
                'This property cannot be deleted because it has units assigned to it.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Property Photo Files
        |--------------------------------------------------------------------------
        */

        $property->load('photos');

        foreach ($property->photos as $photo) {

            if ($photo->path) {
                Storage::disk('public')->delete($photo->path);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Property
        |--------------------------------------------------------------------------
        |
        | property_photos records are automatically removed by
        | the foreign-key cascade.
        |
        */

        $property->delete();

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}