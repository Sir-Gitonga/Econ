@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>📦 Order Details</h1>
        <p>Order #{{ $order->id }} • Placed on {{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}</p>
    </div>

    {{-- ORDER SUMMARY CARD --}}
    <div class="dashboard-card" style="margin-bottom: 2rem;">
        <div class="dashboard-card-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; align-items: center;">
                <div>
                    <div class="dashboard-card-label">Order Status</div>
                    <div style="margin-top: 0.5rem;">
                        <x-order-badge :status="$order->status ?? 'pending'" />
                    </div>
                </div>
                <div>
                    <div class="dashboard-card-label">Total Amount</div>
                    <div class="dashboard-card-value">KES {{ number_format($order->total_amount ?? 0, 2) }}</div>
                </div>
                <div>
                    <div class="dashboard-card-label">Items Ordered</div>
                    <div class="dashboard-card-value">{{ count($order->orderItems ?? []) }}</div>
                </div>
                <div>
                    <div class="dashboard-card-label">Payment Method</div>
                    <div class="dashboard-card-value">{{ $order->payment_method ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

        {{-- ORDER ITEMS SECTION --}}
        <div>
            <div style="background: var(--white); border-radius: var(--radius-lg); padding: 2rem; border: 1px solid var(--gray-200); margin-bottom: 2rem;">
                <h3 style="color: var(--gray-900); margin-bottom: 1.5rem; font-size: 1.25rem;">Order Items</h3>

                @foreach($order->orderItems ?? [] as $item)
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--gray-100);">
                        <div style="width: 60px; height: 60px; border-radius: var(--radius-md); overflow: hidden; background: var(--gray-100); flex-shrink: 0;">
                            <img src="{{ asset('uploads/products/thumbnails/' . ($item->product->image ?? 'placeholder.jpg')) }}" 
                                 alt="{{ $item->product->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.25rem 0; font-size: 1rem; color: var(--gray-900);">
                                <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}" 
                                   style="color: inherit; text-decoration: none;" target="_blank">
                                    {{ $item->product->name }}
                                </a>
                            </h4>
                            <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: var(--gray-500);">
                                <span>SKU: {{ $item->product->SKU ?? 'N/A' }}</span>
                                <span>Qty: {{ $item->quantity }}</span>
                                <span>Price: KES {{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: var(--gray-900);">KES {{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- ORDER TOTALS --}}
                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid var(--gray-100);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--gray-600);">Subtotal:</span>
                        <span style="color: var(--gray-900); font-weight: 500;">KES {{ number_format($order->subtotal ?? 0, 2) }}</span>
                    </div>
                    @if($order->discount ?? 0 > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: var(--gray-600);">Discount:</span>
                            <span style="color: var(--success); font-weight: 500;">-KES {{ number_format($order->discount ?? 0, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: var(--gray-900);">
                        <span>Total:</span>
                        <span>KES {{ number_format($order->total_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            @if($order->status == 'ordered')
                <div style="background: var(--danger-light); border: 1px solid var(--danger); border-radius: var(--radius-lg); padding: 1.5rem;">
                    <h4 style="color: var(--danger); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cancel Order
                    </h4>
                    <p style="color: #b91c1c; margin: 0 0 1rem 0;">Are you sure you want to cancel this order? This action cannot be undone.</p>
                    <form action="{{ route('user.order.cancel') }}" method="POST" id="cancel-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div style="display: flex; gap: 1rem;">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">Go Back</button>
                            <button type="button" class="btn" style="background: var(--danger); color: white;" onclick="confirmCancel()">
                                <i class="fas fa-times"></i>
                                Cancel Order
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div style="text-align: center;">
                    <a href="{{ route('user.orders') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Orders
                    </a>
                </div>
            @endif
        </div>

        {{-- SIDEBAR INFO --}}
        <div>

            {{-- SHIPPING ADDRESS --}}
            <div style="background: var(--white); border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--gray-200); margin-bottom: 2rem;">
                <h4 style="color: var(--gray-900); margin-bottom: 1rem; font-size: 1.1rem;">Shipping Address</h4>
                <div style="color: var(--gray-700);">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">{{ $order->name ?? 'N/A' }}</div>
                    <div>{{ $order->address ?? 'N/A' }}</div>
                    <div>{{ $order->locality ?? '' }}, {{ $order->city ?? 'N/A' }}, {{ $order->country ?? 'N/A' }}</div>
                    <div>{{ $order->zip_code ?? 'N/A' }}</div>
                    <div style="margin-top: 0.5rem;">📞 {{ $order->phone ?? 'N/A' }}</div>
                </div>
            </div>

            {{-- PAYMENT INFO --}}
            <div style="background: var(--white); border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--gray-200); margin-bottom: 2rem;">
                <h4 style="color: var(--gray-900); margin-bottom: 1rem; font-size: 1.1rem;">Payment Information</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--gray-600);">Method:</span>
                        <span style="color: var(--gray-900); font-weight: 500;">{{ $transaction->mode ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--gray-600);">Status:</span>
                        @if($transaction->status ?? '' == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($transaction->status ?? '' == 'declined')
                            <span class="badge badge-danger">Declined</span>
                        @elseif($transaction->status ?? '' == 'refunded')
                            <span class="badge badge-warning">Refunded</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--gray-600);">Transaction ID:</span>
                        <span style="color: var(--gray-900); font-weight: 500;">{{ $transaction->id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- ORDER TIMELINE --}}
            <div style="background: var(--white); border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--gray-200);">
                <h4 style="color: var(--gray-900); margin-bottom: 1rem; font-size: 1.1rem;">Order Timeline</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); flex-shrink: 0;"></div>
                        <div>
                            <div style="font-weight: 500; color: var(--gray-900);">Order Placed</div>
                            <small style="color: var(--gray-500);">{{ $order->order_date ? $order->order_date->format('M d, Y H:i') : 'N/A' }}</small>
                        </div>
                    </div>
                    @if($order->status == 'delivered')
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); flex-shrink: 0;"></div>
                            <div>
                                <div style="font-weight: 500; color: var(--gray-900);">Delivered</div>
                                <small style="color: var(--gray-500);">{{ $order->delivered_date ? $order->delivered_date->format('M d, Y H:i') : 'N/A' }}</small>
                            </div>
                        </div>
                    @elseif($order->status == 'canceled')
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--danger); flex-shrink: 0;"></div>
                            <div>
                                <div style="font-weight: 500; color: var(--gray-900);">Canceled</div>
                                <small style="color: var(--gray-500);">{{ $order->canceled_date ? $order->canceled_date->format('M d, Y H:i') : 'N/A' }}</small>
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--warning); flex-shrink: 0;"></div>
                            <div>
                                <div style="font-weight: 500; color: var(--gray-900);">Processing</div>
                                <small style="color: var(--gray-500);">Your order is being prepared</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
<script>
function confirmCancel() {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        document.getElementById('cancel-form').submit();
    }
}
</script>
@endpush
