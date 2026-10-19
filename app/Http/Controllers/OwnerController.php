<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class OwnerController extends Controller
{
    /**
     * Display a listing of owners.
     */
    public function index()
    {
        $owners = Owner::with(['properties.propertyType', 'user'])
            ->latest()
            ->get();

        return view('owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new owner.
     */
    public function create()
    {
        abort_if(auth()->user()->isOwner(), 403, 'Owners cannot create owner records.');
        return view('owners.create');
    }

    /**
     * Store a newly created owner.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isOwner(), 403, 'Owners cannot create owner records.');
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('id_document')) {
            $validated['id_document_path'] = $request->file('id_document')->store('documents/owners', 'public');
        }

        Owner::create($validated);

        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner created successfully.');
    }

    /**
     * Display the specified owner.
     */
    public function show(Owner $owner)
    {
        $owner->load(['properties.propertyType', 'user', 'staff']);

        return view('owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified owner.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    /**
     * Update the specified owner.
     */
    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('id_document')) {
            if ($owner->id_document_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($owner->id_document_path);
            }
            $validated['id_document_path'] = $request->file('id_document')->store('documents/owners', 'public');
        }

        if ($owner->user) {
            if (
                $validated['email'] &&
                User::where('email', $validated['email'])
                    ->where('id', '!=', $owner->user->id)
                    ->exists()
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'email' => 'This email address is already being used by another user account.',
                    ]);
            }

            $owner->user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);
        }

        $owner->update($validated);

        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner updated successfully.');
    }

    /**
     * Create a portal login account for an owner and send a secure password setup link.
     */
    public function createPortalAccount(Owner $owner)
    {
        if ($owner->user) {
            return redirect()
                ->route('owners.show', $owner)
                ->with('error', 'This owner already has a portal account.');
        }

        if (!$owner->email) {
            return redirect()
                ->route('owners.show', $owner)
                ->with('error', 'This owner must have an email address before a portal account can be created.');
        }

        if (User::where('email', $owner->email)->exists()) {
            return redirect()
                ->route('owners.show', $owner)
                ->with('error', 'This email address is already associated with another user account.');
        }

        $temporaryPassword = Str::random(40);

        $user = User::create([
            'name' => $owner->full_name,
            'email' => $owner->email,
            'password' => $temporaryPassword,
            'role' => 'owner',
            'owner_id' => $owner->id,
            'is_active' => true,
        ]);

        $token = Password::broker()->createToken($user);

        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            $user->delete();
            report($e);

            return redirect()
                ->route('owners.show', $owner)
                ->with(
                    'error',
                    'The portal account could not be created because the password setup email could not be sent. Please check your mail configuration.'
                );
        }

        try {
            Notification::send(
                User::where('id', '!=', $user->id)->get(),
                new SystemNotification(
                    title: 'Owner Portal Account Created',
                    message: "An owner portal account has been created for {$owner->full_name}.",
                    url: route('owners.show', $owner->id),
                    icon: 'bi-person-check',
                    badgeColor: 'bg-success',
                    type: 'owner'
                )
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('owners.show', $owner)
            ->with('success', 'Owner portal account created successfully. A password setup link has been sent to the owner email address.');
    }

    /**
     * Activate or deactivate portal access for an owner.
     */
    public function togglePortalAccess(Owner $owner)
    {
        if (!$owner->user) {
            return back()->with('error', 'This owner does not have a portal account.');
        }

        $newStatus = !$owner->user->is_active;
        $owner->user->update(['is_active' => $newStatus]);

        $message = $newStatus
            ? 'Owner portal access has been activated.'
            : 'Owner portal access has been deactivated.';

        return back()->with('success', $message);
    }

    /**
     * Remove the specified owner.
     */
    public function destroy(Owner $owner)
    {
        if ($owner->properties()->exists()) {
            return back()->with(
                'error',
                'This owner cannot be deleted because they have properties assigned to them.'
            );
        }

        if ($owner->user) {
            $owner->user->delete();
        }

        $owner->delete();

        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner deleted successfully.');
    }
}