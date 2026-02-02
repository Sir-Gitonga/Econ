<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register a helper to generate routes with subdomain parameter if needed
        if (!function_exists('adminRoute')) {
            function adminRoute($name, $parameters = []) {
                if (app()->has('company') && !isset($parameters['subdomain'])) {
                    $parameters['subdomain'] = app('company')->slug;
                }
                return route($name, $parameters);
            }
        }
    }
}
