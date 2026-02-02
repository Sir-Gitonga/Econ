<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'cashier') {
            return redirect('/')->with('error', 'Unauthorized: Cashier access required');
        }

        return $next($request);
    }
}
