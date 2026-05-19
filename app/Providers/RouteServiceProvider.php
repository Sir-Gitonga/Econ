<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * ============================================================
     * MULTI-TENANT ROUTE MODEL BINDING
     * ============================================================
     * 
     * This provider configures explicit model binding with
     * automatic tenant-scoping for subdomain-based multi-tenancy.
     *
     * For subdomain routes, we must ensure that:
     * 1. Models are resolved only within current tenant context
     * 2. Cross-tenant access attempts return 404 (not 403)
     * 3. No manual tenant checks needed in controllers
     * 4. CompanyScope() global scope filters all queries
     *
     * The tenant company is resolved by IdentifyCompanyBySubdomain
     * middleware BEFORE route model binding executes.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        /**
         * IMPLICIT ROUTE MODEL BINDING (Laravel 11 best practice)
         * ============================================================
         * 
         * Route model binding for {user} is now handled by:
         * 
         * 1. Implicit Binding: Laravel auto-resolves {user} via type-hinting
         *    in controller: public function edit(User $user)
         * 
         * 2. CompanyScope Global Scope: The User model automatically filters
         *    all queries to current tenant via CompanyScope global scope
         * 
         * 3. scopeBindings() in routes/web.php: Enables this integration
         * 
         * Flow:
         * - Request: GET {subdomain}.localhost/admin/users/3/edit
         * - IdentifyCompanyBySubdomain middleware sets app('company')
         * - Route parameter {user} is implicitly bound to User::findOrFail(3)
         * - CompanyScope adds WHERE company_id = app('company')->id automatically
         * - Controller receives User instance OR 404 is thrown (never a string)
         * - Cross-tenant access impossible (404 on not found in current tenant)
         * 
         * This is the Laravel 11 convention for multi-tenant scoped bindings.
         * No explicit Route::bind() closure needed - CompanyScope handles it.
         */

        /**
         * Explicit Route Model Binding for Payment
         * (Existing binding, kept for compatibility)
         */
        Route::bind('payment', function ($value) {
            return Payment::findOrFail($value);
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
