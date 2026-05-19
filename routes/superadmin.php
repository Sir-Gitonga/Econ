<?php

use App\SuperAdmin\DashboardController;
use App\SuperAdmin\SuperAdminController;
use App\SuperAdmin\TenantController;
use App\SuperAdmin\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Routes for the Super Admin panel. These routes are protected by the
| superadmin middleware and are only accessible via admin.localhost.
|
*/

Route::middleware(['superadmin'])->prefix('superadmin')->group(function () {

    // Authentication routes (login/logout)
    Route::get('/login', [SuperAdminController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/login', [SuperAdminController::class, 'login'])->name('superadmin.login.post');

    // Protected routes
    Route::middleware(['auth:superadmin'])->group(function () {
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('superadmin.logout');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('superadmin.dashboard');

        // Tenant Management
        Route::resource('tenants', TenantController::class)->names([
            'index' => 'superadmin.tenants.index',
            'create' => 'superadmin.tenants.create',
            'store' => 'superadmin.tenants.store',
            'show' => 'superadmin.tenants.show',
            'edit' => 'superadmin.tenants.edit',
            'update' => 'superadmin.tenants.update',
            'destroy' => 'superadmin.tenants.destroy',
        ]);

        // Additional tenant actions
        Route::patch('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('superadmin.tenants.suspend');
        Route::patch('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('superadmin.tenants.activate');

        // Subscription Management
        Route::resource('subscriptions', SubscriptionController::class)->names([
            'index' => 'superadmin.subscriptions.index',
            'create' => 'superadmin.subscriptions.create',
            'store' => 'superadmin.subscriptions.store',
            'show' => 'superadmin.subscriptions.show',
            'edit' => 'superadmin.subscriptions.edit',
            'update' => 'superadmin.subscriptions.update',
            'destroy' => 'superadmin.subscriptions.destroy',
        ]);

        // Assign subscription to tenant
        Route::post('tenants/{tenant}/assign-subscription', [SubscriptionController::class, 'assignToTenant'])->name('superadmin.subscriptions.assign');
    });
});