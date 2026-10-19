<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        if (! $request->user()) {
            abort(401);
        }

        if (! $request->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'This portal account has been deactivated or is no longer active.',
            ]);
        }

        if (! in_array($request->user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}