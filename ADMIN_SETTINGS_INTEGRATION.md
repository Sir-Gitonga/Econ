# Admin Settings Module Integration Guide

## ✅ Integration Complete

The Company Settings module has been fully integrated into the Softifyx admin panel.

---

## 🔗 Access Points

### 1. **Sidebar Navigation**
- **Location:** Admin panel left sidebar
- **Menu Item:** Settings ⚙️
- **Route:** `/admin/settings`
- **Icon:** icon-settings

```blade
<a href="{{ route('admin.settings') }}" class="">
    <div class="icon"><i class="icon-settings"></i></div>
    <div class="text">Settings</div>
</a>
```

### 2. **Dashboard Card**
- **Location:** Admin dashboard (bottom of statistics)
- **Title:** Company Settings
- **Description:** Manage branding, payments, communication & business settings
- **Design:** Gradient card with indigo accent border
- **Action:** Click arrow or card to navigate to settings

```blade
<!-- Settings Module Card -->
<div class="w-full">
    <div class="wg-chart-default bg-gradient-to-r from-indigo-50 to-blue-50 border-l-4 border-indigo-600">
        <div class="flex items-center justify-between">
            <!-- Settings Card Content -->
        </div>
    </div>
</div>
```

---

## 📍 File Locations

### Modified Files
1. **[resources/views/layouts/admin.blade.php](resources/views/layouts/admin.blade.php)**
   - Updated sidebar menu Settings link
   - Changed from static `settings.html` to dynamic route

2. **[resources/views/admin/dashboard.blade.php](resources/views/admin/dashboard.blade.php)**
   - Added Company Settings promotional card
   - Positioned at bottom of statistics

### Settings Module Files
- **Controller:** [app/Http/Controllers/CompanySettingsController.php](app/Http/Controllers/CompanySettingsController.php)
- **Views:** [resources/views/admin/settings/](resources/views/admin/settings/)
  - `index.blade.php` - Main settings page
  - `partials/general.blade.php` - General settings
  - `partials/appearance.blade.php` - Appearance settings
  - `partials/payments.blade.php` - Payment settings
  - `partials/business.blade.php` - Business settings
  - `partials/communication.blade.php` - Communication settings

- **Models:** [app/Models/](app/Models/)
  - `CompanySetting.php`
  - `AppearanceSetting.php`
  - `PaymentSetting.php`
  - `CommunicationSetting.php`
  - `BusinessSetting.php`

- **Migrations:** [database/migrations/](database/migrations/)
  - `2026_01_24_000001_create_company_settings_table.php`
  - `2026_01_24_000002_create_appearance_settings_table.php`
  - `2026_01_24_000003_create_payment_settings_table.php`
  - `2026_01_24_000004_create_communication_settings_table.php`
  - `2026_01_24_000005_create_business_settings_table.php`

- **Routes:** [routes/web.php](routes/web.php)
  - All settings routes configured with auth + admin middleware

---

## 🎯 User Workflow

### Access Settings from Admin Panel

**Option 1: Via Sidebar**
1. Login to admin panel
2. Click **Settings ⚙️** in left sidebar
3. View all settings with 5 tabs

**Option 2: Via Dashboard**
1. Login to admin panel
2. Scroll down to "Company Settings" card
3. Click card or arrow button
4. View all settings with 5 tabs

### Configure Settings

Once on the settings page, users can:

**General Tab** 📋
- Company name
- Logo upload with preview
- Email & phone numbers
- Address
- Timezone selection
- Currency selection

**Appearance Tab** 🎨
- Primary & secondary colors (with picker)
- Theme selection (light/dark)
- Invoice template selection
- Favicon upload
- Live color preview

**Payments Tab** 💳
- Select payment gateway (M-PESA, IntaSend, or Both)
- Configure M-PESA credentials
- Configure IntaSend credentials
- Choose environment (sandbox/live)

**Business Tab** 📊
- About description
- Mission & vision statements
- Services offered
- Invoice prefix & counter
- Tax rate configuration
- VAT PIN
- Session timeout settings
- Two-factor authentication toggle

