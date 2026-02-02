<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthVendor
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'vendor') {
            return $next($request);
        }
        return redirect('/'); // redirect unauthorized users
    }
}
