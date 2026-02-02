<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

/**
 * IdentifyCompanyBySubdomain Middleware
 *
 * Detects the current tenant (company) from the subdomain.
 * - main domain (example.com) = Landing page, no tenant context
 * - subdomain (company.example.com) = Tenant context, load company
 *
 * Rules:
 * - Main domain shows landing page only
 * - Tenant subdomains load company context
 * - Cross-tenant access is blocked
 */
class IdentifyCompanyBySubdomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $this->normalizeHost($request->getHost());
        $mainDomain = $this->getMainDomain();

        Log::info('Middleware: Host and Domain', [
            'raw_host' => $request->getHost(),
            'normalized_host' => $host,
            'main_domain' => $mainDomain,
        ]);

        // If a company instance is already bound in the application container, skip lookup
        // This allows tests and programmatic requests to set the company context directly.
        if (app()->has('company') && app('company')) {
            view()->share('company', app('company'));
            return $next($request);
        }

        // If accessing main domain, no tenant context needed
        if ($this->isMainDomain($host, $mainDomain)) {
            // Do not set company instance for main domain
            return $next($request);
        }

        // Extract subdomain candidate
        $subdomain = $this->extractSubdomain($host, $mainDomain);

        Log::info('Middleware: Subdomain Extraction', [
            'host' => $host,
            'main_domain' => $mainDomain,
            'extracted_subdomain' => $subdomain,
        ]);

        if (!$subdomain) {
            // Invalid host format, treat as main domain
            return $next($request);
        }

        // Try to find company by slug
        $company = Company::where('slug', $subdomain)->first();

        if (!$company) {
            Log::warning('Company not found for subdomain', [
                'subdomain' => $subdomain,
                'host' => $host,
            ]);
            // Return a custom 404 view with subdomain info
            return response()->view('errors.404', ['subdomain' => $subdomain], 404);
        }

        // Store company in app container for use throughout request
        app()->instance('company', $company);
        view()->share('company', $company);

        // If user is authenticated, verify they belong to this company
        if ($request->user()) {
            $this->validateUserCompanyAccess($request->user(), $company);
        }

        Log::info('Company identified from subdomain', [
            'company_id' => $company->id,
            'company_slug' => $company->slug,
            'host' => $host,
        ]);

        return $next($request);
    }

    /**
     * Remove port from host if present.
     */
    private function normalizeHost(string $host): string
    {
        return explode(':', $host)[0];
    }

    /**
     * Get main domain from config.
     * Returns: localhost (from APP_URL) or defaults to localhost
     */
    private function getMainDomain(): string
    {
        // Parse APP_URL to extract the domain
        $appUrl = config('app.url') ?? env('APP_URL', 'http://localhost');
        $mainDomain = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
        return $mainDomain;
    }

    /**
     * Check if host is the main domain.
     */
    private function isMainDomain(string $host, string $mainDomain): bool
    {
        return $host === $mainDomain;
    }

    /**
     * Extract subdomain from host.
     *
     * Examples for main domain "softifyx.localhost":
     * - "test.softifyx.localhost" → "test"
     * - "acme-store.softifyx.localhost" → "acme-store"
     * - "softifyx.localhost" → null (no subdomain)
     */
    private function extractSubdomain(string $host, string $mainDomain): ?string
    {
        // If host IS the main domain, no subdomain
        if ($host === $mainDomain) {
            return null;
        }

        // Check if this is a subdomain of the main domain
        $pattern = '.' . $mainDomain;

        if (!str_ends_with($host, $pattern)) {
            // Not a subdomain of our main domain
            return null;
        }

        // Extract just the subdomain part
        $subdomain = substr($host, 0, -strlen($pattern));

        return $subdomain ?: null;
    }

    /**
     * Verify that the authenticated user belongs to the company.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function validateUserCompanyAccess($user, Company $company): void
    {
        if ($user->company_id !== $company->id) {
            Log::warning('Unauthorized cross-tenant access attempt', [
                'user_id' => $user->id,
                'user_company_id' => $user->company_id,
                'requested_company_id' => $company->id,
            ]);

            auth('web')->logout();
            abort(403, 'Unauthorized. You do not belong to this company.');
        }
    }
}

