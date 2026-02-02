<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;

class IdentifyCompany
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = explode('.', $request->getHost())[0];

        $company = Company::where('slug', $subdomain)->first();

        if (! $company) {
            abort(404, 'Company not found.');
        }

        // Share globally so any controller/view can access it
        app()->instance('company', $company);
        view()->share('company', $company);

        return $next($request);
    }
}
