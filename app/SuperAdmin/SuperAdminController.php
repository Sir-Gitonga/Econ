<?php

namespace App\SuperAdmin;

use App\SuperAdmin\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class SuperAdminController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('superadmin.auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('superadmin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('superadmin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout the super admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }
}