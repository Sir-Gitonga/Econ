# Inventory Management Module - Complete Implementation Guide

## Overview

This document describes the comprehensive automated inventory management system built into the e-commerce platform. The system provides real-time stock tracking, transactional safety, audit trails, and seamless integration across all sales channels (walk-in POS, online orders, refunds, and restocks).

---

## 1. Core Components

### 1.1 Database Schema

#### Products Table (Enhanced)
- `stock_quantity` - Current stock level (replaces `quantity`)
- `low_stock_threshold` - Alert threshold (default: 10)
- `stock_status` - Enum: `instock`, `lowstock`, `outofstock` (auto-updated)

#### Stock Movements Table (Audit Trail)
```sql
- company_id (tenant scoped)
- product_id (FK to products)
- created_by (FK to users)
- type (enum: sale, purchase, adjustment, return, manual_correction)
- quantity (the delta: positive/negative)
- before_quantity (stock before change)
- after_quantity (stock after change)
- reference_id (order_id, purchase_order_id, etc.)
- reference_type (order, purchase_order, return, etc.)
- notes (reason for adjustment)
- created_at, updated_at
```

#### Order Enhancements
- `refunded_at` - Timestamp of refund
- `refunded_by` - User ID of admin who processed refund

#### Order Items Enhancements
- `refunded_quantity` - Quantity refunded (default: 0)

---

## 2. Service Layer: InventoryService

**Location:** `app/Services/InventoryService.php`

The service handles all inventory operations with automatic transactional safety and row locking.

### 2.1 Constructor
```php
public function __construct(int $userId, int $companyId)
```
- Stores current user ID for audit trail
- Stores company ID for multi-tenancy

### 2.2 Available Methods

#### `deductStock($productId, $quantity, $orderId, $orderType = 'order')`
- **When:** After order placement (POS checkout or web checkout)
- **Action:** Decrements stock, logs to `stock_movements`
- **Safety:** Transactional with `lockForUpdate()`
- **Prevents:** Overselling through row-level locking
- **Logs:**
  - Type: `sale`
  - Reference: order ID
  - Before/after quantities

**Example:**
```php
$inventory = new InventoryService(auth()->id(), $company->id);
$inventory->deductStock(
    productId: 5,
    quantity: 2,
    orderId: $order->id,
    orderType: 'order'
);
```

#### `restoreStock($productId, $quantity, $referenceId, $referenceType = 'order')`
- **When:** Processing refunds or returns
- **Action:** Increments stock, logs to `stock_movements`
- **Safety:** Transactional prevention of invalid states
- **Logs:**
  - Type: `return` or based on context
  - Reference: original order ID

**Example:**
```php
$inventory->restoreStock(
    productId: 5,
    quantity: 2,
    referenceId: $order->id,
    referenceType: 'order'
);
```

#### `adjustStock($productId, $quantityChange, $type, $referenceId, $referenceType, $notes)`
- **When:** Manual inventory adjustments, receiving stock, physical counts, corrections
- **Action:** Adds or removes stock based on delta
- **Safety:** Transactional with `lockForUpdate()`
- **Logs:** Full audit trail with custom notes

**Example:**
```php
// Receiving 50 units from supplier
$inventory->adjustStock(
    productId: 5,
    quantityChange: 50,
    type: 'purchase',
    referenceId: $poId,
    referenceType: 'purchase_order',
    notes: 'Received from Supplier ABC - Invoice #1234'
);

// Manual correction
$inventory->adjustStock(
    productId: 5,
    quantityChange: -3,
    type: 'manual_correction',
    notes: 'Physical count adjustment'
);
```

#### `canDeduct($productId, $quantity)`
- **Returns:** Boolean indicating if enough stock exists
- **Purpose:** Pre-check before attempting deduction

**Example:**
```php
if (!$inventory->canDeduct($productId, $qty)) {
    throw new Exception('Insufficient stock');
}
```

#### `getProductStock($productId)`
- **Returns:** Integer stock quantity
- **Purpose:** Get current stock for real-time checks

#### `getStockStatus($productId)`
- **Returns:** String: `instock`, `lowstock`, or `outofstock`
- **Purpose:** Get current status from database

#### `determineStatus($quantity, $lowThreshold)`
- **Returns:** Status string based on quantity vs threshold
- **Purpose:** Internal calculation helper

---

## 3. Controllers & API Endpoints

### 3.1 Refund Controller

**Location:** `app/Http/Controllers/Admin/RefundController.php`

#### Full Order Refund
```
POST /admin/orders/{order}/refund
```
- Refunds all items in order
- Restores stock for all items
- Marks order as `refunded_at` with current timestamp
- Records `refunded_by` user ID

