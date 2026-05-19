<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockMovement;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\Role::class . ':cashier']);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|distinct',
            'cart.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,mpesa,card',
            'cash_received' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_fixed' => 'nullable|numeric|min:0',
        ]);

        $service = new \App\Services\CheckoutService();
        $result = $service->process(
            $request->input('cart'),
            $request->input('payment_method'),
            $request->input('cash_received',0),
            $request->input('discount_percent',0),
            $request->input('discount_fixed',0)
        );

        return response()->json([
            'success' => true,
            'order_id' => $result['order']->id,
            'receipt_number' => $result['reference'],
            'totals' => $result['totals'],
        ]);
    }
}
