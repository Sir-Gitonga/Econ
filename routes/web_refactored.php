<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/**
 * ============================================================================
 * MULTI-TENANT E-COMMERCE ROUTING ARCHITECTURE
 * ============================================================================
 *
 * STRUCTURE:
 * 1. Central Authentication - available everywhere
 * 2. Landing Page Routes - main domain only (no tenant context)
 * 3. Tenant Routes - subdomain only (with tenant context)
 * 4. Admin Routes - both main domain and subdomains
 * 5. API Routes - tenant-aware
 */

// Enable Laravel's built-in authentication routes (login, register, password reset)
Auth::routes();

// ============================================================================
// 1. CENTRAL AUTHENTICATION ROUTES
// ============================================================================
// These are available on both main domain and subdomains

// ============================================================================
// 2. LANDING PAGE ROUTES - MAIN DOMAIN ONLY
// ============================================================================
// These routes should NOT be accessible from tenant subdomains.
// Use EnsureNotTenant middleware to enforce this.

Route::middleware([\App\Http\Middleware\EnsureNotTenant::class])->group(function () {
    // Home / Landing Page
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');

    // Pricing & Features
    Route::get('/pricing', [App\Http\Controllers\LandingController::class, 'pricing'])->name('landing.pricing');
    Route::get('/features', [App\Http\Controllers\LandingController::class, 'features'])->name('landing.features');

    // Company Registration
    Route::get('/register-company', [App\Http\Controllers\CompanyController::class, 'showRegisterForm'])->name('company.register');
    Route::post('/register-company', [App\Http\Controllers\CompanyController::class, 'store'])->name('company.store');

    // Support / Contact (optional, can be on both)
    Route::get('/contact-us', [App\Http\Controllers\HomeController::class, 'contact'])->name('home.contact');
    Route::post('/contact/store', [App\Http\Controllers\HomeController::class, 'contact_store'])->name('home.contact.store');
});

// ============================================================================
// 3. PUBLIC STOREFRONT ROUTES - AVAILABLE ON ALL DOMAINS
// ============================================================================
// These work on both main domain and tenant subdomains
// The views determine what to display based on context

Route::get('/shop', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [App\Http\Controllers\ShopController::class, 'product_details'])->name('shop.product.details');
Route::get('/search', [App\Http\Controllers\HomeController::class, 'search'])->name('home.search');

// ============================================================================
// 4. SHOPPING CART & WISHLIST ROUTES - AVAILABLE ON ALL DOMAINS
// ============================================================================

Route::middleware(['auth'])->group(function () {
    // Cart
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add_to_cart'])->name('cart.add');
    Route::put('/cart/increase-quantity/{rowId}', [App\Http\Controllers\CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
    Route::put('/cart/decrease-quantity/{rowId}', [App\Http\Controllers\CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
    Route::delete('/cart/remove/{rowId}', [App\Http\Controllers\CartController::class, 'remove_item'])->name('cart.item.remove');
    Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'empty_cart'])->name('cart.empty');
    Route::post('/cart/apply-coupon', [App\Http\Controllers\CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');

    // Wishlist
    Route::post('/wishlist/add', [App\Http\Controllers\WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/item/remove/{rowId}', [App\Http\Controllers\WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
    Route::delete('/wishlist/clear', [App\Http\Controllers\WishlistController::class, 'empty_wishlist'])->name('wishlist.items.clear');
    Route::post('/wishlist/move-to-cart/{rowId}', [App\Http\Controllers\WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

    // Checkout
    Route::get('/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/place-an-order', [App\Http\Controllers\CartController::class, 'place_an_order'])->name('cart.place.an.order');
    Route::get('/order-confirmation', [App\Http\Controllers\CartController::class, 'order_confirmation'])->name('cart.order.confirmation');
});

// ============================================================================
// 5. CUSTOMER DASHBOARD & ACCOUNT ROUTES
// ============================================================================
// Available to authenticated users

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/account-orders', [App\Http\Controllers\UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [App\Http\Controllers\UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [App\Http\Controllers\UserController::class, 'order_cancel'])->name('user.order.cancel');
    Route::get('/account-profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
    Route::put('/account-profile/update', [App\Http\Controllers\UserController::class, 'update_profile'])->name('user.profile.update');
});

// ============================================================================
// 6. ADMIN PANEL ROUTES - TENANT SCOPED
// ============================================================================
// These routes are ONLY for tenant admins managing their company.
// Each admin can only see/modify data for their own company.
//
// Access: Only users with role='admin' and a valid company_id
// Scope: All queries automatically filtered by company_id via CompanyScope

Route::middleware(['auth', \App\Http\Middleware\Role::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    // ========== BRANDS ==========
    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'brands'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'brand_add'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'brand_store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\AdminController::class, 'brand_edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AdminController::class, 'brand_update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'brand_delete'])->name('delete');
    });

    // ========== CATEGORIES ==========
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'categories'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'category_add'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'category_store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\AdminController::class, 'category_edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AdminController::class, 'category_update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'category_delete'])->name('delete');
    });

    // ========== PRODUCTS ==========
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'products'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'product_add'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'product_store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\AdminController::class, 'product_edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AdminController::class, 'product_update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'product_delete'])->name('delete');
    });

    // ========== COUPONS ==========
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'coupons'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'coupon_add'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'coupon_store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\AdminController::class, 'coupon_edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AdminController::class, 'coupon_update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'coupon_delete'])->name('delete');
    });

    // ========== ORDERS ==========
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'orders'])->name('index');
        Route::get('/{order_id}', [App\Http\Controllers\AdminController::class, 'order_details'])->name('show');
        Route::put('/{order_id}/status', [App\Http\Controllers\AdminController::class, 'update_order_status'])->name('update_status');
    });

    // ========== SLIDES / BANNERS ==========
    Route::prefix('slides')->name('slides.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'slides'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'slide_add'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'slide_store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\AdminController::class, 'slide_edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AdminController::class, 'slide_update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'slide_delete'])->name('delete');
    });

    // ========== CONTACTS / INQUIRIES ==========
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'contacts'])->name('index');
        Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'contact_delete'])->name('delete');
    });

    // ========== POS (POINT OF SALE) ==========
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'pos'])->name('index');
        Route::post('/checkout', [App\Http\Controllers\AdminController::class, 'posCheckout'])->name('checkout');
    });
});

// ============================================================================
// 7. PAYMENT ROUTES
// ============================================================================

Route::prefix('payments')->name('payments.')->controller(App\Http\Controllers\PaymentController::class)->group(function () {
    Route::get('/token', 'token')->name('token');
});

// ============================================================================
// 8. TENANT-SPECIFIC ROUTES (SUBDOMAINS ONLY)
// ============================================================================
// These routes are ONLY accessible from tenant subdomains.
// Use subdomain patterns to match: {slug}.example.com

Route::middleware([\App\Http\Middleware\IdentifyCompanyBySubdomain::class, \App\Http\Middleware\EnsureTenant::class])->group(function () {
    // Tenant Dashboard / Storefront
    Route::get('/', [App\Http\Controllers\TenantController::class, 'index'])->name('tenant.index');
    Route::get('/dashboard', [App\Http\Controllers\TenantController::class, 'dashboard'])->name('tenant.dashboard');
    Route::get('/storefront', [App\Http\Controllers\TenantController::class, 'storefront'])->name('tenant.storefront');
});

// ============================================================================
// FALLBACK - 404
// ============================================================================
// Keep this at the end to catch all unmatched routes
