<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();

/**
 * TENANT ROUTES (Subdomain-based multi-tenancy)
 *
 * Routes accessible only on tenant subdomains: {slug}.localhost
 * - Middleware resolves company by slug
 * - All queries auto-scoped by CompanyScope
 * - Prevents cross-tenant access
 */
Route::domain('{subdomain}.localhost')
    ->middleware(\App\Http\Middleware\IdentifyCompanyBySubdomain::class)
    ->group(function () {
        // Tenant home/storefront - uses existing views
        Route::get('/', [HomeController::class, 'index'])->name('tenant.index');
        Route::get('/shop',[ShopController::class, 'index'])->name('tenant.shop.index');
        Route::get('/shop/{product_slug}',[ShopController::class, 'product_details'])->name('tenant.shop.product.details');

        // Admin routes for tenant subdomain
        // POS route must be accessible by admin and cashier
        Route::middleware(['auth', \App\Http\Middleware\Role::class . ':admin|cashier'])->prefix('admin')->group(function() {
            Route::get('/pos', [AdminController::class, 'pos'])->name('admin.pos');
            Route::post('/pos/checkout', [AdminController::class, 'posCheckout'])->name('admin.pos.checkout');
        });

        Route::middleware(['auth', \App\Http\Middleware\Role::class . ':admin'])->prefix('admin')->group(function() {
            // Dashboard
            Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

            // Settings
            Route::get('/settings', [CompanySettingsController::class, 'index'])->name('admin.settings');
            Route::post('/settings/general', [CompanySettingsController::class, 'updateGeneral'])->name('admin.settings.update.general');
            Route::post('/settings/appearance', [CompanySettingsController::class, 'updateAppearance'])->name('admin.settings.update.appearance');
            Route::post('/settings/payment', [CompanySettingsController::class, 'updatePayment'])->name('admin.settings.update.payment');
            Route::post('/settings/business', [CompanySettingsController::class, 'updateBusiness'])->name('admin.settings.update.business');
            Route::post('/settings/communication', [CompanySettingsController::class, 'updateCommunication'])->name('admin.settings.update.communication');

            // SMS & WhatsApp settings (dynamic provider/gateway)
            Route::get('/settings/sms', [\App\Http\Controllers\Admin\Settings\SmsSettingsController::class, 'index'])->name('admin.settings.sms');
            Route::post('/settings/sms', [\App\Http\Controllers\Admin\Settings\SmsSettingsController::class, 'storeOrUpdate'])->name('admin.settings.sms.store');

            Route::get('/settings/whatsapp', [\App\Http\Controllers\Admin\Settings\WhatsappSettingsController::class, 'index'])->name('admin.settings.whatsapp');
            Route::post('/settings/whatsapp', [\App\Http\Controllers\Admin\Settings\WhatsappSettingsController::class, 'storeOrUpdate'])->name('admin.settings.whatsapp.store');

            // Brands
            Route::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
            Route::get('/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand.add');
            Route::post('/brand/store',[AdminController::class, 'brand_store'])->name('admin.brand.store');
            Route::get('/brand/edit/{id}',[AdminController::class, 'brand_edit'])->name('admin.brand.edit');
            Route::put('/brand/update/',[AdminController::class, 'brand_update'])->name('admin.brand.update');
            Route::delete('/brand/{id}/delete', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');

            // Categories
            Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
            Route::get('/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
            Route::post('/category/store',[AdminController::class, 'category_store'])->name('admin.category.store');
            Route::get('/category/{id}/edit',[AdminController::class, 'category_edit'])->name('admin.category.edit');
            Route::put('/category/update',[AdminController::class, 'category_update'])->name('admin.category.update');
            Route::delete('/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

            // Products
            Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
            Route::get('/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
            Route::post('/product/store',[AdminController::class, 'product_store'])->name('admin.product.store');
            Route::get('/product/{id}/edit',[AdminController::class, 'product_edit'])->name('admin.product.edit');
            Route::put('/product/update',[AdminController::class, 'product_update'])->name('admin.product.update');
            Route::delete('/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');

            // Coupons
            Route::get('/coupons',[AdminController::class, 'coupons'])->name('admin.coupons');
            Route::get('/coupon/add',[AdminController::class, 'coupon_add'])->name('admin.coupon.add');
            Route::post('/coupon/store',[AdminController::class, 'coupon_store'])->name('admin.coupon.store');
            Route::get('/coupon/{id}/edit',[AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
            Route::put('/coupon/update',[AdminController::class, 'coupon_update'])->name('admin.coupon.update');
            Route::delete('/coupon/{id}/delete', [AdminController::class, 'coupon_delete'])->name('admin.coupon.delete');

            // Orders
            Route::get('/orders',[AdminController::class, 'orders'])->name('admin.orders');
            Route::get('/order/{order_id}/details',[AdminController::class, 'order_details'])->name('admin.order.details');
            Route::put('/order/update_status',[AdminController::class, 'update_order_status'])->name('admin.order.status.update');

            // Slides
            Route::get('/slides',[AdminController::class,'slides'])->name('admin.slides');
            Route::get('/slide/add',[AdminController::class,'slide_add'])->name('admin.slide.add');
            Route::post('/slide/store',[AdminController::class,'slide_store'])->name('admin.slide.store');
            Route::get('/slide/{id}/edit',[AdminController::class,'slide_edit'])->name('admin.slide.edit');
            Route::put('/slide/update',[AdminController::class,'slide_update'])->name('admin.slide.update');
            Route::delete('/slide/{id}/delete', [AdminController::class,'slide_delete'])->name('admin.slide.delete');

            // Contacts
            Route::get('/contact',[AdminController::class, 'contacts'])->name('admin.contacts');
            Route::delete('/contact/{id}/delete', [AdminController::class, 'contact_delete'])->name('admin.contact.delete');



            // Cashier dashboard (accessible to cashier role)
            Route::middleware(['cashier'])->group(function() {
                Route::get('/cashier/dashboard', function(){ return view('cashier.dashboard'); })->name('admin.cashier.dashboard');
            });

            // Users (company-scoped)
            Route::resource('/users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');
            Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle_status');
            Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('admin.users.reset_password');

            // DEBUG helper - removed after tests
            Route::get('/debug/scoped-user/{id}', function ($id) {
                $found = \App\Models\User::find($id);
                $unscoped = \App\Models\User::withoutGlobalScopes()->find($id);
                $raw = \Illuminate\Support\Facades\DB::table('users')->where('id', $id)->first();
                $all = \Illuminate\Support\Facades\DB::table('users')->get();
                $count = \Illuminate\Support\Facades\DB::table('users')->count();
                return response()->json([
                    'requested_id' => $id,
                    'scoped_find' => $found ? $found->toArray() : null,
                    'unscoped_find' => $unscoped ? $unscoped->toArray() : null,
                    'raw' => $raw,
                    'all_users' => $all,
                    'company' => app()->has('company') ? app('company')->id : null,
                    'users_count' => $count,
                ]);
            })->name('admin.debug.scoped_user');

            Route::post('/pos/checkout', [AdminController::class, 'posCheckout'])->name('admin.pos.checkout');
        });

        // User dashboard routes on tenant subdomain
        Route::middleware(['auth'])->group(function() {
            Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.dashboard');
            Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
            Route::get('/account-profile', [UserController::class, 'profile'])->name('user.profile');
        });
    });


Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class, 'product_details'])->name('shop.product.details');

Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');

Route::post('/cart/apply-coupon',[CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');

Route::post('/wishlist/add',[WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [WishlistController::class,'empty_wishlist'])->name('wishlist.items.clear');
Route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move.to.cart');

Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-an-order',[CartController::class,'place_an_order'])->name('cart.place.an.order');
Route::get('/order-confirmation',[CartController::class,'order_confirmation'])->name('cart.order.confirmation');

Route::get('/contact-us',[Homecontroller::class,'contact'])->name('home.contact');
Route::post('/contact/store',[Homecontroller::class,'contact_store'])->name('home.contact.store');

Route::get('/search',[Homecontroller::class,'search'])->name('home.search');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
});






// User dashboard routes (for both main domain and subdomains)
Route::middleware(['auth'])->group(function() {
    Route::get('/account-dashboard', [App\Http\Controllers\UserController::class, 'index'])->name('user.dashboard');
    Route::get('/account-orders', [App\Http\Controllers\UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
});


Route::controller(PaymentController::class)
    ->prefix('payments')
    ->as('payments.')
    ->group(function () {
        Route::get('/token', 'token')->name('token');
    });


// COMPANY REGISTRATION ROUTES
Route::get('/register-company', [CompanyController::class, 'showRegisterForm'])->name('company.register');
Route::post('/register-company', [CompanyController::class, 'store'])->name('company.store');

// DEBUG: List all companies (remove in production)
Route::get('/debug/companies', function () {
    $companies = \App\Models\Company::all(['id', 'company_name', 'slug', 'domain', 'created_at']);
    return response()->json([
        'count' => $companies->count(),
        'companies' => $companies,
        'access_url' => 'http://{slug}.localhost:8000'
    ]);
});

// TEST HELPERS: Only registered during testing to simplify controller integration tests
if (app()->environment('testing')) {
    Route::prefix('_test')->group(function () {
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create']);
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit']);
        Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::post('/admin/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus']);
        Route::post('/admin/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword']);
        Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
    });
}
