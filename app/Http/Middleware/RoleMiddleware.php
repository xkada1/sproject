<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightweight role middleware.
 *
 * Usage:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,manager')
 *
 * Rules:
 * - admin can access everything (override)
 * - if user->role is null, default to 'cashier'
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles = ''): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $userRole = strtolower((string) ($user->role ?? 'cashier'));

        // Admin override
        if ($userRole === 'admin') {
            return $next($request);
        }

        $allowed = array_values(array_filter(array_map(
            static fn ($r) => strtolower(trim((string) $r)),
            explode(',', (string) $roles)
        )));

        // If no roles were specified, allow through
        if (count($allowed) === 0) {
            return $next($request);
        }

        if (in_array($userRole, $allowed, true)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}
