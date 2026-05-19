<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Process full order refund and restore all inventory
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundOrder(Order $order)
    {
        // Verify authorization and company ownership
        if ($order->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($order->refunded_at !== null) {
            return response()->json(['error' => 'Order already refunded'], 422);
        }

        try {
            $this->processFullRefund($order);

            return response()->json([
                'success' => true,
                'message' => 'Order refunded successfully',
                'order' => $order->fresh()->load('items'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Refund failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process partial refund for specific line items
     *
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function partialRefund(Order $order, Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Verify authorization and company ownership
        if ($order->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $this->processPartialRefund($order, $request->input('items'));

            return response()->json([
                'success' => true,
                'message' => 'Partial refund processed successfully',
                'order' => $order->fresh()->load('items'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Partial refund failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process full refund within a transaction
     */
    private function processFullRefund(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $inventoryService = new InventoryService();

            foreach ($order->items as $item) {
                // Restore stock to product
                $inventoryService->restoreStock(
                    $item->product_id,
                    $item->quantity,
                    $order->id,
                    'order'
                );

                // Mark item as refunded
                $item->update(['refunded_quantity' => $item->quantity]);
            }

            // Mark entire order as refunded
            $order->update([
                'refunded_at' => now(),
                'refunded_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Process partial refund within a transaction
     */
    private function processPartialRefund(Order $order, array $items): void
    {
        DB::transaction(function () use ($order, $items) {
            $inventoryService = new InventoryService();

            foreach ($items as $refundItem) {
                $orderItem = $order->items()->findOrFail($refundItem['order_item_id']);
                $refundQty = $refundItem['quantity'];

                // Validate requested refund quantity
                if ($refundQty > $orderItem->quantity) {
                    throw new \Exception("Cannot refund more than ordered quantity");
                }

                // Restore stock to product
                $inventoryService->restoreStock(
                    $orderItem->product_id,
                    $refundQty,
                    $order->id,
                    'order_partial'
                );

                // Update refunded quantity
                $currentRefunded = $orderItem->refunded_quantity ?? 0;
                $orderItem->update([
                    'refunded_quantity' => $currentRefunded + $refundQty,
                ]);
            }
        });
    }
}
