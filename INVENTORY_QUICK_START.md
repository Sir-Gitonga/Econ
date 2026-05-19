# Inventory System - Quick Start & Deployment Guide

**Last Updated:** April 20, 2026  
**Status:** ✅ Ready to Deploy

---

## 🚀 Deployment Steps

### Step 1: Run Migrations
```bash
php artisan migrate

# Expected output:
# Migrating: 2026_04_20_000001_add_notes_to_stock_movements
# Migrated:  2026_04_20_000001_add_notes_to_stock_movements (0.15s)
```

### Step 2: Clear Cache (Important!)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Verify Installation
```bash
# Check that all files exist
ls -la app/Services/InventoryService.php
ls -la app/Http/Controllers/Admin/StockAdjustmentController.php
ls -la public/js/stock-manager.js
ls -la resources/views/admin/inventory/

# Should see 3 files: adjust.blade.php, history.blade.php, low-stock-widget.blade.php
```

### Step 4: Test in Browser
1. Log in as admin (your company)
2. Navigate to: `/admin/inventory`
3. Should see:
   - Inventory stats (in stock, low stock, out of stock, total)
   - Quick adjustment form
   - Recent movements list
   - Products table with current stock

### Step 5: Run First Test
```bash
# Create a test order via POS
curl -X POST "http://yourdomain.local/admin/pos/checkout" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "cart": [{"id": 1, "qty": 2}],
    "payment_method": "cash"
  }'

# Response should include order_id and receipt_number
# Check database: SELECT * FROM stock_movements LIMIT 5;
# Should see new entry with type='sale'
```

---

## 📊 How to Use - Admin View

### Scenario 1: Quick Stock Adjustment
**Goal:** Add 50 units to product because new stock arrived

1. Go to `/admin/inventory`
2. In "Quick Stock Adjustment" section:
   - Select product from dropdown
   - Enter `50` in "Quantity Change"
   - Select "Purchase/Restock" type
   - Enter reference: "PO-123" (optional)
   - Click "Apply Adjustment"
3. ✅ Stock increased by 50, audit trail created

### Scenario 2: Customer Return (Manual Refund Not Processed by System)
**Goal:** Customer physically returned 2 units, need to add back to inventory

**Option A: Via Admin Panel (UI)**
1. Go to `/admin/inventory`
2. Quick adjustment:
   - Select product
   - Enter `+2`
   - Select "Customer Return"
   - Click submit
3. ✅ Done - stock restored with audit trail

**Option B: Via Order Refund (Automatic)**
1. Go to `/admin/orders`
2. Find the order
3. Click "Refund Order"
4. ✅ All stock auto-restored, movement logged

### Scenario 3: Physical Inventory Count
**Goal:** Counted inventory manually, found 35 units but system shows 40. Need to correct to 35.

1. Go to `/admin/inventory`
2. Find product in table (shows current: 40)
3. Quick adjustment:
   - Select product
   - Enter `-5` (to go from 40 → 35)
   - Select "Physical Count Adjustment"
   - Notes: "Q2 physical inventory count"
   - Submit
4. ✅ Corrected with notes in audit trail

### Scenario 4: View Complete Audit Trail
**Goal:** See all stock changes for a product over the past month

1. Go to `/admin/inventory/history`
2. Filter by:
   - Product: Select your product
   - Type: Leave empty (see all) or select specific type
   - Date range: Optional
3. Click "Filter"
4. See table with all movements:
   - Before → Change → After
   - Who made change (user)
   - When (timestamp)
   - Notes
5. Optional: Export to CSV with button

### Scenario 5: Set Low Stock Threshold
**Goal:** This product should trigger "low stock" alert when it hits 20 units (currently threshold is 10)

1. Go to `/admin/inventory`
2. Find product in table
3. Click flag icon in Actions column
4. Dialog pops up with current threshold
5. Change to `20`
6. Click "Save Threshold"
7. ✅ Status auto-recalculated, future alerts use new threshold

### Scenario 6: Check Low Stock Items
**Goal:** See which products are running low

1. Click "View History" button at top (or go to `/admin/inventory/history`)
2. Filter by Type: "lowstock"
3. Or go directly to Low Stock dashboard:
   - Navigate to `/superadmin/inventory/low-stock` equivalent
   - Or check low-stock widget on main dashboard
4. See all items needing attention with current levels

---

## 🛒 How It Works - Customer View

### For Walk-in (POS)
1. **Product Browse:** View products, each shows stock status
   - Green badge: ✓ In Stock
   - Yellow badge: ⚠ Low Stock
   - Red badge (disabled button): ✗ Out of Stock

2. **Add to Cart:** Only clickable if in stock
   - Tries adding out-of-stock → button disabled, tooltip shown

3. **Checkout:** System checks stock is still available
   - Proceeds if sufficient
   - Fails if sold out meanwhile (error message)

4. **Receipt:** Shows items sold + updated stock in next inventory view

