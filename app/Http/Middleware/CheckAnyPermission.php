<?php

// ============================================================================
// MIDDLEWARE HELPERS
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;



/**
 * Middleware to check if user has any of the specified permissions.
 */
class CheckAnyPermission
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

        if (!$user->hasAnyPermission($permissions)) {
            abort(403, 'Action non autorisée. Permissions requises: ' . implode(', ', $permissions));
        }

        return $next($request);
    }
}
