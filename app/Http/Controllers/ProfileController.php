<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        /*
        |--------------------------------------------------------------------------
        | Remove existing profile photo
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('remove_photo') && $user->profile_photo) {

            Storage::disk('public')->delete($user->profile_photo);

            $user->profile_photo = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload new profile photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_photo')) {

            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $request
                ->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Profile updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}