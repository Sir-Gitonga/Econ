<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('adminRoute')) {
    /**
     * Generate admin route URL with subdomain in domain
     * Includes port if on non-standard port (e.g., dev server on :8010)
     *
     * @param string $name Route name
     * @param array|mixed $parameters Route parameters
     * @return string Route URL
     */
    function adminRoute($name, $parameters = [])
    {
        // Ensure parameters is always an array
        if (!is_array($parameters)) {
            $parameters = [];
        }

        // Check if we're on a non-standard port (development environment)
        $scheme = Request::getScheme();
        $port = Request::getPort();
        $isNonStandardPort = ($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443);
        
        // For development with ports, use fallback routes that don't require subdomains
        if ($isNonStandardPort) {
            // Map subdomain route names to fallback route names
            $fallbackRoutes = [
                'admin.order.details' => 'admin.order.details.fallback',
                // Add other routes that need fallback here
            ];
            
            if (isset($fallbackRoutes[$name])) {
                $name = $fallbackRoutes[$name];
                // Remove subdomain parameter for fallback routes
                unset($parameters['subdomain']);
            }
        }

        // Try to get subdomain from authenticated user's company
        $user = Auth::user();
        $host = Request::getHost(); // Always get host for URL generation
        if ($user && $user->company) {
            $parameters['subdomain'] = $user->company->slug;
        } else {
            // Fallback to extracting subdomain from current request
            if (preg_match('/^([^.]+)\./', $host, $matches)) {
                $parameters['subdomain'] = $matches[1];
            }
        }

        // Generate relative URL path
        $path = route($name, $parameters, false);

        // If current request is on a non-standard port (e.g., dev server on :8010),
        // include it in the generated URL so clicks stay on the same server.
        
        if ($isNonStandardPort && $port) {
            // Generate full URL with explicit port
            return $scheme . '://' . $host . ':' . $port . $path;
        }

        return $path;
    }
}

/**
 * Generate cashier route URL with fallback for development
 *
 * @param string $name Route name
 * @param array|mixed $parameters Route parameters
 * @return string Route URL
 */
if (!function_exists('cashierRoute')) {
    function cashierRoute($name, $parameters = [])
    {
        // Ensure parameters is always an array
        if (!is_array($parameters)) {
            $parameters = [];
        }

        // Check if we're on a non-standard port (development environment)
        $scheme = Request::getScheme();
        $host = Request::getHost();
        $port = Request::getPort();
        $isNonStandardPort = ($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443);
        
        // For development with ports, use fallback routes that don't require subdomains
        if ($isNonStandardPort) {
            // Map subdomain route names to fallback route names
            $fallbackRoutes = [
                'cashier.dashboard' => 'cashier.dashboard.fallback',
                'cashier.pos' => 'cashier.pos.fallback',
                'cashier.shift-summary' => 'cashier.shift-summary.fallback',
                'cashier.orders' => 'cashier.orders.fallback',
                // Add other routes that need fallback here
            ];
            
            if (isset($fallbackRoutes[$name])) {
                $name = $fallbackRoutes[$name];
                // Remove subdomain parameter for fallback routes
                unset($parameters['subdomain']);
            }
        }

        // Try to get subdomain from authenticated user's company
        $user = Auth::user();
        $host = Request::getHost(); // Always get host for URL generation
        if ($user && $user->company) {
            $parameters['subdomain'] = $user->company->slug;
        } else {
            // Fallback to extracting subdomain from current request
            if (preg_match('/^([^.]+)\./', $host, $matches)) {
                $parameters['subdomain'] = $matches[1];
            }
        }

        // Generate relative URL path
        $path = route($name, $parameters, false);

        // If current request is on a non-standard port,
        // include it in the generated URL
        if ($isNonStandardPort && $port) {
            return $scheme . '://' . $host . ':' . $port . $path;
        }

        return $path;
    }
}

