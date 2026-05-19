<?php

namespace App\SuperAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request is coming from admin.localhost
        $host = $request->getHost();
        if ($host !== 'admin.localhost') {
            abort(403, 'Access denied. Super Admin panel is only accessible via admin.localhost');
        }

        // Skip auth check for login routes
        if ($request->route() && in_array($request->route()->getName(), ['superadmin.login', 'superadmin.login.post'])) {
            return $next($request);
        }

        // Check if user is authenticated as superadmin
        if (!Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.login');
        }

        return $next($request);
    }
}