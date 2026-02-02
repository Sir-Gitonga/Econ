<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * EnsureNotTenant Middleware
 *
 * Prevents tenant-specific routes from being accessed on subdomain.
 * Redirects subdomain requests to appropriate tenant routes.
 *
 * Used for: Landing page, pricing, features, public marketing pages
 * Should NOT be used for: Admin, customer dashboard, checkout
 */
class EnsureNotTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If on a tenant subdomain, redirect to appropriate tenant route
        if (app()->has('company') && app('company')) {
            // Redirect tenant subdomains to tenant dashboard or storefront
            return redirect()->route('tenant.dashboard');
        }

        // On main domain, allow access
        return $next($request);
    }
}
