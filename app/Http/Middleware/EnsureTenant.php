<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * EnsureTenant Middleware
 *
 * Ensures that a route is ONLY accessible from a tenant subdomain.
 * Redirects main domain requests to the main domain version of the page.
 *
 * Used for: Tenant dashboard, tenant storefront, admin panel, checkout
 * Should NOT be used for: Landing page, pricing, features
 */
class EnsureTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If NOT on a tenant subdomain, redirect to main site
        if (!app()->has('company') || !app('company')) {
            return redirect()->route('home.index');
        }

        // On tenant subdomain, allow access
        return $next($request);
    }
}
