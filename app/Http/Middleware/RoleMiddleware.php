<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 * 
 * Restricts route access based on user role.
 * Usage: middleware(['auth', 'role:admin,cashier'])
 * 
 * Allows access only if user's role matches one of the specified roles.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles Comma-separated list of allowed roles
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Split roles string into array
        $allowedRoles = array_map('trim', explode(',', $roles));
        $userRole = Auth::user()->getRoleName();

        // Check if user's role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            // Redirect to appropriate dashboard based on user role
            // Use named routes and correct admin-prefixed cashier route
            return match ($userRole) {
                'admin' => redirect()->route('admin.dashboard'),
                'cashier' => redirect()->route('admin.cashier.dashboard'),
                default => redirect()->route('dashboard'),
            };
        }

        return $next($request);
    }
}
