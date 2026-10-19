<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::forUser()->latest()->get();

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->user()->owner_id) {
            $validated['owner_id'] = $request->user()->owner_id;
        }

        if ($request->hasFile('id_document')) {
            $validated['id_document_path'] = $request->file('id_document')->store('documents/tenants', 'public');
        }

        $tenant = Tenant::create($validated);

        try {
            Notification::send(
                User::all(),
                new SystemNotification(
                    title: 'New Tenant Registered',
                    message: "Tenant {$tenant->full_name} has been added to the system.",
                    url: route('tenants.show', $tenant->id),
                    icon: 'bi-person-plus',
                    badgeColor: 'bg-primary',
                    type: 'tenant'
                )
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    protected function authorizeTenantAccess(Tenant $tenant): void
    {
        if (!Tenant::forUser()->where('id', $tenant->id)->exists()) {
            abort(403, 'You do not have authorization to access or manage this tenant.');
        }
    }

    public function show(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);

        $tenant->load([
            'leases.unit.property',
            'user',
        ]);

        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('id_document')) {
            if ($tenant->id_document_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tenant->id_document_path);
            }
            $validated['id_document_path'] = $request->file('id_document')->store('documents/tenants', 'public');
        }

        /*
         * If the tenant already has a portal account,
         * keep the portal login email synchronized.
         */
        if ($tenant->user) {
            if (
                $validated['email'] &&
                User::where('email', $validated['email'])
                    ->where('id', '!=', $tenant->user->id)
                    ->exists()
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'email' => 'This email address is already being used by another user account.',
                    ]);
            }

            $tenant->user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);
        }

        $tenant->update($validated);

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Create a portal login account for a tenant
     * and send a secure password setup link.
     */
    public function createPortalAccount(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        /*
         * Prevent duplicate portal accounts.
         */
        if ($tenant->user) {
            return redirect()
                ->route('tenants.show', $tenant)
                ->with(
                    'error',
                    'This tenant already has a portal account.'
                );
        }

        /*
         * Tenant must have an email address.
         */
        if (!$tenant->email) {
            return redirect()
                ->route('tenants.show', $tenant)
                ->with(
                    'error',
                    'This tenant must have an email address before a portal account can be created.'
                );
        }

        /*
         * Prevent duplicate email addresses.
         */
        if (User::where('email', $tenant->email)->exists()) {
            return redirect()
                ->route('tenants.show', $tenant)
                ->with(
                    'error',
                    'This email address is already associated with another user account.'
                );
        }

        /*
         * Generate a random temporary password.
         *
         * The tenant will NOT receive or see this password.
         * It only exists until the tenant sets their own password.
         */
        $temporaryPassword = Str::random(40);

        $user = User::create([
            'name' => $tenant->full_name,
            'email' => $tenant->email,
            'password' => $temporaryPassword,
            'role' => 'tenant',
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        /*
         * Generate a secure password-reset token.
         */
        $token = Password::broker()->createToken($user);

        /*
         * Send the password setup/reset email.
         */
        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            /*
             * If the email cannot be sent, remove the account
             * so we don't leave a broken portal account behind.
             */
            $user->delete();

            report($e);

            return redirect()
                ->route('tenants.show', $tenant)
                ->with(
                    'error',
                    'The portal account could not be created because the password setup email could not be sent. Please check your mail configuration.'
                );
        }

        /*
         * Notify existing system users.
         */
        try {
            Notification::send(
                User::where('id', '!=', $user->id)->get(),
                new SystemNotification(
                    title: 'Tenant Portal Account Created',
                    message: "A portal account has been created for {$tenant->full_name}.",
                    url: route('tenants.show', $tenant->id),
                    icon: 'bi-person-check',
                    badgeColor: 'bg-success',
                    type: 'tenant'
                )
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('tenants.show', $tenant)
            ->with(
                'success',
                'Tenant portal account created successfully. A password setup link has been sent to the tenant email address.'
            );
    }

    public function destroy(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        /*
         * If the tenant has a portal account, remove it as well.
         */
        if ($tenant->user) {
            $tenant->user->delete();
        }

        $tenant->delete();

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Activate or deactivate portal access for a tenant.
     */
    public function togglePortalAccess(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        if (!$tenant->user) {
            return back()->with('error', 'This tenant does not have a portal account.');
        }

        $newStatus = !$tenant->user->is_active;
        $tenant->user->update(['is_active' => $newStatus]);

        $message = $newStatus
            ? 'Tenant portal access has been activated.'
            : 'Tenant portal access has been deactivated.';

        return back()->with('success', $message);
    }
}