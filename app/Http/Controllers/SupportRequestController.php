<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SupportRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = $request->user()
            ->supportRequests()
            ->latest()
            ->get();

        return view('support.index', [
            'requests' => $requests,
            'aiEnabled' => filled(config('services.openai.api_key')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:general,account,technical,billing'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $request->user()->supportRequests()->create($validated);

        return redirect()
            ->route('support.index')
            ->with('success', 'Your support request has been submitted.');
    }

    public function askAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:6'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            return response()->json([
                'message' => 'AI Help is not configured yet. Please submit a support request instead.',
            ], 503);
        }

        $history = collect($validated['history'] ?? [])
            ->map(fn (array $item) => strtoupper($item['role']).': '.$item['content'])
            ->implode("\n");

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'store' => false,
                    'instructions' => 'You are the in-app support assistant for a real estate management system. Give concise, practical guidance about navigating the application and common workflows: properties, units, tenants, leases, invoices, payments, maintenance, reports, profile, settings, and support. Do not claim that you changed data or performed actions. Do not ask for passwords, API keys, or sensitive personal information. If you cannot resolve the issue, recommend submitting a support request with the relevant details.',
                    'input' => trim($history."\nUSER: ".$validated['message']),
                    'text' => ['verbosity' => 'low'],
                ]);

            if ($response->failed()) {
                report(new \RuntimeException('OpenAI support request failed: '.$response->status()));

                return response()->json([
                    'message' => 'AI Help is temporarily unavailable. Please submit a support request instead.',
                ], 502);
            }

            $answer = $response->json('output_text')
                ?? collect($response->json('output', []))
                    ->flatMap(fn (array $item) => $item['content'] ?? [])
                    ->where('type', 'output_text')
                    ->pluck('text')
                    ->implode("\n");

            if (blank($answer)) {
                return response()->json([
                    'message' => 'AI Help could not generate a response. Please submit a support request instead.',
                ], 502);
            }

            return response()->json(['answer' => $answer]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'AI Help is temporarily unavailable. Please submit a support request instead.',
            ], 502);
        }
    }
}
