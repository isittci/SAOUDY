<?php

// ============================================================================
// MIDDLEWARE HELPERS
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;



/**
 * Middleware to check role level.
 */
class CheckRoleLevel
{
    public function handle(Request $request, Closure $next, int $minimumLevel)
    {
        if (!auth()->check()) {
            abort(401, 'Non authentifié');
        }

        /**
         * @var User $user
         */
        $user = auth()->user();
        if (!$user->hasMinimumLevel($minimumLevel)) {
            abort(403, 'Niveau de rôle insuffisant');
        }

        return $next($request);
    }
}

