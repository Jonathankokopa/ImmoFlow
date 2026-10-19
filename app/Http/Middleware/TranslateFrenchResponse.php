<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslateFrenchResponse
{
    /**
     * Translate legacy Blade text that has not yet been converted to __() calls.
     *
     * New or edited templates should use Laravel's translation helpers directly.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->getLocale() !== 'fr' || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $translations = require lang_path('fr/ui.php');
        $content = $response->getContent();

        if (is_string($content)) {
            uksort($translations, fn (string $left, string $right) => mb_strlen($right) <=> mb_strlen($left));
            $response->setContent(strtr($content, $translations));
        }

        return $response;
    }
}
