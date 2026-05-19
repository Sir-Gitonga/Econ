<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InventoryDashboardController extends Controller
{
    /**
     * Get low stock widget data
     */
    public function lowStockWidget()
    {
        $companyId = Auth::user()->company_id;

        // Get products in low stock or out of stock
        $products = Product::where('company_id', $companyId)
            ->whereIn('stock_status', ['lowstock', 'outofstock'])
            ->select('id', 'name', 'stock_quantity', 'stock_status', 'low_stock_threshold', 'sku')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Count by status
        $stats = Product::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN stock_status = "outofstock" THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw('SUM(CASE WHEN stock_status = "lowstock" THEN 1 ELSE 0 END) as low_stock')
            ->first();

        return view('admin.inventory.low-stock-widget', [
            'products' => $products,
            'stats' => $stats,
        ]);
    }

    /**
     * Get inventory summary for admin dashboard
     */
    public function inventorySummary()
    {
        $companyId = Auth::user()->company_id;

        $summary = Product::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as total_products')
            ->selectRaw('SUM(stock_quantity) as total_stock')
            ->selectRaw('SUM(CASE WHEN stock_status = "instock" THEN 1 ELSE 0 END) as in_stock_count')
            ->selectRaw('SUM(CASE WHEN stock_status = "lowstock" THEN 1 ELSE 0 END) as low_stock_count')
            ->selectRaw('SUM(CASE WHEN stock_status = "outofstock" THEN 1 ELSE 0 END) as out_of_stock_count')
            ->first();

        return response()->json($summary);
    }
}