### For Web Store Customers
1. **Browse:** Same stock badges on product pages
2. **Add to Cart:** Can't add out-of-stock items
3. **Proceed to Checkout:** 
   - Stock validated again
   - Shows confirmation if any items just went out of stock
4. **Order Complete:** Stock deducted, receipt sent

---

## 🔄 Automated Flows

### POS Sale (Automatic)
```
Customer purchases at cashier
  ↓
POS system creates order
  ↓
CheckoutService called
  ↓
For each item:
  InventoryService::deductStock() called
  ↓
  Database transaction:
    - Lock product row
    - Validate stock available
    - Decrease quantity
    - Recalculate status
    - Log to stock_movements
    - Unlock row
  ↓
Order complete, stock updated ✅
```

**Result:** Stock is **guaranteed correct**, no overselling possible.

---

### Web Order (Automatic)
```
Customer submits online order
  ↓
Same CheckoutService & InventoryService used
  ↓
All same guarantees as POS
  ↓
Order complete, stock updated ✅
```

**Result:** POS and Web have identical inventory logic.

---

### Refund Processing (Automatic)
```
Admin views order in admin panel
  ↓
Clicks "Refund Order"
  ↓
RefundController calls InventoryService::restoreStock()
  ↓
For each item:
  - Increment quantity
  - Recalculate status  
  - Log as type='return'
  ↓
Order marked refunded
  ↓
Stock restored ✅
```

**Result:** Refunds are **fully automatic**, no manual adjustments needed.

---

### Real-Time Frontend Updates (Automatic)
```
Admin updates stock
  ↓
Database updated
  ↓
Frontend polls API every 30 seconds
  ↓
Product cards check /api/inventory/products/stock
  ↓
Display updated:
  - New stock qty
  - New status badge
  - Button enabled/disabled
  ↓
Customer sees live updates ✅
```

**Result:** No page refresh needed, stock displays are always fresh.

---

## 📱 API for Integrations

### Check Single Product Stock
```bash
curl "http://yourdomain.com/api/inventory/product/5/stock" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Response:
{
  "id": 5,
  "name": "Widget A",
  "stock_quantity": 45,
  "stock_status": "instock",
  "low_stock_threshold": 10,
  "can_sell": true
}
```

### Check Multiple Products at Once
```bash
curl -X POST "http://yourdomain.com/api/inventory/products/stock" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3, 4, 5]}'

# Response:
{
  "1": {"id": 1, "stock_quantity": 100, "stock_status": "instock", "can_sell": true},
  "2": {"id": 2, "stock_quantity": 5, "stock_status": "lowstock", "can_sell": true},
  "3": {"id": 3, "stock_quantity": 0, "stock_status": "outofstock", "can_sell": false},
  ...
}
```

### Get Low Stock Alert List
```bash
curl "http://yourdomain.com/api/inventory/low-stock" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Response:
{
  "total": 12,
  "low_stock": 8,
  "out_of_stock": 4,
  "products": [
    {
      "id": 15,
      "name": "Critical Item",
      "stock_quantity": 2,
      "stock_status": "lowstock",
      "low_stock_threshold": 10
    },
    ...
  ]
}
```

---

## 🧪 Testing Checklist

### ✅ Pre-Launch Tests
- [ ] Migrations run without errors
- [ ] Routes accessible: `/admin/inventory`, `/api/inventory/*`
- [ ] Admin can view inventory page
- [ ] Admin can adjust stock
- [ ] Admin can see stock history

### ✅ POS Integration Tests
- [ ] Create POS order → Stock deducted
- [ ] Check `stock_movements` → Entry created
- [ ] Order 2 items of same product → Qty correct
- [ ] Restock product → Can order again

### ✅ Web Integration Tests
- [ ] Product shows stock status (badge)
- [ ] Can't add out-of-stock to cart
- [ ] Complete web order → Stock deducted
- [ ] Movements logged correctly

### ✅ Refund Tests
- [ ] Full refund order → Stock restored
- [ ] Partial refund items → Correct qty restored
- [ ] Movements logged as 'return'
- [ ] Can order product again after refund

### ✅ Multi-tenant Tests
- [ ] Log in to tenant A
- [ ] Check stock for product
- [ ] Switch to tenant B (different subdomain)
- [ ] See different stock (not affected by A)
- [ ] Adjusting in B doesn't change A

### ✅ Live Update Tests
- [ ] Open product page in browser
- [ ] Adjust stock in admin panel on another window
- [ ] Wait max 30 seconds
- [ ] Product page auto-updates (no refresh needed)

---

## 🔧 Troubleshooting

### Problem: Migrations fail
```
Laravel Error: SQLSTATE HY000] General error
```
**Solution:**
```bash
# Check existing columns
php artisan tinker
>>> DB::select('DESCRIBE stock_movements')

# Run only new migration
php artisan migrate --step
```

