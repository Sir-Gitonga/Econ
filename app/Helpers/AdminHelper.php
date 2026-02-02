<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('adminRoute')) {
    /**
     * Generate admin route URL with subdomain in domain
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

        // Try to get subdomain from authenticated user's company
        $user = Auth::user();
        if ($user && $user->company) {
            $parameters['subdomain'] = $user->company->slug;
        } else {
            // Fallback to extracting subdomain from current request
            $host = Request::getHost();
            if (preg_match('/^([^.]+)\./', $host, $matches)) {
                $parameters['subdomain'] = $matches[1];
            }
        }

        return route($name, $parameters);
    }
}

