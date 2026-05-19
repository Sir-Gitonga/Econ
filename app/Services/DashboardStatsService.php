<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardStatsService
{
    protected $companyId;

    public function __construct()
    {
        // Get company ID from the app container (set by IdentifyCompanyBySubdomain middleware)
        $company = app()->has('company') ? app('company') : null;
        $this->companyId = $company?->id;
    }

    /**
     * Get sales total for today. Optionally filter by cashier user ID.
     *
     * @param int|null $userId
     * @return float
     */
    public function salesToday(?int $userId = null): float
    {
        $query = Order::forCompany($this->companyId)
            ->whereDate('created_at', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->sum('total');
    }

    /**
     * Count of orders created today. Optionally filter by cashier user ID.
     *
     * @param int|null $userId
     * @return int
     */
    public function countToday(?int $userId = null): int
    {
        $query = Order::forCompany($this->companyId)
            ->whereDate('created_at', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->count();
    }

    public function topProducts(int $limit = 5)
    {
        return OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as sold')
            ->whereHas('order', fn($q)=> $q->where('company_id',$this->companyId))
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    public function lowStock(int $threshold = 10)
    {
        // If schema doesn't have stock_quantity (migration not run), only rely on stock_status flag
        $query = Product::forCompany($this->companyId);

        if (Schema::hasColumn('products', 'stock_quantity')) {
            return $query->where(function($q) use ($threshold) {
                $q->where('stock_status', 'lowstock')
                  ->orWhere('stock_quantity', '<', $threshold);
            })->get();
        }

        return $query->where('stock_status', 'lowstock')->get();
    }

    public function lowStockCount(): int
    {
        return Product::forCompany($this->companyId)
            ->where('stock_status', 'lowstock')
            ->count();
    }

    public function outOfStockCount(): int
    {
        return Product::forCompany($this->companyId)
            ->where('stock_status', 'outofstock')
            ->count();
    }

    public function cashierRanking(int $limit = 5)
    {
        return Order::forCompany($this->companyId)
            ->select('user_id')
            ->selectRaw('SUM(total) as revenue, COUNT(*) as txs')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->with('cashier')
            ->limit($limit)
            ->get();
    }

    // ----------------------------------------
    // new convenience helpers for dashboard
    // ----------------------------------------

    public function totalOrders(): int
    {
        return Order::forCompany($this->companyId)->count();
    }

    public function totalRevenue(): float
    {
        return Order::forCompany($this->companyId)->sum('total');
    }

    public function totalByStatus(string $status): int
    {
        return Order::forCompany($this->companyId)
            ->where('status', $status)
            ->count();
    }

    public function totalAmountByStatus(string $status): float
    {
        return Order::forCompany($this->companyId)
            ->where('status', $status)
            ->sum('total');
    }

    /**
     * Build the object structure that was previously calculated in controller
     * so other pieces of the app can consume the same shape.
     *
     * @return \stdClass
     */
    public function dashboardTotals(): \stdClass
    {
        return (object)[
            'TotalOrders' => $this->totalOrders(),
            'TotalAmount' => $this->totalRevenue(),
            'TotalOrdered' => $this->totalByStatus('ordered'),
            'TotalOrderedAmount' => $this->totalAmountByStatus('ordered'),
            'TotalDelivered' => $this->totalByStatus('delivered'),
            'TotalDeliveredAmount' => $this->totalAmountByStatus('delivered'),
            'TotalCanceled' => $this->totalByStatus('canceled'),
            'TotalCanceledAmount' => $this->totalAmountByStatus('canceled'),
        ];
    }
}
