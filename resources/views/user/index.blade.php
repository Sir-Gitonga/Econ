@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
        <p>Here's what's happening with your orders today</p>
    </div>

    @php
        $totalOrders = $ordersCount ?? 0;
        $deliveredOrders = $recentOrders->where('status', 'delivered')->count() ?? 0;
        $pendingOrders = $recentOrders->where('status', '!=', 'delivered')->where('status', '!=', 'canceled')->count() ?? 0;
        $totalSpent = $recentOrders->sum('total_amount') ?? 0;
    @endphp

    {{-- INSIGHT BOX --}}
    @if ($totalOrders == 0)
        <x-insight-box
            type="info"
            message="You haven't placed any orders yet. Start shopping and discover amazing products!"
            action="{{ route('shop.index') }}"
            actionText="Browse Shop"
        />
    @elseif ($pendingOrders > 0)
        <x-insight-box
            type="warning"
            message="You have {{ $pendingOrders }} pending order{{ $pendingOrders > 1 ? 's' : '' }}. Check your order status below."
            action="{{ route('user.orders') }}"
            actionText="View Orders"
        />
    @else
        <x-insight-box
            type="success"
            message="Great! All your orders are delivered. Ready for more shopping?"
            action="{{ route('shop.index') }}"
            actionText="Continue Shopping"
        />
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="card-container">
        <x-dashboard-card
            label="Total Orders"
            value="{{ $totalOrders }}"
            subtitle="All time"
            class="success"
        />
        <x-dashboard-card
            label="Total Spent"
            value="KES {{ number_format($totalSpent, 2) }}"
            subtitle="Lifetime value"
        />
        <x-dashboard-card
            label="Delivered Orders"
            value="{{ $deliveredOrders }}"
            subtitle="Completed"
            class="success"
        />
        <x-dashboard-card
            label="Pending Orders"
            value="{{ $pendingOrders }}"
            subtitle="In progress"
            class="warning"
        />
    </div>

    {{-- RECENT ORDERS --}}
    @if ($recentOrders->count() > 0)
        <div style="margin-top: 3rem;">
            <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--gray-100);">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--gray-900);">Recent Orders</h2>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin: 0.5rem 0 0 0;">Your latest orders at a glance</p>
            </div>

            @foreach ($recentOrders as $order)
                <x-order-card :order="$order" compact="true" />
            @endforeach

            @if ($totalOrders > 5)
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="{{ route('user.orders') }}" class="btn btn-primary">
                        <span>View All Orders</span>
                    </a>
                </div>
            @endif
        </div>
    @else
        <div style="margin-top: 3rem;">
            <x-empty-state
                icon="fas fa-shopping-bag"
                title="No Orders Yet"
                description="Start exploring our amazing products and place your first order today!"
                action="{{ route('shop.index') }}"
                actionText="Start Shopping"
            />
        </div>
    @endif
@endsection
