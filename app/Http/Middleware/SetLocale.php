<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'sw', 'fr'];

    /**
     * Apply the authenticated user's language preference to each request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->language ?? $request->session()->get('language');

        app()->setLocale(in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : config('app.fallback_locale'));

        return $next($request);
    }
}
