<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Role Middleware
 *
 * Checks if the authenticated user has one of the required roles.
 *
 * Usage:
 * - Route::middleware(Role::class . ':admin')
 * - Route::middleware(Role::class . ':admin|staff')
 * - Route::middleware(Role::class . ':admin|staff|customer')
 */
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Comma-separated roles: 'admin' or 'admin|staff'
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // User must be authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Normalize roles array
        $roles = $this->normalizeRoles($roles);

// use User helper to handle normalization
        $userRole = $request->user()->getRoleName() ?? null;

        // Check if user's role matches any required role
        if ($userRole && in_array($userRole, $roles)) {
            return $next($request);
        }

        // Unauthorized
        return redirect('/')->with('error', 'Unauthorized. Your role does not have access to this resource.');
    }

    /**
     * Normalize roles from middleware parameter.
     *
     * Converts:
     * - ['admin'] -> ['admin']
     * - ['admin|staff'] -> ['admin', 'staff']
     * - ['admin', 'staff'] -> ['admin', 'staff']
     */
    private function normalizeRoles(array $roles): array
    {
        $normalized = [];

        foreach ($roles as $role) {
            if (is_string($role) && strpos($role, '|') !== false) {
                // Split pipe-separated roles
                $normalized = array_merge($normalized, explode('|', $role));
            } else {
                $normalized[] = $role;
            }
        }

        return array_filter(array_unique($normalized));
    }
}

