<?php

// ============================================================================
// MIDDLEWARE HELPERS
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check if user has a specific permission.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            abort(401, 'Non authentifié');
        }

        /**
         * @var User $user
         */
        $user = auth()->user();


        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Action non autorisée. Permission requise: ' . $permission);
        }

        return $next($request);
    }
}
