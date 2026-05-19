<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardStatsService;

/**
 * CashierController
 * 
 * Displays the cashier dashboard with today's sales, shift summary, and POS access.
 * Cashiers can only see their own sales; admins see all sales.
 */
class CashierController extends Controller
{
    /**
     * Show the cashier dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get today's recent sales for this cashier (limit 10)
        $todaySales = \App\Models\Order::query()
            ->forCompany($company->id)
            ->whereDate('created_at', today())
            ->where('user_id', $user->id) // filter by the authenticated cashier
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get latest orders using raw query (same as admin dashboard)
        $ordersQuery = "SELECT o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date, COUNT(oi.id) as items_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id";
        $bindings = [];
        if ($user && $user->company_id) {
            $ordersQuery .= " WHERE o.company_id = ?";
            $bindings[] = $user->company_id;
        }
        $ordersQuery .= " GROUP BY o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date ORDER BY o.created_at DESC LIMIT 10";
        $orders = DB::select($ordersQuery, $bindings);

        // use service for aggregated figures so logic is centralised
        $statsService = app(\App\Services\DashboardStatsService::class);
        $todayTotal = $statsService->salesToday($user->id);
        $todayCount = $statsService->countToday($user->id);

        // Get average transaction value
        $avgTransaction = $todayCount > 0 ? $todayTotal / $todayCount : 0;

        // use unified dashboard view and specify cashier layout
        $layout = 'layouts.cashier';
        return view('dashboard', compact(
            'layout',
            'company',
            'todaySales',
            'orders',
            'todayTotal',
            'todayCount',
            'avgTransaction'
        ));
    }

    /**
     * Show the POS (Point of Sale) screen
     */
    public function pos()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get all products for POS (filter by stock status)
        $products = $company->products()
            ->where('stock_status', 'instock')
            ->orderBy('name')
            ->get();

        // collect categories for filter dropdown
        $categories = $company->categories()->orderBy('name')->get();

        // use shared pos view with cashier layout
        $layout = 'layouts.cashier';
        return view('pos', compact('company', 'products', 'categories', 'layout'));
    }

    /**
     * List online orders for cashier to review
     * Uses same data format as admin dashboard for consistency
     */
    public function orders()
    {
        $user = Auth::user();
        $company = $user->company;

        // Use raw query to match admin dashboard approach and include items count
        $ordersQuery = "SELECT o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date, COUNT(oi.id) as items_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id";
        $bindings = [];
        
        // Filter by company and order type (web orders are online orders)
        $ordersQuery .= " WHERE o.company_id = ? AND o.order_type = 'web'";
        $bindings[] = $company->id;
        
        // Group and order results
        $ordersQuery .= " GROUP BY o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date ORDER BY o.created_at DESC";
        
        // Get paginated results using raw query
        $page = request()->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $countQuery = "SELECT COUNT(DISTINCT o.id) as total FROM orders o WHERE o.company_id = ? AND o.order_type = 'web'";
        $totalCount = DB::selectOne($countQuery, [$company->id])->total;
        
        $paginatedQuery = $ordersQuery . " LIMIT $offset, $perPage";
        $orders = collect(DB::select($paginatedQuery, $bindings));
        
        // Create paginator instance manually
        $scheme = request()->getScheme();
        $port = request()->getPort();
        $isNonStandardPort = ($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443);
        
        $routeName = $isNonStandardPort ? 'cashier.orders.fallback' : 'cashier.orders';
        
        $orders = new \Illuminate\Pagination\Paginator(
            $orders,
            $perPage,
            $page,
            ['path' => route($routeName, $isNonStandardPort ? [] : ['subdomain' => $user->company->slug]), 'query' => request()->query()]
        );
        $orders->setPath(route($routeName, $isNonStandardPort ? [] : ['subdomain' => $user->company->slug]));
        $orders->appends(request()->query());

        $layout = 'layouts.cashier';
        return view('admin.orders', compact('orders', 'layout'));
    }

    /**
     * Get today's shift summary
     * Returns JSON data for AJAX
     */
    public function shiftSummary()
    {
        $user = Auth::user();
        $company = $user->company;

        $summary = [
            'shift_start' => now()->startOfDay()->toTimeString(),
            'shift_end' => now()->endOfDay()->toTimeString(),
            'total_sales' => $company->orders()
                ->whereDate('created_at', today())
                ->where('user_id', $user->id)
                ->sum('total'),
            'sales_count' => $company->orders()
                ->whereDate('created_at', today())
                ->where('user_id', $user->id)
                ->count(),
            'items_sold' => DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.user_id', $user->id)
                ->whereDate('orders.created_at', today())
                ->sum('order_items.quantity'),
        ];

        return response()->json($summary);
    }
}
