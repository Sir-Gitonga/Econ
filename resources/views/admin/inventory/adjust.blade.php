@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">📊 Inventory Management</h1>
        <div>
            <a href="{{ route('admin.inventory.history') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-history"></i> View History
            </a>
            <a href="{{ route('admin.inventory.export') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="card-title">In Stock</h5>
                    <h3 class="text-success">{{ $products->where('stock_status', 'instock')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="card-title">Low Stock</h5>
                    <h3 class="text-warning">{{ $products->where('stock_status', 'lowstock')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5 class="card-title">Out of Stock</h5>
                    <h3 class="text-danger">{{ $products->where('stock_status', 'outofstock')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <h3 class="text-info">{{ $products->total() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Adjustment Panel -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">⚙️ Quick Stock Adjustment</h5>
                </div>
                <div class="card-body">
                    <form id="quickAdjustForm">
                        @csrf
                        <div class="mb-3">
                            <label for="product_select" class="form-label">Select Product</label>
                            <select id="product_select" class="form-control" required>
                                <option value="">-- Choose a product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} (SKU: {{ $product->sku }}) - Current: {{ $product->stock_quantity }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="product_info" class="form-label">Product Details</label>
                            <div id="product_info" class="alert alert-light" style="display: none;">
                                <small>
                                    <p class="mb-1"><strong>Current Stock:</strong> <span id="info_stock">-</span></p>
                                    <p class="mb-1"><strong>Status:</strong> <span id="info_status">-</span></p>
                                    <p class="mb-0"><strong>Low Threshold:</strong> <span id="info_threshold">-</span></p>
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity_change" class="form-label">Quantity Change</label>
                            <input type="number" id="quantity_change" name="quantity_change" class="form-control" 
                                   placeholder="Positive to add, negative to reduce" required>
                            <small class="form-text text-muted">Example: +10 to add 10 units, -5 to reduce by 5</small>
                        </div>

                        <div class="mb-3">
                            <label for="adjustment_type" class="form-label">Adjustment Type</label>
                            <select id="adjustment_type" name="type" class="form-control" required>
                                <option value="">-- Select type --</option>
                                <option value="purchase">📦 Purchase/Restock</option>
                                <option value="adjustment">🔧 Physical Count Adjustment</option>
                                <option value="return">↩️ Customer Return</option>
                                <option value="damage_loss">🚫 Damage/Loss</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reference_id" class="form-label">Reference ID (Optional)</label>
                            <input type="text" id="reference_id" name="reference_id" class="form-control" 
                                   placeholder="e.g., PO-123, Invoice #">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" 
                                      placeholder="Add notes about this adjustment..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Apply Adjustment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Movements -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📋 Recent Stock Movements</h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($movements as $movement)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-1">
                                        <strong>{{ $movement->product?->name }}</strong>
                                        @if($movement->type === 'sale')
                                            <span class="badge bg-danger">Sale</span>
                                        @elseif($movement->type === 'purchase')
                                            <span class="badge bg-success">Purchase</span>
                                        @elseif($movement->type === 'return')
                                            <span class="badge bg-warning">Return</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($movement->type) }}</span>
                                        @endif
                                    </p>
                                    <small class="text-muted">
                                        {{ $movement->before_quantity }} → {{ $movement->after_quantity }}
                                        ({{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }})
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        By {{ $movement->user?->name ?? 'System' }} 
                                        <br>
                                        {{ $movement->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <span class="badge {{ $movement->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light text-center">
                            No recent movements
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">📦 All Products</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Low Threshold</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>
                                <span class="badge bg-info">{{ $product->stock_quantity }}</span>
                            </td>
                            <td>
                                @if($product->stock_status === 'instock')
                                    <span class="badge bg-success">In Stock</span>
                                @elseif($product->stock_status === 'lowstock')
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            <td>{{ $product->low_stock_threshold }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-details" 
                                        data-id="{{ $product->id }}" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info set-threshold"
                                        data-id="{{ $product->id }}" data-threshold="{{ $product->low_stock_threshold }}"
                                        title="Set Low Stock Threshold">
                                    <i class="fas fa-flag"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- Set Threshold Modal -->
<div class="modal fade" id="thresholdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Low Stock Threshold</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="thresholdForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="threshold_product_id">
                    <div class="mb-3">
                        <label for="threshold_value" class="form-label">Low Stock Threshold</label>
                        <input type="number" id="threshold_value" class="form-control" min="1" required>
                        <small class="form-text text-muted">
                            When stock reaches this level, product will be marked as "Low Stock"
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Threshold</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_select');
    const productInfo = document.getElementById('product_info');
    const quickAdjustForm = document.getElementById('quickAdjustForm');
    const thresholdForm = document.getElementById('thresholdForm');
    const thresholdModal = new bootstrap.Modal(document.getElementById('thresholdModal'));

    // Load product details when selected
    productSelect.addEventListener('change', async function() {
        if (!this.value) {
            productInfo.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.inventory.product', '') }}/${this.value}`);
            const data = await response.json();
            
            document.getElementById('info_stock').textContent = data.current_stock;
            document.getElementById('info_status').innerHTML = 
                `<span class="badge bg-${getStatusColor(data.status)}">${data.status}</span>`;
            document.getElementById('info_threshold').textContent = data.low_threshold;
            productInfo.style.display = 'block';
        } catch (error) {
            console.error('Error loading product details:', error);
        }
    });

    // Quick adjustment form
    quickAdjustForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!productSelect.value) {
            alert('Please select a product');
            return;
        }

        const formData = new FormData();
        formData.append('product_id', productSelect.value);
        formData.append('quantity_change', document.getElementById('quantity_change').value);
        formData.append('type', document.getElementById('adjustment_type').value);
        formData.append('reference_id', document.getElementById('reference_id').value);
        formData.append('notes', document.getElementById('notes').value);

        try {
            const response = await fetch('{{ route('admin.inventory.adjust') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ Stock adjusted successfully!');
                location.reload();
            } else {
                alert('❌ Error: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error processing adjustment');
        }
    });

    // Set threshold buttons
    document.querySelectorAll('.set-threshold').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const currentThreshold = this.dataset.threshold;
            
            document.getElementById('threshold_product_id').value = productId;
            document.getElementById('threshold_value').value = currentThreshold;
            thresholdModal.show();
        });
    });

    // Threshold form submit
    thresholdForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('product_id', document.getElementById('threshold_product_id').value);
        formData.append('threshold', document.getElementById('threshold_value').value);

        try {
            const response = await fetch('{{ route('admin.inventory.set_threshold') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ Threshold updated successfully!');
                thresholdModal.hide();
                location.reload();
            } else {
                alert('❌ Error: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error updating threshold');
        }
    });

    function getStatusColor(status) {
        switch(status) {
            case 'instock': return 'success';
            case 'lowstock': return 'warning';
            case 'outofstock': return 'danger';
            default: return 'secondary';
        }
    }
});
</script>
@endsection
