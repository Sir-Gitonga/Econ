@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Cashier Dashboard</h3>
        </div>

        <div class="wg-box">
            <h4>Sales Summary</h4>
            <div class="grid grid-cols-4 gap-4 mt-4">
                <div class="p-4 bg-white rounded shadow">Today Sales:<div class="font-semibold">KES 12,450</div></div>
                <div class="p-4 bg-white rounded shadow">Orders:<div class="font-semibold">24</div></div>
                <div class="p-4 bg-white rounded shadow">Cash:<div class="font-semibold">KES 6,200</div></div>
                <div class="p-4 bg-white rounded shadow">M-Pesa:<div class="font-semibold">KES 6,250</div></div>
            </div>

            <div class="mt-6">
                <h5>Quick Actions</h5>
                <div class="flex gap-4 mt-2">
                    <a href="{{ adminRoute('admin.pos') }}" class="tf-button style-1">+ New Sale</a>
                    <button class="tf-button style-2">+ Scan Product</button>
                    <button class="tf-button style-2">+ Receive Payment</button>
                    <button class="tf-button style-2">+ Print Receipt</button>
                </div>
            </div>

            <div class="mt-6">
                <h5>Recent Orders</h5>
                <p>Last 10 orders handled by this cashier will appear here.</p>
            </div>

            <div class="mt-6">
                <h5>Shift Summary</h5>
                <p>Start Time: 08:00</p>
                <p>End Time: --</p>
                <p>Total: KES 32,100</p>
            </div>
        </div>
    </div>
</div>
@endsection
