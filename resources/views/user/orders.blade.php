@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>🛒 Your Orders</h1>
        <p>Track and manage all your purchases</p>
    </div>

    @if($orders->count() > 0)
        {{-- ORDERS GRID --}}
        @foreach($orders as $order)
            <x-order-card :order="$order" />
        @endforeach

        {{-- PAGINATION --}}
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @else
        <x-empty-state
            icon="fas fa-shopping-bag"
            title="No Orders Yet"
            description="You haven't placed any orders yet. Start shopping to see your orders here!"
            action="{{ route('shop.index') }}"
            actionText="Start Shopping"
        />
    @endif
@endsection
