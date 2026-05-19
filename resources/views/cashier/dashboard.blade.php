@extends('layouts.cashier')

@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Welcome, {{ Auth::user()->name }}</h2>
                    <p class="text-muted">{{ $company->company_name }} - Cashier Dashboard</p>
                </div>
                <div class="text-right">
                    <p class="mb-0 text-muted">{{ now()->format('l, M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary Cards -->
    <div class="row mb-4">
        <!-- Total Sales -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1">Today's Sales</div>
                    <div class="h3 mb-0 font-weight-bold">Ksh {{ number_format($todayTotal, 0) }}</div>
                    <small class="text-muted">Total revenue today</small>
                </div>
            </div>
        </div>

        <!-- Transaction Count -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1">Transactions</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $todayCount }}</div>
                    <small class="text-muted">Sales processed today</small>
                </div>
            </div>
        </div>

        <!-- Average Transaction -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="text-info text-uppercase mb-1">Avg Transaction</div>
                    <div class="h3 mb-0 font-weight-bold">Ksh {{ number_format($avgTransaction, 0) }}</div>
                    <small class="text-muted">Average per sale</small>
                </div>
            </div>
        </div>

        <!-- Quick Action -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-left-warning">
                <div class="card-body text-center">
                    <a href="{{ route('admin.pos', ['subdomain' => request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null)]) }}" class="btn btn-warning btn-block font-weight-bold">
                        <i class="fas fa-store"></i> New Sale
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Today's Recent Sales -->
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Today's Recent Sales</h5>
                    <span class="badge badge-light">{{ $todaySales->count() }} sales</span>
                </div>
                <div class="card-body p-0">
                    @if($todaySales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Time</th>
                                    <th>Items</th>
                                    <th class="text-right">Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySales as $sale)
                                <tr>
                                    <td class="font-weight-bold">#{{ $sale->id }}</td>
                                    <td>{{ $sale->created_at->format('H:i') }}</td>
                                    <td>{{ $sale->items_count ?? 0 }} items</td>
                                    <td class="text-right font-weight-bold text-success">Ksh {{ number_format($sale->total, 0) }}</td>
                                    <td>
                                        @if($sale->status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($sale->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($sale->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-4 text-center text-muted">
                        <p><i class="fas fa-inbox fa-3x mb-3"></i></p>
                        <p>No sales yet today. Start by creating a new sale.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 2px solid #007bff !important;
    }
    .border-left-success {
        border-left: 2px solid #28a745 !important;
    }
    .border-left-info {
        border-left: 2px solid #17a2b8 !important;
    }
    .border-left-warning {
        border-left: 2px solid #ffc107 !important;
    }
</style>
@endsection


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
                    <a href="{{ route('admin.pos', ['subdomain' => request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null)]) }}" class="tf-button style-1">+ New Sale</a>
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
