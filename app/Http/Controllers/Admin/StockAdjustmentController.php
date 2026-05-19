<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\Role::class . ':admin');
    }

    /**
     * Show inventory adjustment page
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        $products = Product::where('company_id', $companyId)
            ->select('id', 'name', 'sku', 'stock_quantity', 'stock_status', 'low_stock_threshold')
            ->orderBy('name')
            ->paginate(20);

        $movements = StockMovement::where('company_id', $companyId)
            ->with('product', 'user')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.inventory.adjust', compact('products', 'movements'));
    }

    /**
     * Get product details for quick adjustment
     */
    public function getProduct(Product $product)
    {
        if ($product->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'current_stock' => $product->stock_quantity,
            'status' => $product->stock_status,
            'low_threshold' => $product->low_stock_threshold,
        ]);
    }

    /**
     * Adjust product stock (manual correction)
     * 
     * Accepts JSON request:
     * {
     *   "product_id": 1,
     *   "quantity_change": 5,  (positive = add, negative = reduce)
     *   "type": "purchase|adjustment|return|damage_loss",
     *   "notes": "Physical count adjustment",
     *   "reference_id": "PO-123"  (optional)
     * }
     */
    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity_change' => 'required|integer',
            'type' => 'required|in:purchase,adjustment,return,damage_loss',
            'notes' => 'nullable|string|max:500',
            'reference_id' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $inventory = new InventoryService();
            $inventory->adjustStock(
                product_id: $product->id,
                quantityChange: $request->quantity_change,
                type: $request->type,
                referenceId: $request->reference_id ? (int) $request->reference_id : null,
                referenceType: 'manual_adjustment',
                notes: $request->notes
            );

            $product->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'current_stock' => $product->stock_quantity,
                    'status' => $product->stock_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk adjust stock levels
     * 
     * Accepts array of adjustments:
     * [
     *   {
     *     "product_id": 1,
     *     "new_quantity": 50,
     *     "notes": "Physical count"
     *   },
     *   ...
     * ]
     */
    public function bulkAdjust(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array|min:1',
            'adjustments.*.product_id' => 'required|integer|exists:products,id',
            'adjustments.*.new_quantity' => 'required|integer|min:0',
            'adjustments.*.notes' => 'nullable|string|max:500',
        ]);

        $companyId = Auth::user()->company_id;
        $results = [];
        $failed = [];

        try {
            DB::transaction(function () use ($request, $companyId, &$results, &$failed) {
                $inventory = new InventoryService();

                foreach ($request->adjustments as $adjustment) {
                    try {
                        $product = Product::where('id', $adjustment['product_id'])
                            ->where('company_id', $companyId)
                            ->firstOrFail();

                        $quantityChange = $adjustment['new_quantity'] - $product->stock_quantity;

                        if ($quantityChange !== 0) {
                            $inventory->adjustStock(
                                product_id: $product->id,
                                quantityChange: $quantityChange,
                                type: 'adjustment',
                                referenceType: 'bulk_adjustment',
                                notes: $adjustment['notes'] ?? 'Bulk stock adjustment'
                            );
                        }

                        $product->refresh();
                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'new_quantity' => $product->stock_quantity,
                            'status' => $product->stock_status,
                        ];
                    } catch (\Exception $e) {
                        $failed[] = [
                            'product_id' => $adjustment['product_id'],
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            });

            return response()->json([
                'success' => count($failed) === 0,
                'message' => count($failed) === 0 
                    ? 'All stocks adjusted successfully'
                    : 'Some adjustments failed. See details below.',
                'adjusted' => $results,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Set low stock threshold for a product
     */
    public function setThreshold(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'threshold' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $oldThreshold = $product->low_stock_threshold;
        $product->update(['low_stock_threshold' => $request->threshold]);

        // Recalculate status with new threshold
        $inventory = new InventoryService();
        $newStatus = $inventory->determineStatus($product->stock_quantity, $request->threshold);
        $product->update(['stock_status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Low stock threshold updated from $oldThreshold to {$request->threshold}",
            'product' => [
                'id' => $product->id,
                'threshold' => $product->low_stock_threshold,
                'status' => $product->stock_status,
            ],
        ]);
    }

    /**
     * Get stock movement history for a product
     */
    public function movementHistory(Product $product)
    {
        if ($product->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $movements = StockMovement::where('product_id', $product->id)
            ->where('company_id', Auth::user()->company_id)
            ->with('user')
            ->latest()
            ->paginate(50);

        return response()->json($movements);
    }

    /**
     * Export stock movement report
     */
    public function exportMovements(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = StockMovement::where('company_id', $companyId)->with('product', 'user');

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        // For CSV export
        $filename = 'stock-movements-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Product', 'Type', 'Quantity', 'Before', 'After', 'Changed By', 'Date', 'Notes']);
            
            // Data
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->product?->name ?? 'Unknown',
                    $movement->type,
                    $movement->quantity,
                    $movement->before_quantity,
                    $movement->after_quantity,
                    $movement->user?->name ?? 'System',
                    $movement->created_at->format('Y-m-d H:i:s'),
                    $movement->notes ?? '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show stock movement history page
     */
    public function history(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = StockMovement::where('company_id', $companyId)
            ->with('product', 'user');

        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->paginate(50);

        $products = Product::where('company_id', $companyId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.inventory.history', compact('movements', 'products'));
    }
}
