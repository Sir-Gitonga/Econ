# Super Admin Panel - Navigation Guide

## 🗺️ Complete Site Map

```
SUPER ADMIN DASHBOARD
└── http://admin.localhost:8000/superadmin/

├── 🔐 AUTHENTICATION
│   ├── Login: /superadmin/login
│   └── Logout: /superadmin/logout (POST)
│
├── 📊 DASHBOARD (Home)
│   └── /superadmin/ 
│       ├── KPI Cards (5 metrics)
│       ├── Quick Actions (4 buttons)
│       ├── Tenant Status Distribution
│       ├── System Health
│       ├── Recent Tenants Table
│       └── Subscription Plans Overview
│
├── 🏢 TENANT MANAGEMENT
│   ├── List Tenants: /superadmin/tenants
│   │   └── Columns: Company, Email, Users, Status, Plan, Created, Actions
│   │   └── Actions: View, Edit, Suspend, Activate, Delete
│   │
│   ├── Create Tenant: /superadmin/tenants/create
│   │   └── Fields: Name, Email, Phone, Business Type, Password, Status
│   │
│   ├── View Tenant: /superadmin/tenants/{id}
│   │   ├── Company Info
│   │   ├── Current Subscription
│   │   ├── Users List
│   │   └── Subscription History
│   │
│   ├── Edit Tenant: /superadmin/tenants/{id}/edit
│   │   └── Update all tenant details
│   │
│   ├── Suspend: /superadmin/tenants/{id}/suspend (PATCH)
│   │   └── Marks tenant as suspended
│   │
│   ├── Activate: /superadmin/tenants/{id}/activate (PATCH)
│   │   └── Marks tenant as active
│   │
│   └── Delete: /superadmin/tenants/{id} (DELETE)
│       └── Removes tenant permanently
│
├── 💳 SUBSCRIPTION PLANS
│   ├── List Plans: /superadmin/subscriptions
│   │   ├── Grid Layout (3 columns)
│   │   ├── Each card shows:
│   │   │   ├── Plan Name
│   │   │   ├── Price/Month
│   │   │   ├── Description
│   │   │   ├── Features List
│   │   │   ├── Tenant Count
│   │   │   └── Actions (View Details, Edit, Delete)
│   │   └── Empty State: Create First Plan button
│   │
│   ├── Create Plan: /superadmin/subscriptions/create
│   │   └── Fields: Name, Price, Currency, Description, Features (dynamic)
│   │
│   ├── View Plan: /superadmin/subscriptions/{id}
│   │   ├── Plan Details (name, description, pricing)
│   │   ├── Features List
│   │   ├── Plan Statistics
│   │   │   ├── Active Subscriptions
│   │   │   ├── Total Subscriptions
│   │   │   └── Monthly Revenue
│   │   ├── Recent Subscriptions Table
│   │   └── Actions (Edit, Delete)
│   │
│   ├── Edit Plan: /superadmin/subscriptions/{id}/edit
│   │   ├── Update all plan details
│   │   └── Sidebar with statistics
│   │
│   └── Delete: /superadmin/subscriptions/{id} (DELETE)
│       └── Removes plan (subscriptions unaffected)
│
└── 📱 RESPONSIVE LAYOUTS
    ├── Sidebar Navigation (left 256px)
    ├── Top Navbar with user greeting
    ├── Main Content Area (responsive grid)
    └── Mobile-friendly Tailwind designs
```

---

## 🔄 User Journey Examples

### Example 1: Creating a New Tenant

1. **Start:** Dashboard
2. **Click:** "Create Tenant" quick action button
3. **Navigate:** `/superadmin/tenants/create`
4. **Fill Form:**
   - Company Name: "Acme Corp"
   - Email: "admin@acmecorp.com"
   - Phone: "+1234567890"
   - Business Type: "E-Commerce"
   - Password: "SecurePass123"
   - Status: "Active"
5. **Submit:** Form validation → Database insert
6. **Redirect:** Shows success message → `/superadmin/tenants/{id}`
7. **Result:** Tenant listed on dashboard, appears in Recent Tenants table

