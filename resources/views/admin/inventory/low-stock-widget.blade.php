@php
    $outOfStockCount = $stats->out_of_stock ?? 0;
    $lowStockCount = $stats->low_stock ?? 0;
@endphp

<div class="low-stock-widget" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">
            <i class="fas fa-exclamation-triangle" style="color: #d97706; margin-right: 8px;"></i>
            Inventory Alerts
        </h3>
        <a href="{{ route('admin.products') }}" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
            View All <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
        </a>
    </div>

    @if($outOfStockCount > 0 || $lowStockCount > 0)
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
            @if($outOfStockCount > 0)
                <div style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 16px;">
                    <div style="font-size: 0.875rem; color: #7f1d1d; margin-bottom: 4px;">Out of Stock</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #dc2626;">{{ $outOfStockCount }}</div>
                </div>
            @endif

            @if($lowStockCount > 0)
                <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 16px;">
                    <div style="font-size: 0.875rem; color: #78350f; margin-bottom: 4px;">Low Stock</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #d97706;">{{ $lowStockCount }}</div>
                </div>
            @endif
        </div>

        @if($products->count() > 0)
            <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                <table style="width: 100%; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 8px 0; font-weight: 600; color: #6b7280;">Product</th>
                            <th style="text-align: center; padding: 8px 0; font-weight: 600; color: #6b7280;">SKU</th>
                            <th style="text-align: right; padding: 8px 0; font-weight: 600; color: #6b7280;">Stock</th>
                            <th style="text-align: center; padding: 8px 0; font-weight: 600; color: #6b7280;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr style="border-bottom: 1px solid #f3f4f6; hover:background-color: #f9fafb;">
                                <td style="padding: 12px 0;">
                                    <a href="{{ route('admin.product.edit', $product->id) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                                        {{ substr($product->name, 0, 30) }}{{ strlen($product->name) > 30 ? '...' : '' }}
                                    </a>
                                </td>
                                <td style="text-align: center; padding: 12px 0; color: #6b7280;">{{ $product->sku ?? '-' }}</td>
                                <td style="text-align: right; padding: 12px 0; font-weight: 600;">{{ $product->stock_quantity }}</td>
                                <td style="text-align: center; padding: 12px 0;">
                                    @if($product->stock_status === 'outofstock')
                                        <span style="background: #fee; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                            OUT OF STOCK
                                        </span>
                                    @elseif($product->stock_status === 'lowstock')
                                        <span style="background: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                            LOW STOCK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 20px; color: #9ca3af;">
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                <p style="margin: 0;">All products in good stock!</p>
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 20px; color: #9ca3af;">
            <i class="fas fa-check-circle" style="font-size: 2rem; color: #16a34a; margin-bottom: 8px; display: block;"></i>
            <p style="margin: 0; color: #16a34a;">All products in good stock!</p>
        </div>
    @endif
</div>
