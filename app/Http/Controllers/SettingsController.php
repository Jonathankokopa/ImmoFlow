<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('settings.index', [
            'language' => auth()->user()->language ?? config('app.locale'),
            'appearance' => auth()->user()->appearance ?? 'light',
        ]);
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', 'max:100'],
            'language' => ['required', 'string', 'in:en,sw,fr'],
            'currency' => ['required', 'string', 'max:10'],
            'appearance' => ['required', 'string', 'in:light,dark,system'],
        ]);

        $request->user()->update([
            'language' => $validated['language'],
            'appearance' => $validated['appearance'],
        ]);

        $request->session()->put('language', $validated['language']);
        app()->setLocale($validated['language']);

        return redirect()
            ->route('settings.index')
            ->with('success', __('Settings updated successfully.'));
    }
}
