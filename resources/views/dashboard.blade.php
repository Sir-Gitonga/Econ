@extends($layout)

@section('content')
    {{-- Cashier-specific summary --}}
    @isset($todaySales)
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">Welcome, {{ Auth::user()->name }}</h2>
                            <p class="text-muted">{{ auth()->user()->company->company_name }} - Cashier Dashboard</p>
                        </div>
                        <div class="text-right">
                            <p class="mb-0 text-muted">{{ now()->format('l, M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-primary text-uppercase mb-1">Today's Sales</div>
                            <div class="h3 mb-0 font-weight-bold">Ksh {{ number_format($todayTotal ?? 0, 0) }}</div>
                            <small class="text-muted">Total revenue today</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-success text-uppercase mb-1">Transactions</div>
                            <div class="h3 mb-0 font-weight-bold">{{ $todayCount ?? 0 }}</div>
                            <small class="text-muted">Sales processed today</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-info text-uppercase mb-1">Avg Transaction</div>
                            <div class="h3 mb-0 font-weight-bold">Ksh {{ number_format($avgTransaction ?? 0, 0) }}</div>
                            <small class="text-muted">Average per sale</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-left-warning">
                        <div class="card-body text-center">
                            <a href="{{ route('admin.pos', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}" class="btn btn-warning btn-block font-weight-bold">
                                <i class="fas fa-store"></i> New Sale
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($todaySales))
                <div class="row">
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
            @endif

            {{-- Recent orders section for cashier --}}
            @if(isset($orders))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-lg">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Orders</h5>
                                <a href="{{ cashierRoute('cashier.orders', ['subdomain' => Auth::user()->company->slug]) }}" class="btn btn-light btn-sm">
                                    <span>View all</span>
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width:70px">OrderNo</th>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Phone</th>
                                                <th class="text-center">Subtotal</th>

                                                <th class="text-center">Total</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Order Date</th>
                                                <th class="text-center">Total Items</th>
                                                <th class="text-center">Delivered On</th>
                                                <th class="text-center">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                            <tr>
                                                <td class="text-center">{{$order->id}}</td>
                                                <td class="text-center">{{$order->name}}</td>
                                                <td class="text-center">{{$order->phone}}</td>
                                                <td class="text-center">Ksh{{ number_format($order->subtotal, 2) }}</td>

                                                <td class="text-center">Ksh{{ number_format($order->total, 2) }}</td>
                                                <td class="text-center">
                                                    @if($order->status == 'delivered')
                                                        <span class="badge bg-success">Delivered</span>
                                                    @elseif($order->status == 'canceled')
                                                        <span class="badge bg-danger">Canceled</span>
                                                    @else
                                                        <span class="badge bg-warning">Ordered</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{$order->created_at}}</td>
                                                <td class="text-center">{{ $order->items_count }}</td>
                                                <td class="text-center">{{$order->delivered_date}}</td>
                                                <td class="text-center">
                                                    <a href="{{adminRoute('admin.order.details',['subdomain' => Auth::user()->company->slug, 'order_id'=>$order->id])}}">
                                                        <div class="list-icon-function view-icon">
                                                            <div class="item eye">
                                                                <i class="icon-eye"></i>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endisset

    {{-- Admin-specific content --}}
    @isset($dashboardDatas)
        @include('partials.admin-dashboard')
    @endisset

    {{-- optional real‑time listener for pusher/echo; will reload the page when a new
         order is broadcast on the company channel.  Echo is commented out in
         `resources/js/bootstrap.js` by default, so enabling requires updating
         the front‑end build. --}}
    @if(isset($company))
        <script>
            if (window.Echo) {
                Echo.channel('company.{{ $company->id }}')
                    .listen('OrderCreated', function () {
                        location.reload();
                    });
            }
        </script>
    @endif
@endsection