**Response:**
```json
{
    "success": true,
    "message": "Order refunded successfully",
    "order": { /* full order with items */ }
}
```

#### Partial Refund
```
POST /admin/orders/{order}/partial-refund
{
    "items": [
        { "order_item_id": 1, "quantity": 1 },
        { "order_item_id": 2, "quantity": 2 }
    ]
}
```
- Refunds specific items in order
- Restores stock only for refunded quantities
- Updates `refunded_quantity` on each item
- Prevents refunding more than ordered

**Response:**
```json
{
    "success": true,
    "message": "Partial refund processed successfully",
    "order": { /* updated order */ }
}
```

### 3.2 Inventory API Endpoints

**Location:** `app/Http/Controllers/Api/InventoryController.php`

All API endpoints require authentication (`auth:sanctum` or session auth).

#### Get Single Product Stock
```
GET /api/inventory/product/{product}/stock
```
**Response:**
```json
{
    "id": 5,
    "name": "Product Name",
    "stock_quantity": 42,
    "stock_status": "instock",
    "low_stock_threshold": 10,
    "can_sell": true
}
```

#### Get Multiple Products Stock (Bulk)
```
POST /api/inventory/products/stock
{
    "ids": [1, 2, 3, 4, 5]
}
```
**Response:**
```json
{
    "1": { "id": 1, "stock_quantity": 100, "stock_status": "instock", "can_sell": true },
    "2": { "id": 2, "stock_quantity": 3, "stock_status": "lowstock", "can_sell": true },
    "3": { "id": 3, "stock_quantity": 0, "stock_status": "outofstock", "can_sell": false }
}
```

#### Get Low Stock Products
```
GET /api/inventory/low-stock
```
**Response:**
```json
{
    "total": 5,
    "low_stock": 3,
    "out_of_stock": 2,
    "products": [
        {
            "id": 2,
            "name": "Product B",
            "stock_quantity": 3,
            "stock_status": "lowstock",
            "low_stock_threshold": 10
        },
        /* ... */
    ]
}
```

### 3.3 Inventory Dashboard Controller

**Location:** `app/Http/Controllers/Admin/InventoryDashboardController.php`

#### Low Stock Widget
```php
public function lowStockWidget()
```
- Returns view with low stock alerts
- Displays top 10 products needing attention
- Shows statistics: out of stock count, low stock count

#### Inventory Summary
```php
public function inventorySummary()
```
- Returns JSON with overall inventory stats
- Total products, total stock value
- Breakdown by status (in stock, low stock, out of stock)

---

## 4. Integration Points

### 4.1 POS Checkout Flow

**File:** `resources/views/pos.blade.php` + `app/Http/Controllers/PosController.php`

1. **Display:** Products show stock status and disabled state if out of stock
2. **Data Attributes:** `data-stock="{{ $product->stock_quantity }}"`
3. **Add to Cart:**
   - JavaScript checks if stock > 0 before adding
   - Shows warning if stock ≤ 5
   - Prevents adding out-of-stock items

4. **Checkout:**
   - Calls `InventoryService::deductStock()` for each item
   - Wrapped in transaction for safety
   - Logs audit trail for all items

### 4.2 Web Checkout Flow

**File:** `app/Http/Controllers/CartController.php`

1. **Stock Validation:** Before checkout, confirm stock available
2. **On Order Placement:**
   ```php
   $inventory = new InventoryService(auth()->id(), $company->id);
   foreach ($order->items as $item) {
       $inventory->deductStock(
           $item->product_id,
           $item->quantity,
           $order->id,
           'order'
       );
   }
   ```

### 4.3 Order Refund Flow

**File:** `app/Http/Controllers/Admin/RefundController.php`

1. **Admin initiates refund** via `/admin/orders/{order}/refund`
2. **System restores stock** for all items
3. **Audit trail logged** with type `return`
4. **Order marked refunded** with timestamp and user

### 4.4 Manual Adjustments

**Approach:** Use `InventoryService::adjustStock()` from admin panel:

```php
$inventory->adjustStock(
    productId: 5,
    quantityChange: 50,  // Positive to add
    type: 'purchase',
    referenceId: $poId,
    referenceType: 'purchase_order',
    notes: 'PO #12345 from ABC Suppliers'
);
```

---

## 5. Models

### 5.1 Product Model Updates

**File:** `app/Models/Product.php`

**New Columns:**
```php
protected $fillable = [
    'stock_quantity',        // Current stock level
    'low_stock_threshold',   // Alert threshold
    'stock_status',          // instock|lowstock|outofstock
    /* existing fields */
];

// Relationship to stock movements
public function movements()
{
    return $this->hasMany(StockMovement::class);
}
```

### 5.2 StockMovement Model

**File:** `app/Models/StockMovement.php`

