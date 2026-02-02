<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class VendorScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

    // Only apply scope if user is vendor or an admin for that vendor
    if ($user && in_array($user->role, ['vendor', 'vendor_admin', 'admin'])) {
            // Automatically add vendor_id scope
            \Illuminate\Database\Eloquent\Builder::macro('forVendor', function (Builder $builder) use ($user) {
                return $builder->where('vendor_id', $user->vendor_id);
            });


            // Share vendor globally (for controllers/views)
            view()->share('current_vendor_id', $user->vendor_id);
        };

        return $next($request);
    };
};