### Example 2: Creating a Subscription Plan

1. **Start:** Dashboard
2. **Click:** "Create Plan" quick action
3. **Navigate:** `/superadmin/subscriptions/create`
4. **Fill Form:**
   - Name: "Premium Plan"
   - Price: "59.99"
   - Currency: "USD"
   - Description: "Full featured plan"
   - Features: Add multiple via "+ Add Feature" button
     - "Unlimited Users"
     - "Advanced Analytics"
     - "Priority Support"
     - "Custom Integrations"
5. **Submit:** Validation → Database insert
6. **Redirect:** Success message → `/superadmin/subscriptions/{id}`
7. **Result:** Plan visible in subscriptions grid with all features displayed

### Example 3: Managing a Tenant Status

1. **Navigate:** `/superadmin/tenants` (Tenants list)
2. **Find:** Company in table
3. **Options:**
   - **View Details:** Click "View" → See full profile
   - **Edit:** Click "Edit" → Modify details
   - **Suspend:** Click "Suspend" → Change status to suspended
   - **Activate:** Click "Activate" → Change status to active (if suspended)
   - **Delete:** Click "Delete" → Permanent removal

---

## 📋 Forms & Validation

### Tenant Forms
**Create Form** (`/superadmin/tenants/create`)
- Company Name: Required, Unique
- Email: Required, Valid Email, Unique
- Phone: Required
- Business Type: Required
- Password: Required, Min 8 chars, Confirmed
- Status: Required, Select from (Active, Inactive, Suspended)

**Edit Form** (`/superadmin/tenants/{id}/edit`)
- All same fields as create
- Password: Optional (leave blank to keep existing)
- Pre-populated with current values

### Subscription Forms
**Create Form** (`/superadmin/subscriptions/create`)
- Name: Required, Unique
- Price: Required, Decimal, Min 0
- Currency: Select from (USD, EUR, GBP, KES)
- Description: Required, Text
- Features: Dynamic array input
  - Add up to unlimited features
  - Remove individual features
  - Cleaned on submit (empty fields removed)

**Edit Form** (`/superadmin/subscriptions/{id}/edit`)
- All same as create
- Pre-populated values
- Statistics sidebar showing impact

---

## 🎨 Dashboard Sections Explained

### 1. KPI Cards (5 Cards)
```
┌─────────────────────────────────────────────┐
│ Total Tenants  │  Active  │  Suspended  │  │
│      24        │   20     │      2      │  │
└─────────────────────────────────────────────┘
                    │
            Total Users: 156
            Active Subscriptions: 18
```
- Gradient backgrounds for visual appeal
- Icons for quick recognition
- Real-time data from database

### 2. Quick Actions (4 Buttons)
```
[Create Tenant] [Create Plan] [View Tenants] [Manage Plans]
```
- Fast access to common operations
- Reduces navigation clicks

### 3. Tenant Status Distribution
```
Active: 83% ████████████████████
Inactive: 12% ███
Suspended: 5% █
```
- Visual progress bars
- Percentage breakdown
- At-a-glance status overview

### 4. System Health
```
✓ All Systems Operational
✓ Database: Connected
✓ Caching: Active
✓ Queues: Processed
```
- Green checkmarks for good status
- Quick system status overview

### 5. Recent Tenants Table
```
Company    │ Email           │ Users │ Plan      │ Status │ Registered │ Actions
Acme Corp  │ admin@acme...   │ 12    │ Pro       │ Active │ 5 days ago │ View
TechStart  │ admin@techstart │ 8     │ Basic     │ Active │ 2 weeks    │ View
```
- Sortable columns
- Pagination support
- Quick actions (View/Edit/Suspend/Delete)