```php
protected $fillable = [
    'company_id',
    'product_id',
    'created_by',
    'type',              // sale|purchase|adjustment|return
    'quantity',          // Delta (positive/negative)
    'before_quantity',   // Stock before change
    'after_quantity',    // Stock after change
    'reference_id',      // order_id or PO_id
    'reference_type',    // order|purchase_order|return
    'notes',             // Custom reason/notes
];

// Relationships
public function product()
{
    return $this->belongsTo(Product::class);
}

public function user()
{
    return $this->belongsTo(User::class, 'created_by');
}
```

### 5.3 Order & OrderItem Updates

**Order Model Additions:**
```php
protected $fillable = [
    'refunded_at',    // Timestamp of refund
    'refunded_by',    // User ID who refunded
    /* existing fields */
];
```

**OrderItem Model Additions:**
```php
protected $fillable = [
    'refunded_quantity', // Qty refunded (partial refund tracking)
    /* existing fields */
];
```

---

## 6. POS Real-Time Updates

### 6.1 Stock Status Display

**Location:** `resources/views/pos.blade.php`

**Visual Indicators:**
```blade
<div class="prod-stock {{ $product->stock_status === 'outofstock' ? 'none' : ($product->stock_status === 'lowstock' ? 'low' : 'ok') }}">
    <i class="fas fa-{{ $product->stock_status !== 'outofstock' ? 'circle-check' : 'circle-xmark' }}"></i>
    {{ $product->stock_status !== 'outofstock' ? 'Stock: '.$product->stock_quantity : 'Out of Stock' }}
</div>
```

**Button Management:**
```blade
<button class="prod-add-btn add-to-cart"
    {{ $product->stock_status === 'outofstock' ? 'disabled' : '' }}>
    {{ $product->stock_status !== 'outofstock' ? 'Add' : 'Unavailable' }}
</button>
```

### 6.2 Enhanced JavaScript Logic

**Prevents Overselling:**
```javascript
function addToCart(id, name, price, stock) {
    // Prevent adding out-of-stock items
    if (stock <= 0) {
        notify('error', 'Out of Stock', `${name} is not available`); 
        return;
    }
    
    // Existing quantity check logic...
    // Prevents exceeding available stock when adding
}
```

---

## 7. Audit Trail & Reporting

### 7.1 Stock Movement History

**Access via:** `StockMovement::where('product_id', $id)->orderBy('created_at', 'desc')->get()`

**Fields for Reporting:**
- `type` - Sale, purchase, adjustment, return
- `quantity` - Amount changed
- `before_quantity` - Previous stock
- `after_quantity` - New stock
- `created_by` - User who initiated
- `reference_type` - Order, PO, etc.
- `notes` - Custom reason
- `created_at` - When change occurred

### 7.2 Sample Audit Trail

```
Product: Widget Pro
Reference Order #1001

Type: sale | Qty: -2 | Before: 50 | After: 48 | By: John Smith | At: 2024-03-05 14:32:15
Type: sale | Qty: -1 | Before: 48 | After: 47 | By: John Smith | At: 2024-03-05 14:35:42
Type: return | Qty: +1 | Before: 47 | After: 48 | By: Admin User | At: 2024-03-05 15:10:00
```

---

## 8. Notifications & Alerts

### 8.1 Admin Dashboard Widget

**Location:** `resources/views/admin/inventory/low-stock-widget.blade.php`

**Features:**
- Displays out-of-stock count
- Displays low-stock count
- Lists top 10 products needing attention
- Direct links to edit product
- Color-coded status badges

### 8.2 Low Stock API

```
GET /api/inventory/low-stock
```

Useful for:
- External dashboards
- Mobile apps
- Email notifications
- Slack integrations

---

## 9. Usage Examples

### 9.1 Example: POS Checkout

```php
// In CheckoutService or CartController
public function processCheckout($order)
{
    DB::transaction(function () use ($order) {
        // Save order and items first
        $order->save();
        
        foreach ($order->items as $item) {
            $item->save();
        }
        
        // Now deduct stock
        $inventory = new InventoryService(auth()->id(), $order->company_id);
        
        foreach ($order->items as $item) {
            if (!$inventory->canDeduct($item->product_id, $item->quantity)) {
                throw new Exception("Out of stock: {$item->product->name}");
            }
            
            $inventory->deductStock(
                $item->product_id,
                $item->quantity,
                $order->id,
                'order'
            );
        }
        
        // Process payment, send email, etc.
    });
}
```

### 9.2 Example: Process Refund

```php
// Admin initiates refund
$inventory = new InventoryService(auth()->id(), $order->company_id);

foreach ($order->items as $item) {
    // Restore stock
    $inventory->restoreStock(
        $item->product_id,
        $item->quantity,
        $order->id,
        'order_return'
    );
}

// Mark order as refunded
$order->update([
    'refunded_at' => now(),
    'refunded_by' => auth()->id(),
]);
```

