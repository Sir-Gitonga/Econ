<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        // Defensive check: if on a tenant host, ensure the company exists before showing the register form
        $host = request()->getHost();
        $host = explode(':', $host)[0];
        $mainDomain = parse_url(config('app.url') ?? env('APP_URL', ''), PHP_URL_HOST) ?: 'softifyx.localhost';

        if ($host !== $mainDomain) {
            $subdomain = explode('.', $host)[0] ?? null;
            $company = Company::where('slug', $subdomain)->first();
            if (!$company) {
                abort(404, 'Company not found');
            }
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Determine current company (middleware may have bound it to the container)
        $company = app()->bound('company') ? app('company') : null;

        // Normalize host (strip port if present) before deriving subdomain/main-domain checks
        $host = $request->getHost();
        $host = explode(':', $host)[0]; // remove port when present

        // If middleware didn't set company, try deriving from the host using the slug
        if (!$company) {
            $subdomain = explode('.', $host)[0] ?? null;
            if ($subdomain) {
                $company = Company::where('slug', $subdomain)->first();
            }
        }

        // Determine if we're on the main domain
        $mainDomain = parse_url(config('app.url') ?? env('APP_URL', ''), PHP_URL_HOST) ?: 'softifyx.localhost';
        $isMainDomain = $host === $mainDomain;

        // If we're not on main domain and there's no company, reject
        if (!$isMainDomain && !$company) {
            return back()->withErrors(['domain' => 'Invalid company domain.']);
        }

        $user = User::create([
            'company_id' => $company?->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $isMainDomain ? 'admin' : 'user', // admin for main domain, user for subdomains
        ]);

        Auth::login($user);

        if ($isMainDomain) {
            // Admin registered on main domain - redirect to their company subdomain
            if ($company) {
                return redirect()->route('admin.dashboard', ['subdomain' => $company->slug])->with('success', 'Admin account created successfully!');
            }
            return back()->withErrors(['domain' => 'Company not found.']);
        } else {
            // User registered on a subdomain
            return redirect()->route('user.dashboard', ['subdomain' => $company->slug])->with('success', 'Account created successfully!');
        }
    }
}
