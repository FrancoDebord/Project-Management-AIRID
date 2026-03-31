<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:  ->middleware('role:super_admin')
     *                   ->middleware('role:super_admin,facility_manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Accès refusé. Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
