<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LoginResponse
 * 
 * Custom login response that redirects users based on their role.
 * admin -> /admin/dashboard
 * cashier -> /cashier/dashboard
 * user -> /dashboard
 */
class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        $user = Auth::user();
        
        // Determine redirect based on user's role using named routes
        return match ($user->getRoleName()) {
            'admin' => redirect()->route('admin.dashboard'),
            'cashier' => redirect()->route('admin.cashier.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
