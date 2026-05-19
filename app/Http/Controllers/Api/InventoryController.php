<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Get real-time stock status for a product
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductStock(Product $product)
    {
        // Verify product belongs to user's company
        $user = Auth::user();
        if (!$user || $product->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'stock_quantity' => $product->stock_quantity,
            'stock_status' => $product->stock_status,
            'low_stock_threshold' => $product->low_stock_threshold,
            'can_sell' => $product->stock_status !== 'outofstock',
        ]);
    }

    /**
     * Get stock status for multiple products (for bulk POS updates)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMultipleProductStock()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $companyId = $user->company_id;
        $productIds = request()->input('ids', []);

        if (empty($productIds)) {
            return response()->json(['error' => 'No product IDs provided'], 422);
        }

        $products = Product::where('company_id', $companyId)
            ->whereIn('id', $productIds)
            ->select('id', 'stock_quantity', 'stock_status', 'low_stock_threshold')
            ->get();

        $data = $products->map(fn($p) => [
            'id' => $p->id,
            'stock_quantity' => $p->stock_quantity,
            'stock_status' => $p->stock_status,
            'can_sell' => $p->stock_status !== 'outofstock',
        ])->keyBy('id');

        return response()->json($data);
    }

    /**
     * Get low stock products for admin notification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowStockProducts()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $companyId = $user->company_id;

        $products = Product::where('company_id', $companyId)
            ->whereIn('stock_status', ['lowstock', 'outofstock'])
            ->select('id', 'name', 'stock_quantity', 'stock_status', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->limit(50)
            ->get();

        return response()->json([
            'total' => $products->count(),
            'low_stock' => $products->where('stock_status', 'lowstock')->count(),
            'out_of_stock' => $products->where('stock_status', 'outofstock')->count(),
            'products' => $products,
        ]);
    }
}
