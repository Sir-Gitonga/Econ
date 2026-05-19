<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\StockMovement;
use App\Services\InventoryService;

class CheckoutService
{
    protected $companyId;
    protected $userId;

    public function __construct()
    {
        // Get company ID from the app container (set by IdentifyCompanyBySubdomain middleware)
        $company = app()->has('company') ? app('company') : null;
        $this->companyId = $company?->id;
        $this->userId = Auth::id();
    }

    public function process(array $cart, string $paymentMethod, float $cashReceived = 0, float $discountPercent = 0, float $discountFixed = 0)
    {
        return DB::transaction(function () use ($cart, $paymentMethod, $cashReceived, $discountPercent, $discountFixed) {
            $items = collect($cart);

            $productIds = $items->pluck('id')->all();
            $products = Product::whereIn('id', $productIds)
                ->where('company_id', $this->companyId)
                ->lockForUpdate()
                ->get()->keyBy('id');

            if ($products->count() !== count($productIds)) {
                throw new \Exception('Invalid product in cart');
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $p = $products[$item['id']];
                $unitPrice = $p->sale_price ?? $p->regular_price;
                if (isset($item['price']) && $item['price'] != $unitPrice) {
                    throw new \Exception('Price mismatch');
                }
                if ($p->stock_quantity < $item['qty']) {
                    throw new \Exception('Insufficient stock');
                }
                $subtotal += ($p->sale_price ?? $p->regular_price) * $item['qty'];
            }

            $discount = max(($discountPercent / 100) * $subtotal, $discountFixed);
            $total = $subtotal - $discount;
            $reference = strtoupper(uniqid('POS'));

            $order = Order::create([
                'company_id' => $this->companyId,
                'user_id' => $this->userId,
                'created_by' => $this->userId,
                'reference' => $reference,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'total' => $total,
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'order_type' => 'pos',
            ]);

            $inventory = new InventoryService();

            foreach ($items as $item) {
                $p = $products[$item['id']];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'quantity' => $item['qty'],
                    'unit_price' => ($p->sale_price ?? $p->regular_price),
                    'subtotal' => ($p->sale_price ?? $p->regular_price) * $item['qty'],
                
                ]);

                // deduct stock using service which handles locking/status/movement
                $inventory->deductStock($p->id, $item['qty'], $order->id, Order::class);
            }

            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $paymentMethod,
            ]);

            event(new \App\Events\OrderCreated($order));

            return [
                'order' => $order,
                'totals' => ['subtotal'=>$subtotal,'discount'=>$discount,'total'=>$total],
                'reference' => $reference,
            ];
        });
    }
}
