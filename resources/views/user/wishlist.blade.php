@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>❤️ My Wishlist</h1>
        <p>Items you've saved for later</p>
    </div>

    {{-- WISHLIST STATS --}}
    <div class="card-container" style="margin-bottom: 2rem;">
        <div class="dashboard-card" style="background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);">
            <div class="dashboard-card-content">
                <div class="dashboard-card-label">Items in Wishlist</div>
                <div class="dashboard-card-value">{{ count($wishlist ?? []) }}</div>
                <div class="dashboard-card-subtitle">Products saved</div>
            </div>
        </div>
        <div class="dashboard-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%);">
            <div class="dashboard-card-content">
                <div class="dashboard-card-label">Recently Added</div>
                <div class="dashboard-card-value">{{ count($wishlist ?? []) > 0 ? '1' : '0' }}</div>
                <div class="dashboard-card-subtitle">This week</div>
            </div>
        </div>
    </div>

    {{-- WISHLIST GRID --}}
    @if(!empty($wishlist ?? []))
        <div class="product-grid">
            @foreach($wishlist as $item)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ asset('uploads/products/' . ($item->product->image ?? 'placeholder.jpg')) }}" 
                             alt="{{ $item->product->name }}">
                        <div style="position: absolute; top: 10px; right: 10px;">
                            <button class="btn btn-icon" style="background: rgba(239, 68, 68, 0.9); color: white;" 
                                    title="Remove from wishlist" onclick="removeFromWishlist({{ $item->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-content">
                        <div class="product-name">{{ $item->product->name }}</div>
                        <div class="product-price">KES {{ number_format($item->product->price ?? 0, 2) }}</div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}" 
                               class="btn btn-primary btn-sm" style="flex: 1;">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <button class="btn btn-secondary btn-sm" onclick="addToCart({{ $item->product->id }})">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SHARE WISHLIST --}}
        <div style="margin-top: 3rem; text-align: center;">
            <div style="background: var(--white); border-radius: var(--radius-lg); padding: 2rem; border: 1px solid var(--gray-200); display: inline-block;">
                <h3 style="color: var(--gray-900); margin-bottom: 1rem;">Share Your Wishlist</h3>
                <p style="color: var(--gray-500); margin-bottom: 1.5rem;">Let friends know about your favorite products</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-secondary">
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fab fa-twitter"></i>
                        <span>Twitter</span>
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </button>
                </div>
            </div>
        </div>
    @else
        <x-empty-state
            icon="fas fa-heart"
            title="Your Wishlist is Empty"
            description="Save products you love to your wishlist and they'll appear here. Start exploring our amazing collection!"
            action="{{ route('shop.index') }}"
            actionText="Browse Products"
        />

        {{-- FEATURED PRODUCTS SUGGESTION --}}
        <div style="margin-top: 3rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h3 style="color: var(--gray-900); margin-bottom: 0.5rem;">Popular Products</h3>
                <p style="color: var(--gray-500);">Discover trending items you might like</p>
            </div>
            <div class="product-grid">
                {{-- Example product cards --}}
                <div class="product-card">
                    <div class="product-image" style="background: linear-gradient(45deg, #f3f4f6, #e5e7eb);">
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--gray-400);">
                            <i class="fas fa-image" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="product-content">
                        <div class="product-name">Sample Product</div>
                        <div class="product-price">KES 2,500.00</div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button class="btn btn-primary btn-sm" style="flex: 1;">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-secondary btn-sm">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image" style="background: linear-gradient(45deg, #e0e7ff, #c7d2fe);">
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--gray-400);">
                            <i class="fas fa-image" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="product-content">
                        <div class="product-name">Another Product</div>
                        <div class="product-price">KES 1,800.00</div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button class="btn btn-primary btn-sm" style="flex: 1;">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-secondary btn-sm">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image" style="background: linear-gradient(45deg, #fef3c7, #fde68a);">
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--gray-400);">
                            <i class="fas fa-image" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="product-content">
                        <div class="product-name">Featured Item</div>
                        <div class="product-price">KES 3,200.00</div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button class="btn btn-primary btn-sm" style="flex: 1;">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-secondary btn-sm">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
function removeFromWishlist(itemId) {
    if (confirm('Remove this item from your wishlist?')) {
        // Handle removal
        alert('Item removed from wishlist! (Backend integration needed)');
    }
}

function addToCart(productId) {
    // Handle add to cart
    alert('Added to cart! (Backend integration needed)');
}
</script>
@endpush