### 9.3 Example: Receive Purchase Order

```php
// Admin receives 100 units of Product #5 from supplier
$inventory = new InventoryService(auth()->id(), $company->id);

$inventory->adjustStock(
    productId: 5,
    quantityChange: 100,
    type: 'purchase',
    referenceId: 42,  // PO ID
    referenceType: 'purchase_order',
    notes: 'Received PO #LP-2024-042 from ABC Ltd - Invoice #INV-5678'
);
```

---

## 10. Migration Files

### 10.1 Migration: Enhance Stock Movements Table

**File:** `database/migrations/2026_03_04_095149_enhance_stock_movements_table.php`

Adds columns:
- `before_quantity`
- `after_quantity`
- `reference_id`
- `reference_type`
- `created_by`

### 10.2 Migration: Product Stock Fields

**File:** `database/migrations/2026_03_04_094729_add_inventory_fields_to_products_table.php`

Updates:
- Renames `quantity` → `stock_quantity`
- Adds `low_stock_threshold` (default: 10)
- Adds `stock_status` enum

### 10.3 Migration: Refund Fields

**File:** `database/migrations/2026_03_05_000001_add_refund_columns_to_orders.php`

Adds to orders:
- `refunded_at` timestamp
- `refunded_by` user FK

Adds to order_items:
- `refunded_quantity` (default: 0)

---

## 11. Routes Added

### 11.1 Admin Routes

```php
// In routes/web.php (admin prefix)

// Refunds
Route::post('/orders/{order}/refund', [\App\Http\Controllers\Admin\RefundController::class, 'refundOrder'])
    ->name('admin.orders.refund');
    
Route::post('/orders/{order}/partial-refund', [\App\Http\Controllers\Admin\RefundController::class, 'partialRefund'])
    ->name('admin.orders.partial_refund');
```

### 11.2 API Routes

```php
// In routes/api.php

// Inventory (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventory/product/{product}/stock', [InventoryController::class, 'getProductStock']);
    Route::post('/inventory/products/stock', [InventoryController::class, 'getMultipleProductStock']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'getLowStockProducts']);
});
```

---

## 12. Key Features Summary

✅ **Transactional Safety** - `DB::transaction()` + `lockForUpdate()` prevents race conditions
✅ **Full Audit Trail** - Every stock change logged with before/after values
✅ **Multi-Channel Integration** - POS, web, refunds, restocks all use same service
✅ **Real-Time Status** - `instock`, `lowstock`, `outofstock` dynamically calculated
✅ **Prevented Overselling** - Stock deduction happens in transaction
✅ **Low Stock Alerts** - Dashboard widget + API endpoint
✅ **Partial Refunds** - Item-level refund tracking with `refunded_quantity`
✅ **Manual Adjustments** - Support for physical counts and corrections
✅ **Tenant Scoped** - All operations respect company/tenant boundaries
✅ **RESTful API** - API endpoints for external integrations

---

## 13. Testing Checklist

- [ ] Create order in POS → stock decrements
- [ ] Create order on web → stock decrements
- [ ] Refund full order → stock restored
- [ ] Partial refund item → only that quantity restored
- [ ] Stock reaches threshold → status changes to `lowstock`
- [ ] Stock reaches 0 → `outofstock`, button disabled in POS
- [ ] Manual adjustment → audit trail created
- [ ] Purchase order received → stock increased
- [ ] API endpoint returns correct stock levels
- [ ] Low stock widget displays top 10 products
- [ ] Audit trail shows all changes with user/timestamp

---

## 14. Troubleshooting

**Products not showing stock status?**
- Run migrations to ensure `stock_status`, `stock_quantity` columns exist
- Check `Product::boot()` has `CompanyScope` applied

**Stock not deducting on POS checkout?**
- Verify `PosController` calls `InventoryService::deductStock()`
- Check `InventoryService` is instantiated with correct `userId` and `companyId`
- Confirm migrations ran: `php artisan migrate`

**Refund endpoint 404?**
- Verify routes file includes refund routes
- Check auth middleware allows admin access
- Ensure `RefundController` exists in correct namespace

**API endpoints returning 401?**
- Confirm using correct auth header or session
- Check API middleware in `routes/api.php`

---

## 15. Future Enhancements

- Email alerts when product goes out of stock
- Slack integration for low stock notifications
- Automatic reorder suggestions based on usage trends
- Stock prediction/forecasting
- Barcode scanning for fast stock adjustments
- Purchase order integration with suppliers
- Stock transfer between locations
- Warranty/serialized inventory tracking