### 6. Subscription Plans Grid
```
┌────────────────┐  ┌────────────────┐  ┌────────────────┐
│ Basic          │  │ Professional   │  │ Enterprise     │
│ $29.99/month   │  │ $59.99/month   │  │ $99.99/month   │
│                │  │                │  │                │
│ ✓ Feature 1    │  │ ✓ Feature 1    │  │ ✓ Feature 1    │
│ ✓ Feature 2    │  │ ✓ Feature 2    │  │ ✓ Feature 2    │
│ ✓ Feature 3    │  │ ✓ Feature 3    │  │ ✓ Feature 3    │
│                │  │                │  │                │
│ 4 tenants      │  │ 8 tenants      │  │ 12 tenants     │
│                │  │                │  │                │
│ [View] [Edit]  │  │ [View] [Edit]  │  │ [View] [Edit]  │
│ [Delete]       │  │ [Delete]       │  │ [Delete]       │
└────────────────┘  └────────────────┘  └────────────────┘
```
- 3-column responsive layout
- All plan details at a glance
- Quick navigation to details/edit

---

## 🔧 Technical URLs Reference

### API/Route Endpoints (RESTful)

**Tenants Resource:**
| Method | Endpoint | Route Name | Purpose |
|--------|----------|-----------|---------|
| GET | /superadmin/tenants | superadmin.tenants.index | List all tenants |
| GET | /superadmin/tenants/create | superadmin.tenants.create | Show create form |
| POST | /superadmin/tenants | superadmin.tenants.store | Store new tenant |
| GET | /superadmin/tenants/{id} | superadmin.tenants.show | Show tenant detail |
| GET | /superadmin/tenants/{id}/edit | superadmin.tenants.edit | Show edit form |
| PUT | /superadmin/tenants/{id} | superadmin.tenants.update | Update tenant |
| DELETE | /superadmin/tenants/{id} | superadmin.tenants.destroy | Delete tenant |
| PATCH | /superadmin/tenants/{id}/suspend | superadmin.tenants.suspend | Suspend tenant |
| PATCH | /superadmin/tenants/{id}/activate | superadmin.tenants.activate | Activate tenant |

**Subscriptions Resource:**
| Method | Endpoint | Route Name | Purpose |
|--------|----------|-----------|---------|
| GET | /superadmin/subscriptions | superadmin.subscriptions.index | List all plans |
| GET | /superadmin/subscriptions/create | superadmin.subscriptions.create | Show create form |
| POST | /superadmin/subscriptions | superadmin.subscriptions.store | Store new plan |
| GET | /superadmin/subscriptions/{id} | superadmin.subscriptions.show | Show plan detail |
| GET | /superadmin/subscriptions/{id}/edit | superadmin.subscriptions.edit | Show edit form |
| PUT | /superadmin/subscriptions/{id} | superadmin.subscriptions.update | Update plan |
| DELETE | /superadmin/subscriptions/{id} | superadmin.subscriptions.destroy | Delete plan |

**Authentication:**
| Method | Endpoint | Route Name | Purpose |
|--------|----------|-----------|---------|
| GET | /superadmin/login | superadmin.login | Show login form |
| POST | /superadmin/login | superadmin.login.post | Process login |
| POST | /superadmin/logout | superadmin.logout | Logout user |

---

## 🎯 Quick Tips

1. **Accessing Forms:**
   - Always use route names in links: `route('superadmin.tenants.create')`
   - This ensures links work even if URLs change

2. **Form Submission:**
   - All forms use POST/PUT/DELETE via `@method` directive
   - CSRF protection automatic via `@csrf` in forms

3. **Navigation:**
   - Sidebar always visible on left
   - Click any item to navigate
   - Breadcrumbs show current location

4. **Data Display:**
   - Tables are sortable (click headers)
   - Pagination controls at table bottom
   - Dates are formatted as "Mon DD, YYYY"

5. **Status Indicators:**
   - Green badges = Active/Good
   - Yellow badges = Inactive/Warning
   - Red badges = Suspended/Error

---

## ✨ Responsive Design

- **Desktop:** Full layout with sidebar + content
- **Tablet:** Sidebar collapsible, stacked grid (2-3 columns)
- **Mobile:** Sidebar hidden behind hamburger, single column

All forms and tables automatically adjust to screen size.

---

*Happy managing your SaaS platform! 🚀*
