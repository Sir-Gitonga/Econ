<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     * If the request is on a tenant subdomain that does not exist, abort with 404.
     */
    public function showLoginForm(Request $request)
    {
        // Normalize host (strip port if present)
        $host = $request->getHost();
        $host = explode(':', $host)[0];

        $mainDomain = parse_url(config('app.url') ?? env('APP_URL', ''), PHP_URL_HOST) ?: 'localhost';

        // If this is a tenant host, ensure company exists
        if ($host !== $mainDomain) {
            $subdomain = explode('.', $host)[0] ?? null;
            $company = Company::where('slug', $subdomain)->first();
            if (!$company) {
                abort(404, 'Company not found');
            }
        }

        return view('auth.login');
    }

    /**
     * Handle post-authentication redirect.
     * If the user belongs to a company, redirect to that company's domain (tenant subdomain).
     * Fallback to the admin index route if no domain is configured.
     */
    protected $redirectTo = '/';

    protected function authenticated(Request $request, $user)
    {
        // Normalize host (strip port if present) before determining main vs subdomain
        $host = $request->getHost();
        $host = explode(':', $host)[0];
        $mainDomain = parse_url(config('app.url') ?? env('APP_URL', ''), PHP_URL_HOST) ?: 'localhost';
        $isMainDomain = $host === $mainDomain;

        if ($isMainDomain) {
            // On main domain, only admins can log in
            $isAdmin = ($user->role === 'admin') || (isset($user->utype) && strtoupper($user->utype) === 'ADM');
            if (!$isAdmin) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'This account does not have admin privileges.']);
            }

            // update last login
            $user->last_login = now();
            $user->save();

            // Redirect to their company's subdomain admin dashboard
            if ($user->company_id) {
                $company = Company::find($user->company_id);
                if ($company) {
                    return redirect()->route('admin.dashboard', ['subdomain' => $company->slug]);
                }
            }
            return redirect()->route('login')
                ->withErrors(['email' => 'No company associated with this account.']);
        } else {
            // On subdomain
            $subdomain = explode('.', $host)[0] ?? null;
            $company = Company::where('slug', $subdomain)->first();

            if (!$company || $user->company_id !== $company->id) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid account for this domain.']);
            }

            // update last_login
            $user->last_login = now();
            $user->save();

            $isAdmin = ($user->role === 'admin') || (isset($user->utype) && strtoupper($user->utype) === 'ADM');
            return $isAdmin
                ? redirect()->route('admin.dashboard', ['subdomain' => $subdomain])
                : redirect()->route('user.dashboard', ['subdomain' => $subdomain]);
        }
    }
}
