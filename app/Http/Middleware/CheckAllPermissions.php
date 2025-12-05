<?php

// ============================================================================
// MIDDLEWARE HELPERS
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check if user has all of the specified permissions.
 */
class CheckAllPermissions
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        if (!auth()->check()) {
            abort(401, 'Non authentifié');
        }

        /**
         * @var User $user
         */
        $user = auth()->user();
        if (!$user->hasAllPermissions($permissions)) {
            abort(403, 'Action non autorisée. Toutes les permissions sont requises: ' . implode(', ', $permissions));
        }

        return $next($request);
    }
}