**Communication Tab** 📧
- SMTP configuration (for email sending)
- SMS provider setup (Twilio, Vonage, Africa's Talking)
- Notification preferences
- Help examples for setup

---

## 🔐 Security & Access Control

### Multi-Tenant Protection
✅ Each company can only access their own settings
✅ Settings scoped to authenticated company
✅ No cross-company data exposure

### Role-Based Access
✅ Admin role required to access settings
✅ Settings link only shows for admins
✅ Unauthenticated users redirected to login

### Data Protection
✅ Sensitive fields encrypted (API keys, passwords)
✅ File uploads validated server-side
✅ CSRF protection on all forms
✅ Input validation on all fields

---

## 📊 Settings Structure

### Company Settings Table
```
id                  | bigint, primary key
company_id          | bigint, foreign key (unique)
company_name        | string
logo                | string (file path)
email               | string
phone               | string
whatsapp            | string
address             | text
timezone            | string
currency            | string
created_at          | timestamp
updated_at          | timestamp
```

### Appearance Settings Table
```
id                  | bigint, primary key
company_id          | bigint, foreign key (unique)
primary_color       | string (hex color)
secondary_color     | string (hex color)
theme               | enum ('light', 'dark')
invoice_template    | string
favicon             | string (file path)
created_at          | timestamp
updated_at          | timestamp
```

### Payment Settings Table
```
id                  | bigint, primary key
company_id          | bigint, foreign key (unique)
gateway             | string (enum)
mpesa_paybill       | string
mpesa_consumer_key  | string (encrypted)
mpesa_consumer_secret | string (encrypted)
mpesa_passkey       | string (encrypted)
mpesa_environment   | string
intasend_publishable_key | string
intasend_secret_key | string (encrypted)
intasend_mode       | string
created_at          | timestamp
updated_at          | timestamp
```

### Communication Settings Table
```
id                  | bigint, primary key
company_id          | bigint, foreign key (unique)
smtp_host           | string
smtp_port           | integer
smtp_username       | string
smtp_password       | string (encrypted)
smtp_from_address   | string
smtp_from_name      | string
smtp_encryption     | string
sms_provider        | string
sms_api_key         | string (encrypted)
email_notifications_enabled | boolean
sms_notifications_enabled | boolean
created_at          | timestamp
updated_at          | timestamp
```

### Business Settings Table
```
id                  | bigint, primary key
company_id          | bigint, foreign key (unique)
about_description   | text
mission             | text
vision              | text
services            | text
invoice_prefix      | string
tax_rate            | decimal
vat_pin             | string
invoice_number_counter | integer
session_timeout_minutes | integer
two_factor_enabled  | boolean
created_at          | timestamp
updated_at          | timestamp
```

---

## 🚀 Usage Examples

### Access Settings in Controller
```php
// Get company settings
$company = Auth::user()->company;
$settings = $company->companySetting;
$appearance = $company->appearanceSetting;
$payment = $company->paymentSetting;
$communication = $company->communicationSetting;
$business = $company->businessSetting;

// Use in logic
$taxRate = $business->tax_rate;
$smtpHost = $communication->smtp_host;
$primaryColor = $appearance->primary_color;
```

### Display in Blade View
```blade
<!-- Company branding -->
<img src="{{ $company->companySetting->logo_url }}" alt="Company Logo">
<h1>{{ $company->companySetting->company_name }}</h1>

<!-- Apply theme -->
<body class="{{ $company->appearanceSetting->theme === 'dark' ? 'dark-theme' : 'light-theme' }}">

<!-- Use company colors -->
<button style="background-color: {{ $company->appearanceSetting->primary_color }}">
    Click Me
</button>

<!-- Display tax rate -->
<p>Tax: {{ $company->businessSetting->tax_rate }}%</p>
```

### Send Email with Company SMTP
```php
$comm = $company->communicationSetting;

Mail::mailer('custom')->send(new MyMailable(), [
    'host' => $comm->smtp_host,
    'port' => $comm->smtp_port,
    'username' => $comm->smtp_username,
    'password' => $comm->smtp_password,
    'from' => $comm->smtp_from_address,
]);
```

### Generate Invoice Number
```php
$business = $company->businessSetting;
$nextNumber = $business->getNextInvoiceNumber();
// Returns: "INV-001001", "INV-001002", etc.
```

---

## 📋 Integration Checklist

- [x] Updated admin sidebar with Settings route
- [x] Added Settings card to dashboard
- [x] All 5 migrations runnable
- [x] All 5 models created with relationships
- [x] Controller fully functional
- [x] 6 Blade views complete (1 main + 5 partials)
- [x] Routes configured with middleware
- [x] Company model updated with relationships
- [x] File upload handling implemented
- [x] Form validation rules configured
- [x] Encryption for sensitive fields
- [x] Multi-tenant isolation enforced
- [x] Responsive design implemented
- [x] Alpine.js tab switching working
- [x] Success/error messaging added
- [x] Documentation created

---

## 🎓 Next Steps

1. **Verify Installation**
   ```bash
   php artisan migrate  # If not already done
   ```

2. **Access Settings**
   - Login to admin panel
   - Click "Settings" in sidebar or dashboard card
   - Fill in your company information

3. **Configure Each Section**
   - General: Company info & logo
   - Appearance: Colors, theme, favicon
   - Payments: Payment gateway setup
   - Business: Invoice, tax, security settings
   - Communication: Email & SMS setup

4. **Use Settings in Your App**
   - Reference the "Usage Examples" above
   - Check SETTINGS_CODE_EXAMPLES.md for 50+ examples
   - Refer to SETTINGS_MODULE_GUIDE.md for advanced usage

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| [SETTINGS_MODULE_GUIDE.md](SETTINGS_MODULE_GUIDE.md) | Complete technical documentation |
| [SETTINGS_IMPLEMENTATION_CHECKLIST.md](SETTINGS_IMPLEMENTATION_CHECKLIST.md) | Implementation guide with checklist |
| [SETTINGS_CODE_EXAMPLES.md](SETTINGS_CODE_EXAMPLES.md) | 50+ code examples for common scenarios |
| [QUICK_START_SETTINGS.md](QUICK_START_SETTINGS.md) | Quick reference & setup guide |
| [SETTINGS_MODULE_DELIVERY_SUMMARY.md](SETTINGS_MODULE_DELIVERY_SUMMARY.md) | Complete delivery summary |
| [ADMIN_SETTINGS_INTEGRATION.md](ADMIN_SETTINGS_INTEGRATION.md) | This file - integration guide |

---

## 🆘 Troubleshooting

### Settings Link Not Showing
- Verify user has admin role
- Clear Laravel cache: `php artisan cache:clear`
- Check routes registered: `php artisan route:list | grep settings`

### Settings Card Not Appearing
- Ensure migrations ran: `php artisan migrate:status`
- Check view file: `resources/views/admin/dashboard.blade.php`
- Verify no JavaScript errors in browser console

### Can't Access Settings Page
- Ensure authenticated with admin role
- Check middleware in routes/web.php
- Verify CompanySettingsController exists
- Check auth middleware: `auth` and `role:admin`

### File Uploads Not Working
- Verify storage link exists: `php artisan storage:link`
- Check file permissions in storage/app/public
- Verify file size under 2MB for logos, 512KB for favicons

### Settings Not Saving
- Check form validation in controller
- Verify CSRF token in form
- Check database connection
- Look at Laravel logs: `storage/logs/laravel.log`

---

## 📞 Quick Reference

| Item | Location |
|------|----------|
| Settings Menu | Admin Panel > Left Sidebar |
| Settings Page | `/admin/settings` |
| Dashboard Card | Admin Panel > Bottom of Statistics |
| Controller | `app/Http/Controllers/CompanySettingsController.php` |
| Views | `resources/views/admin/settings/` |
| Models | `app/Models/[Setting]Setting.php` (5 models) |
| Migrations | `database/migrations/2026_01_24_*.php` (5 migrations) |
| Documentation | `SETTINGS_*.md` files (5 docs) |

---

## ✨ Features Summary

✅ **5 Settings Sections** - General, Appearance, Payments, Business, Communication
✅ **40+ Fields** - Comprehensive configuration options
✅ **Responsive Design** - Works on mobile, tablet, desktop
✅ **Real-time Preview** - Live color preview, theme preview
✅ **Encrypted Fields** - Sensitive data protected
✅ **File Uploads** - Logo, favicon with validation
✅ **Multi-tenant** - Complete isolation per company
✅ **Role-based Access** - Admin only
✅ **Form Validation** - Server-side validation rules
✅ **Error Handling** - User-friendly error messages
✅ **Success Notifications** - Confirmation on save
✅ **Alpine.js Tabs** - Smooth tab switching
✅ **Tailwind Styling** - Beautiful, modern UI
✅ **Production Ready** - Enterprise-grade code

---

**Status:** ✅ **FULLY INTEGRATED & READY TO USE**

Access your settings from the admin panel today!
