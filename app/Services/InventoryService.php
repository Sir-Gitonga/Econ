<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\StockMovement;

class InventoryService
{
    protected ?int $companyId;
    protected ?int $userId;

    public function __construct()
    {
        $company = app()->has('company') ? app('company') : null;
        $this->companyId = $company?->id;
        $this->userId = Auth::id();
    }

    /**
     * Deduct quantity from product and record movement.
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $referenceId
     * @param string|null $referenceType
     * @throws \Exception when stock insufficient
     */
    public function deductStock(int $productId, int $quantity, ?int $referenceId = null, ?string $referenceType = null): void
    {
        DB::transaction(function () use ($productId, $quantity, $referenceId, $referenceType) {
            $product = Product::where('id', $productId)
                ->where('company_id', $this->companyId)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $product->stock_quantity;
            if ($before < $quantity) {
                throw new \Exception('Insufficient stock for product ' . $product->id);
            }

            $product->stock_quantity = $before - $quantity;
            $product->stock_status = $this->determineStatus($product->stock_quantity, $product->low_stock_threshold);
            $product->save();

            StockMovement::create([
                'company_id' => $this->companyId,
                'product_id' => $product->id,
                'created_by' => $this->userId,
                'type' => 'sale',
                'quantity' => -$quantity,
                'before_quantity' => $before,
                'after_quantity' => $product->stock_quantity,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
            ]);
        });
    }

    /**
     * Restore stock back to product (useful for returns, cancellations).
     */
    public function restoreStock(int $productId, int $quantity, ?int $referenceId = null, ?string $referenceType = null): void
    {
        DB::transaction(function () use ($productId, $quantity, $referenceId, $referenceType) {
            $product = Product::where('id', $productId)
                ->where('company_id', $this->companyId)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $product->stock_quantity;
            $product->stock_quantity = $before + $quantity;
            $product->stock_status = $this->determineStatus($product->stock_quantity, $product->low_stock_threshold);
            $product->save();

            StockMovement::create([
                'company_id' => $this->companyId,
                'product_id' => $product->id,
                'created_by' => $this->userId,
                'type' => 'return',
                'quantity' => $quantity,
                'before_quantity' => $before,
                'after_quantity' => $product->stock_quantity,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
            ]);
        });
    }

    /**
     * Calculate stock status string based on quantity and threshold
     */
    public function determineStatus(int $quantity, int $lowThreshold): string
    {
        if ($quantity <= 0) {
            return 'outofstock';
        }
        if ($quantity <= $lowThreshold) {
            return 'lowstock';
        }
        return 'instock';
    }

    /**
     * Check if product has sufficient stock to deduct
     */
    public function canDeduct(int $productId, int $quantity): bool
    {
        $product = Product::where('id', $productId)
            ->where('company_id', $this->companyId)
            ->select('stock_quantity')
            ->first();

        return $product && $product->stock_quantity >= $quantity;
    }

    /**
     * Get current stock status for a product
     */
    public function getStockStatus(int $productId): ?string
    {
        $product = Product::where('id', $productId)
            ->where('company_id', $this->companyId)
            ->select('stock_status', 'stock_quantity', 'low_stock_threshold')
            ->first();

        if (!$product) {
            return null;
        }

        return $this->determineStatus($product->stock_quantity, $product->low_stock_threshold);
    }

    /**
     * Get current stock quantity for a product
     */
    public function getProductStock(int $productId): int
    {
        $product = Product::where('id', $productId)
            ->where('company_id', $this->companyId)
            ->select('stock_quantity')
            ->first();

        return $product?->stock_quantity ?? 0;
    }

    /**
     * Manually adjust stock (for inventory counts, corrections, restock purchases)
     *
     * @param int $productId
     * @param int $quantityChange (positive for increase, negative for decrease)
     * @param string $type ('purchase', 'adjustment', 'manual_correction', etc)
     * @param int|null $referenceId
     * @param string|null $referenceType
     * @param string|null $notes
     * @throws \Exception when product not found or company mismatch
     */
    public function adjustStock(
        int $productId,
        int $quantityChange,
        string $type = 'adjustment',
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($productId, $quantityChange, $type, $referenceId, $referenceType, $notes) {
            $product = Product::where('id', $productId)
                ->where('company_id', $this->companyId)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $product->stock_quantity;
            $product->stock_quantity = max(0, $before + $quantityChange);
            $product->stock_status = $this->determineStatus($product->stock_quantity, $product->low_stock_threshold);
            $product->save();

            StockMovement::create([
                'company_id' => $this->companyId,
                'product_id' => $product->id,
                'created_by' => $this->userId,
                'type' => $type,
                'quantity' => $quantityChange,
                'before_quantity' => $before,
                'after_quantity' => $product->stock_quantity,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'notes' => $notes,
            ]);
        });
    }
}
