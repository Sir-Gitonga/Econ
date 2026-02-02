<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isAdmin = (isset($user->role) && $user->role === 'admin') || (isset($user->utype) && strtoupper($user->utype) === 'ADM');
            if ($isAdmin) {
                return $next($request);
            }

            Session::flush();
            return redirect()->route('login')->with('message', 'You are not authorized to access this page.');
        }

        return redirect()->route('login')->with('message', 'You need to login first.');
    }
}
