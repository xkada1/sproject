<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures a branch is selected in session (for multi-branch).
 *
 * - If there are NO branches yet, redirect to setup wizard.
 * - Otherwise, if session('branch_id') is missing, set it to the first branch.
 */
class CurrentBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow setup routes without forcing a branch
        if ($request->routeIs('setup.*')) {
            return $next($request);
        }

        // If no branches exist yet, force setup
        if (Branch::query()->count() === 0) {
            return redirect()->route('setup.branch.create');
        }

        $branchId = session('branch_id');

        if (!$branchId || !Branch::query()->whereKey($branchId)->exists()) {
            $first = Branch::query()->orderBy('id')->first();
            session(['branch_id' => $first->id]);
        }

        return $next($request);
    }
}
