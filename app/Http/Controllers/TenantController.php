<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * TenantController
 *
 * Handles tenant-specific routes (subdomain only).
 * All data is automatically scoped to the current tenant by CompanyScope middleware.
 *
 * The app('company') contains the current tenant instance.
 * All queries to tenant-scoped models (Product, Category, etc.) are filtered by company_id.
 */
class TenantController extends Controller
{
    /**
     * Show tenant storefront/home page.
     * Company context is automatically available via app('company').
     * All queries are automatically scoped to this company via CompanyScope.
     */
    public function index()
    {
        $company = app('company');

        if (!$company) {
            Log::error('Company not found in IdentifyCompanyBySubdomain middleware');
            abort(404, 'Company not found');
        }

        // All these queries are automatically scoped to the current company
        // via the CompanyScope global scope applied in the model boot method
        $slides = Slide::where('status', 1)->take(3)->get();

        $categories = Category::orderBy('name')->get();

        $sproducts = Product::whereNotNull('sale_price')
            ->where('sale_price', '<>', '')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $fproducts = Product::where('featured', 1)->take(8)->get();

        return view('tenant.index', compact('company', 'slides', 'categories', 'sproducts', 'fproducts'));
    }

    /**
     * Show tenant dashboard.
     * Requires authentication. User must belong to this company.
     */
    public function dashboard()
    {
        $company = app('company');
        $user = auth()->user();

        // Verify user belongs to this company
        if (!$user || $user->company_id !== $company->id) {
            Log::warning('Unauthorized dashboard access', [
                'user_id' => $user?->id,
                'user_company_id' => $user?->company_id,
                'requested_company_id' => $company->id,
            ]);
            abort(403, 'Unauthorized');
        }

        // Get tenant statistics
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total'),
            'pending_orders' => Order::where('status', 'ordered')->count(),
        ];

        return view('tenant.dashboard', compact('company', 'stats', 'user'));
    }

    /**
     * Show tenant storefront with categories and products.
     */
    public function storefront()
    {
        $company = app('company');

        $categories = Category::with('products')->get();
        $products = Product::where('featured', 1)->take(12)->get();

        return view('tenant.storefront', compact('company', 'categories', 'products'));
    }

    /**
     * Show a single product.
     * Automatically scoped to current tenant.
     */
    public function showProduct($id)
    {
        $company = app('company');
        $product = Product::findOrFail($id);

        return view('tenant.product', compact('company', 'product'));
    }

    /**
     * Show tenant about page.
     */
    public function about()
    {
        $company = app('company');
        return view('tenant.about', compact('company'));
    }

    /**
     * Show tenant contact page.
     */
    public function contact()
    {
        $company = app('company');
        return view('tenant.contact', compact('company'));
    }
}
