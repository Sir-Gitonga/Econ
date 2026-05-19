<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function addresses()
    {
        // Get user's addresses (you can create an Address model later)
        // For now, returning empty or default address
        return view('user.addresses');
    }

    public function account()
    {
        $user = Auth::user();
        $latestOrder = Order::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->first();

        $lastOrderPhone = $latestOrder?->phone;

        return view('user.account-details', compact('user', 'lastOrderPhone'));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:25'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function wishlist()
    {
        // Get wishlist items from session or database
        // For now, just return the view
        return view('user.wishlist');
    }

}