### Problem: Routes not found
```
404: Route not found /admin/inventory
```
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
# Verify routes with:
php artisan route:list | grep inventory
```

### Problem: Stock not updating on checkout
```
Order created but stock_movements empty
```
**Solution:**
1. Check CheckoutService is calling InventoryService
2. Verify company context middleware is active
3. Check for exceptions in `storage/logs/laravel.log`

### Problem: Out-of-stock button still active
```
Can still add out-of-stock items to cart
```
**Solution:**
1. Check `stock-manager.js` is loaded:
   - Open DevTools Network tab
   - Should see `public/js/stock-manager.js` loaded
2. Check API endpoint works:
   - Go to browser console
   - `fetch('/api/inventory/low-stock').then(r => r.json()).then(console.log)`
   - Should return data
3. Check auth token in meta tag or localStorage

### Problem: Data not matching across tenants
```
Tenant A stock differs from actual in DB
```
**Solution:**
1. Never use direct SQL queries outside application
2. Always go through models with global scopes
3. Verify company middleware is active
4. Check user's company_id: `Auth::user()->company_id`

---

## 📈 Performance Tips

### For High-Volume Sales (1000+ orders/day)
1. **Increase polling interval:**
   - Edit `public/js/stock-manager.js` 
   - Change `30000` to `60000` (60 seconds)
   - Reduces API load by 50%

2. **Cache low-stock list:**
   - Admin dashboard caches it for 5 minutes
   - Manually refresh if need immediate update

3. **Archive old movements:**
   - After 90 days, archive to separate table
   - Keeps stock_movements table lean
   - Query performance stays fast

### For Real-Time Accuracy
1. **Decrease polling interval:**
   - Change `30000` to `10000` (10 seconds)
   - More real-time but more API calls
   - Good for retail with fast turnover

2. **Use WebSockets:**
   - Replace polling with Laravel Echo
   - Listen to broadcast channels
   - Instant updates without API overhead

---

## 🎯 Common Tasks

### Export Stock Report
```bash
# Via admin panel: /admin/inventory/export
# Or if using API:
curl "http://yourdomain.com/api/inventory/export?from_date=2026-04-01&to_date=2026-04-30" \
  -H "Authorization: Bearer TOKEN" \
  -o stock-report-apr.csv
```

### Set Up Alerts
```php
// In admin panel, go to /admin/inventory
// For each critical product:
// - Click the flag icon
// - Set low_stock_threshold to desired level
// - Save

// If qty drops below threshold:
// - Status changes to "lowstock"
// - Appears in /api/inventory/low-stock
// - Admin sees alert on dashboard
```

### Integrate with External System
```php
// Use API endpoint to sync:
$response = Http::withToken($token)->post(
    'http://yourdomain.com/api/inventory/products/stock',
    ['ids' => [1, 2, 3]]
);

$stock = $response->json();
// $stock[1] = ['stock_quantity' => 50, 'can_sell' => true, ...]
// Update your external system with this data
```

---

## 📚 File Structure

```
project/
├── app/
│   ├── Services/
│   │   ├── InventoryService.php          ← Core inventory logic
│   │   └── CheckoutService.php           ← Integrated with inventory
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── StockAdjustmentController.php  ← Admin adjustments
│   │   │   └── RefundController.php           ← Refund processing
│   │   └── Api/
│   │       └── InventoryController.php    ← REST API (3 endpoints)
│   └── Models/
│       ├── Product.php                   ← has movements() relationship
│       ├── StockMovement.php             ← Audit trail model
│       └── Order.php                     ← has refund fields
├── database/
│   └── migrations/
│       ├── 2026_03_03_000104_create_stock_movements_table.php
│       ├── 2026_03_04_094729_add_inventory_fields_to_products_table.php
│       ├── 2026_03_04_095149_enhance_stock_movements_table.php
│       └── 2026_04_20_000001_add_notes_to_stock_movements.php
├── routes/
│   ├── api.php                          ← 3 API endpoints
│   └── web.php                          ← Admin routes
├── public/
│   └── js/
│       └── stock-manager.js             ← Frontend real-time updates
└── resources/
    └── views/
        └── admin/inventory/
            ├── adjust.blade.php          ← Adjustment UI
            ├── history.blade.php         ← History view
            └── low-stock-widget.blade.php ← Alert widget
```

---

## ✅ Deployment Checklist

Before going live, verify:

- [ ] All migrations ran successfully
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Config cached (`php artisan config:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Environment is `production` or `staging`
- [ ] Admin can access `/admin/inventory`
- [ ] Created test order → stock deducted
- [ ] API endpoints return data
- [ ] Frontend updates work (no console errors)
- [ ] Multi-tenant isolation verified
- [ ] Backups taken
- [ ] Staff trained on new system

---

## 🎉 You're Ready!

The inventory system is production-ready. Deploy with confidence!

For detailed technical documentation, see `INVENTORY_SYSTEM_COMPLETE.md`.

---

**v2.0.0 - Deployment Guide** ✅
