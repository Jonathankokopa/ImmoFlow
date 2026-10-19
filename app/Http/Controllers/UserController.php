<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'tenant')
            ->latest()
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = [
            'administrator',
            'property_manager',
            'accountant',
            'maintenance_staff',
            'reception_staff',
        ];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    'administrator',
                    'property_manager',
                    'accountant',
                    'maintenance_staff',
                    'reception_staff',
                ]),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        /*
         * Tenant accounts should be managed
         * from the tenant section.
         */
        abort_if($user->role === 'tenant', 404);

        $roles = [
            'administrator',
            'property_manager',
            'accountant',
            'maintenance_staff',
            'reception_staff',
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        /*
         * Tenant accounts should be managed
         * from the tenant section.
         */
        abort_if($user->role === 'tenant', 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'administrator',
                    'property_manager',
                    'accountant',
                    'maintenance_staff',
                    'reception_staff',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make(
                $validated['password']
            );
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'tenant') {
            return back()->with(
                'error',
                'Tenant portal accounts must be managed from the tenant section.'
            );
        }

        if (auth()->id() === $user->id) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}