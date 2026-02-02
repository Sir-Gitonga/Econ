<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $dbProduct = \App\Models\Product::find($product->id);

        if ($product->qty < $dbProduct->quantity) {
            $qty = $product->qty + 1;
            Cart::instance('cart')->update($rowId, $qty);
        } else {
            return redirect()->back()->with('error', 'You cannot add more than available stock!');
        }

        return redirect()->back();
    }


    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        if ($product->qty > 1) { // Prevent quantity from going below 1
            $qty = $product->qty - 1;
            Cart::instance('cart')->update($rowId, $qty);
        }
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code))
        {
            $coupon = Coupon::where('code', $coupon_code)->where('expiry_date','>=',Carbon::today())
            ->where('cart_value','<=',Cart::instance('cart')->subtotal())->first();
            if(!$coupon)
            {
                return redirect()->back()->with('error', 'Invalid coupon code!');
            }
            else{
                Session::put('coupon',[
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon applied successfully!');
            }
        }
        else{
            return redirect()->back()->with('error', 'Invalid coupon code.');
        }
    }

    public function calculateDiscount()
    {
        $discount = 0;
        if(Session::has('coupon'))
        {
            if(Session::get('coupon')['type']=='fixed')
            {
                $discount = Session::get('coupon')['value'];
            }
            else{
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value'])/100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount),2,'.',''),
                'tax' => number_format(floatval($taxAfterDiscount),2,'.',''),
                'total' => number_format(floatval($totalAfterDiscount),2,'.','')
            ]);
        }
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login')->with('message', 'Please login first to proceed with checkout.');
        }

        $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
        return view('checkout',compact('address'));
    }

public function place_an_order(Request $request)
{
    $user_id = Auth::user()->id;

    // 🔎 Step 1: Validate stock before creating order
    foreach (Cart::instance('cart')->content() as $item) {
        $product = \App\Models\Product::find($item->id);

        if (!$product) {
            return back()->with('error', "Product {$item->name} not found.");
        }

        if ($item->qty > $product->quantity) {
            return back()->with('error', "Sorry, only {$product->quantity} of {$item->name} left in stock.");
        }
    }

    // 🔎 Step 2: Handle address
    $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
    if (!$address) {
        $request->validate([
            'name' =>'required|max:100',
            'phone' =>'required|numeric|digits:10',
            'zip' =>'required|numeric|digits:6',
            'state' =>'required',
            'city' =>'required',
            'address' =>'required',
            'locality' =>'required',
            'landmark' =>'required',
        ]);

        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'kenya';
        $address->user_id = $user_id;
        $address->isdefault = true;
        $address->save();
    }

    $checkoutResponse = $this->SetAmountforCheckout();
    if ($checkoutResponse instanceof \Illuminate\Http\RedirectResponse) {
        return $checkoutResponse;
    }

    // 🔎 Step 3: Create order
    $order = new Order();
    $order->user_id = $user_id;
    $order->company_id = auth()->user()->company_id;
    $order->subtotal = (float) str_replace(',', '', Session::get('checkout')['subtotal']);
    $order->discount = (float) str_replace(',', '', Session::get('checkout')['discount']);
    $order->tax = (float) str_replace(',', '', Session::get('checkout')['tax']);
    $order->total = (float) str_replace(',', '', Session::get('checkout')['total']);
    $order->name = $address->name;
    $order->phone = $address->phone;
    $order->locality = $address->locality;
    $order->address = $address->address;
    $order->state = $address->state;
    $order->city = $address->city;
    $order->country = $address->country;
    $order->zip = $address->zip;
    $order->landmark = $address->landmark;
    $order->save();

    // 🔎 Step 4: Save order items
    foreach (Cart::instance('cart')->content() as $item) {
        $orderItem = new OrderItem();
        $orderItem->product_id = $item->id;
        $orderItem->order_id = $order->id;
        $orderItem->company_id = auth()->user()->company_id;
        $orderItem->price = $item->price;
        $orderItem->quantity = $item->qty;
        $orderItem->save();
    }

    // 🔎 Step 5: Handle transactions
    if ($request->mode == "cod") {
        $transaction = new Transaction();
        $transaction->order_id = $order->id;
        $transaction->user_id = $user_id;
        $transaction->mode = $request->mode;
        $transaction->status = "pending";
        $transaction->company_id = auth()->user()->company_id;
        $transaction->save();
    }

    // 🔎 Step 6: Clear cart + sessions
    Cart::instance('cart')->destroy();
    Session::forget('checkout');
    Session::forget('coupon');
    Session::forget('discounts');
    Session::put('order_id', $order->id);

    // Redirect based on user role
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.order.details', ['order_id' => $order->id]);
    }

    return redirect()->route('cart.order.confirmation');
}

    public function SetAmountforCheckout()
    {
        if(!cart::instance('cart')->content()->count()>0)
        {
            Session::forget('checkout');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        if(Session::has('coupon'))
        {
            Session::put('checkout',[
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],

            ]);
        }
        else{
            Session::put('checkout',[
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }

    public function order_confirmation()
    {
        if(Session::has('order_id'))
        {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }

}

