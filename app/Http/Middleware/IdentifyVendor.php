<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use App\Services\VendorManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVendor
{
    /**
     * Handle an incoming request.
     *
     * This middleware identifies which tenant (vendor) is making the request
     * by checking the subdomain, loads their database configuration,
     * and sets up a dynamic DB connection for that tenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1️ Get the host (e.g. vendor1.yourdomain.com)
        $host = $request->getHost();

        // 2️ Extract the subdomain part (e.g. vendor1)
        $parts = explode('.', $host);
        $subdomain = $parts[0] ?? null;

        // 3️ Ignore requests to main domain (e.g. www.yourdomain.com)
        if ($subdomain && !in_array($subdomain, ['www', 'main', 'app'])) {
            // 4️ Find vendor with this subdomain
            $vendor = Vendor::where('subdomain', $subdomain)->first();

            if ($vendor) {
                // 5️ Use the VendorManager to add vendor connection dynamically
                $VendorManager = app(VendorManager::class);
                $VendorManager->addVendorConnection(
                    $vendor->database,
                    $vendor->db_username,
                    $vendor->db_password
                );

                // 6️ Make vendor info available globally in the app
                app()->instance('currentVendor', $vendor);
                Config::set('vendor.active', $vendor->toArray());
            } else {
                // If no vendor found for subdomain, abort
                abort(404, "Vendor workspace not found.");
            }
        }

        // 7️ Proceed with request
        return $next($request);
    }
}
