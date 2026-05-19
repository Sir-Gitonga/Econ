@props(['order', 'compact' => false])

<div class="order-card">
    <div class="order-card-header">
        <div>
            <div class="order-card-id">#{{ $order->id }}</div>
            <div class="order-card-date">{{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}</div>
        </div>
        <x-order-badge :status="$order->status ?? 'pending'" />
    </div>

    <div class="order-card-body">
        <div class="order-card-item">
            <div class="order-card-label">Amount</div>
            <div class="order-card-value">KES {{ number_format($order->total_amount ?? 0, 2) }}</div>
        </div>
        <div class="order-card-item">
            <div class="order-card-label">Items</div>
            <div class="order-card-value">{{ count($order->orderItems ?? []) }}</div>
        </div>
        @if (!$compact)
            <div class="order-card-item">
                <div class="order-card-label">Payment</div>
                <div class="order-card-value">{{ $order->payment_method ?? 'N/A' }}</div>
            </div>
            <div class="order-card-item">
                <div class="order-card-label">Delivery</div>
                <div class="order-card-value">{{ $order->delivery_date ? $order->delivery_date->format('M d') : 'Pending' }}</div>
            </div>
        @endif
    </div>

    <div class="order-card-footer">
        <small class="text-muted">Order placed {{ $order->order_date ? $order->order_date->diffForHumans() : 'recently' }}</small>
        <div class="order-card-actions">
            <a href="{{ route('user.order.details', $order->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i>
                <span>View</span>
            </a>
        </div>
    </div>
</div>
