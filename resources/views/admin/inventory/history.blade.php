@extends('layouts.app')

@section('title', 'Stock Movement History')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">📋 Stock Movement History</h1>
        <a href="{{ route('admin.inventory.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventory.history') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="product_id" class="form-label">Filter by Product</label>
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">-- All Products --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Filter by Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">-- All Types --</option>
                        <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.inventory.history') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-center">Before</th>
                        <th class="text-center">Change</th>
                        <th class="text-center">After</th>
                        <th>Changed By</th>
                        <th>Date & Time</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="align-middle">
                            <td>
                                <strong>{{ $movement->product?->name ?? 'N/A' }}</strong>
                                @if($movement->product?->sku)
                                    <br><small class="text-muted">{{ $movement->product->sku }}</small>
                                @endif
                            </td>
                            <td>
                                @if($movement->type === 'sale')
                                    <span class="badge bg-danger">Sale</span>
                                @elseif($movement->type === 'purchase')
                                    <span class="badge bg-success">Purchase</span>
                                @elseif($movement->type === 'return')
                                    <span class="badge bg-warning">Return</span>
                                @elseif($movement->type === 'adjustment')
                                    <span class="badge bg-info">Adjustment</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($movement->type) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <code>{{ $movement->before_quantity ?? '-' }}</code>
                            </td>
                            <td class="text-center">
                                @if($movement->quantity > 0)
                                    <span class="text-success">+{{ $movement->quantity }}</span>
                                @elseif($movement->quantity < 0)
                                    <span class="text-danger">{{ $movement->quantity }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <code>{{ $movement->after_quantity ?? '-' }}</code>
                            </td>
                            <td>{{ $movement->user?->name ?? 'System' }}</td>
                            <td>
                                <small>
                                    {{ $movement->created_at->format('M d, Y') }}<br>
                                    <span class="text-muted">{{ $movement->created_at->format('H:i:s') }}</span>
                                </small>
                            </td>
                            <td>
                                @if($movement->notes)
                                    <small class="text-muted" title="{{ $movement->notes }}">
                                        {{ Str::limit($movement->notes, 30) }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No stock movements found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $movements->links() }}
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Movements</h5>
                    <h3 class="text-primary">{{ $movements->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Sales</h5>
                    <h3 class="text-danger">
                        {{ $movements->where('type', 'sale')->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Purchases</h5>
                    <h3 class="text-success">
                        {{ $movements->where('type', 'purchase')->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Adjustments</h5>
                    <h3 class="text-info">
                        {{ $movements->where('type', 'adjustment')->count() + $movements->where('type', 'return')->count() }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
