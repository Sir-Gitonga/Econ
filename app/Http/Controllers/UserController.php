<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    public function index()
    {
        // Get orders count for this user
        $ordersCount = Order::where('user_id', Auth::guard('web')->id())->count();

        // Get recent orders
        $recentOrders = Order::where('user_id', Auth::guard('web')->id())
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        return view('user.index', compact('ordersCount', 'recentOrders'));
    }
    public function orders()
    {
        $orders = Order::where('user_id', Auth::guard('web')->id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::id())->where('id', $order_id)->first();

        if ($order) {
            $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order_id)->first();

            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        }

        return redirect()->route('orders')->with('error', 'Order not found.');
    }
    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();

        return back()->with('status', 'Order canceled successfully!!');
    }

}
