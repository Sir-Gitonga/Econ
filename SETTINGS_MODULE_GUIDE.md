# Company Settings Module - Implementation Guide

## Overview

A complete, production-ready Laravel multi-tenant SaaS settings module for managing company configurations, appearance, payments, communications, and business settings.

## Features

✅ **7 Settings Sections:**
- General (company info, logo, contact details)
- Appearance (colors, theme, invoice template)
- Payments (M-PESA, IntaSend configuration)
- Business (about, mission, vision, invoice settings, security)
- Communication (SMTP, SMS, notifications)

✅ **Security:**
- Multi-tenant isolation (company-scoped queries)
- Encrypted sensitive fields (API keys, passwords)
- Role-based access control
- CSRF protection

✅ **User Experience:**
- Tabbed interface with Alpine.js
- Real-time color preview
- Responsive design (Tailwind CSS)
- Form validation with error messages
- Success notifications

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── CompanySettingsController.php
├── Models/
│   ├── CompanySetting.php
│   ├── AppearanceSetting.php
│   ├── PaymentSetting.php
│   ├── CommunicationSetting.php
│   └── BusinessSetting.php
│
database/
├── migrations/
│   ├── 2026_01_24_000001_create_company_settings_table.php
│   ├── 2026_01_24_000002_create_appearance_settings_table.php
│   ├── 2026_01_24_000003_create_payment_settings_table.php
│   ├── 2026_01_24_000004_create_communication_settings_table.php
│   └── 2026_01_24_000005_create_business_settings_table.php
│
resources/
└── views/
    └── admin/
        └── settings/
            ├── index.blade.php
            └── partials/
                ├── general.blade.php
                ├── appearance.blade.php
                ├── payments.blade.php
                ├── business.blade.php
                └── communication.blade.php

routes/
└── web.php (settings routes added)
```

## Installation Steps

### 1. Run Migrations

Create all database tables:

```bash
php artisan migrate
```

This creates:
- `company_settings` - General company information
- `appearance_settings` - Branding and theme
- `payment_settings` - Payment gateway credentials
- `communication_settings` - Email and SMS configuration
- `business_settings` - Business info and security settings

### 2. Access the Settings Page

Navigate to your admin panel:

```
http://admin.yourdomain.com/admin/settings
```

Or via subdomain for multi-tenant:

```
http://companyname.localhost/admin/settings
```

## Routes

All routes require `auth` middleware and admin role:

```php
// Settings page
GET  /admin/settings                 → CompanySettingsController@index

// Update endpoints
POST /admin/settings/general         → CompanySettingsController@updateGeneral
POST /admin/settings/appearance      → CompanySettingsController@updateAppearance
POST /admin/settings/payment         → CompanySettingsController@updatePayment
POST /admin/settings/business        → CompanySettingsController@updateBusiness
POST /admin/settings/communication   → CompanySettingsController@updateCommunication
```

## Database Schema

### company_settings
```sql
- id (PK)
- company_id (FK)
- company_name (string)
- logo (string, nullable) - file path
- email (string)
- phone (string, nullable)
- whatsapp (string, nullable)
- address (text, nullable)
- timezone (string) - default: Africa/Nairobi
- currency (string) - default: KES
- timestamps
```

### appearance_settings
```sql
- id (PK)
- company_id (FK)
- primary_color (string) - hex color
- secondary_color (string) - hex color
- theme (enum: light|dark)
- invoice_template (string)
- favicon (string, nullable)
- timestamps
```

### payment_settings
```sql
- id (PK)
- company_id (FK)
- gateway (enum: mpesa|intasend|both)
- mpesa_paybill (string, nullable)
- mpesa_consumer_key (text, nullable)
- mpesa_consumer_secret (text, encrypted, nullable)
- mpesa_passkey (text, encrypted, nullable)
- mpesa_environment (enum: sandbox|live)
- intasend_publishable_key (text, nullable)
- intasend_secret_key (text, encrypted, nullable)
- intasend_mode (enum: test|live)
- timestamps
```

### communication_settings
```sql
- id (PK)
- company_id (FK)
- smtp_host (string, nullable)
- smtp_port (integer, nullable)
- smtp_username (string, nullable)
- smtp_password (text, encrypted, nullable)
- smtp_from_address (string, nullable)
- smtp_from_name (string, nullable)
- smtp_encryption (enum: tls|ssl, nullable)
- sms_api_key (text, encrypted, nullable)
- sms_provider (string, nullable)
- email_notifications_enabled (boolean)
- sms_notifications_enabled (boolean)
- timestamps
```

### business_settings
```sql
- id (PK)
- company_id (FK)
- about_description (text, nullable)
- mission (text, nullable)
- vision (text, nullable)
- services (text, nullable)
- invoice_prefix (string) - default: INV
- tax_rate (decimal) - 0-100
- vat_pin (string, nullable)
- invoice_number_counter (integer)
- session_timeout_minutes (integer) - default: 30
- two_factor_enabled (boolean) - default: false
- timestamps
```

## Controller Methods

### CompanySettingsController

#### `index()`
- Display all settings sections
- Auto-initialize settings if not exist
- Pass all settings to view

#### `updateGeneral(Request $request)`
- Validate general settings
- Handle logo upload
- Create/update company settings
- Return success message

#### `updateAppearance(Request $request)`
- Validate colors (hex format)
- Handle favicon upload
- Update appearance settings

#### `updatePayment(Request $request)`
- Validate based on selected gateway
- M-PESA validation if selected
- IntaSend validation if selected
- Sensitive fields auto-encrypted

#### `updateBusiness(Request $request)`
- Validate business info
- Update invoice settings
- Save security preferences

#### `updateCommunication(Request $request)`
- Validate SMTP settings
- Validate SMS configuration
- Encrypted password fields

#### `initializeSettings(Company $company)`
- Create default settings for new companies
- Called automatically on first access

## Models

### CompanySetting
```php
$companySetting->company()          // BelongsTo Company
$companySetting->logo_url           // Get logo URL attribute
```

### AppearanceSetting
```php
AppearanceSetting::getInvoiceTemplates()  // ['default', 'modern', ...]
AppearanceSetting::getThemes()             // ['light', 'dark']
$appearanceSetting->favicon_url            // Get favicon URL
```

### PaymentSetting
```php
PaymentSetting::getGateways()           // ['mpesa', 'intasend', 'both']
PaymentSetting::getMpesaEnvironments()  // ['sandbox', 'live']
PaymentSetting::getIntasendModes()      // ['test', 'live']
```

### CommunicationSetting
```php
CommunicationSetting::getEncryptionOptions()  // ['tls', 'ssl']
CommunicationSetting::getSmsProviders()       // ['twilio', 'vonage', ...]
```

### BusinessSetting
```php
$businessSetting->getNextInvoiceNumber()  // Generate invoice number
```

## Accessing Settings in Code

### In Controller
```php
$company = Auth::user()->company;

// Get settings
$companySetting = $company->companySetting;
$paymentSetting = $company->paymentSetting;

// Access attributes
echo $companySetting->company_name;
echo $paymentSetting->mpesa_paybill;
```

### In Blade Template
```blade
{{ auth()->user()->company->companySetting->company_name }}
{{ auth()->user()->company->appearanceSetting->primary_color }}
```

### Create Invoice Number
```php
$businessSetting = auth()->user()->company->businessSetting;
$invoiceNumber = $businessSetting->getNextInvoiceNumber();
// Output: INV001001, INV001002, etc.
```

## Security Considerations

### 1. Encryption
Sensitive fields are automatically encrypted:
- `PaymentSetting::mpesa_consumer_secret`
- `PaymentSetting::mpesa_passkey`
- `PaymentSetting::intasend_secret_key`
- `CommunicationSetting::smtp_password`
- `CommunicationSetting::sms_api_key`

Implement in models by adding to `$encrypted` property:
```php
protected $encrypted = [
    'mpesa_consumer_secret',
    'mpesa_passkey',
    // ...
];
```

### 2. Multi-Tenant Isolation
All settings are scoped to company via:
```php
$company->companySetting()  // BelongsTo relationship
CompanySetting::where('company_id', $company_id)->first()
```

### 3. File Uploads
- Stored in `/storage/app/public/company-logos/`
- Size limit: 2MB for logos, 512KB for favicons
- Allowed formats validated server-side
- Delete old files when updating

### 4. Access Control
```php
// Middleware requires auth + admin role
Route::middleware(['auth', Role::class . ':admin'])
```

## Form Validation Rules

### General Settings
```php
'company_name' => 'required|string|max:255',
'email' => 'required|email|max:255',
'phone' => 'nullable|string|max:20',
'whatsapp' => 'nullable|string|max:20',
'address' => 'nullable|string|max:500',
'timezone' => 'required|timezone',
'currency' => 'required|string|size:3',
'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
```

### Appearance Settings
```php
'primary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
'secondary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
'theme' => 'required|in:light,dark',
'invoice_template' => 'required|in:default,modern,professional,detailed',
```

### Payment Settings (M-PESA)
```php
'mpesa_paybill' => 'required|string|max:10',
'mpesa_consumer_key' => 'required|string|max:255',
'mpesa_consumer_secret' => 'required|string|max:255',
'mpesa_passkey' => 'required|string|max:255',
'mpesa_environment' => 'required|in:sandbox,live',
```

### Business Settings
```php
'invoice_prefix' => 'required|string|max:10',
'tax_rate' => 'required|numeric|min:0|max:100',
'session_timeout_minutes' => 'required|integer|min:5|max:1440',
```

## Common Use Cases

### 1. Send Email Using Company SMTP
```php
$setting = auth()->user()->company->communicationSetting;

Mail::mailer('custom')->send(new OrderConfirmation($order));
```

### 2. Apply Company Colors in Blade
```blade
<style>
    :root {
        --primary: {{ auth()->user()->company->appearanceSetting->primary_color }};
        --secondary: {{ auth()->user()->company->appearanceSetting->secondary_color }};
    }
</style>
```

### 3. Display Company Logo
```blade
<img src="{{ auth()->user()->company->companySetting->logo_url }}" alt="Logo">
```

### 4. Format Prices with Company Currency
```php
$currency = auth()->user()->company->companySetting->currency;
echo number_format($price, 2) . ' ' . $currency;
```

### 5. Get Next Invoice Number
```php
$invoiceNum = auth()->user()->company->businessSetting->getNextInvoiceNumber();
```

## Testing

### Manual Testing Checklist
- [ ] Access `/admin/settings` without login (should redirect)
- [ ] Login as admin and navigate to settings
- [ ] Update general settings with logo upload
- [ ] Update appearance colors and verify form sync
- [ ] Select M-PESA gateway and verify IntaSend fields hidden
- [ ] Select M-PESA gateway and verify M-PESA fields visible
- [ ] Update payment credentials
- [ ] Update business settings
- [ ] Update communication settings
- [ ] Verify error messages for validation failures
- [ ] Verify success messages after updates
- [ ] Logout and verify settings persist
- [ ] Login with different company (verify isolation)

### Unit Test Example
```php
public function test_company_settings_are_isolated()
{
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();
    
    $setting1 = CompanySetting::create([
        'company_id' => $company1->id,
        'company_name' => 'Company 1',
    ]);
    
    $setting2 = CompanySetting::where('company_id', $company2->id)->first();
    $this->assertNull($setting2);
}
```

## Troubleshooting

### Issue: Settings page shows 404
**Solution:** Ensure routes are registered in `routes/web.php` and middleware is applied

### Issue: Logo not uploading
**Solution:** Check storage symlink exists
```bash
php artisan storage:link
```

### Issue: Encrypted fields not working
**Solution:** Ensure `APP_KEY` is set in `.env` file
```bash
php artisan key:generate
```

### Issue: File permissions error
**Solution:** Fix storage directory permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Extending the Module

### Add New Settings Section

1. **Create Migration:**
```php
Schema::create('new_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
    $table->string('field_name');
    $table->timestamps();
});
```

2. **Create Model:**
```php
class NewSetting extends Model {
    protected $fillable = ['company_id', 'field_name'];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
}
```

3. **Add to Company Model:**
```php
public function newSetting() {
    return $this->hasOne(NewSetting::class);
}
```

4. **Create Controller Method:**
```php
public function updateNew(Request $request) {
    // Validation and update logic
}
```

5. **Add Route:**
```php
Route::post('/settings/new', [CompanySettingsController::class, 'updateNew']);
```

6. **Create Blade Partial:**
```blade
<!-- resources/views/admin/settings/partials/new.blade.php -->
```

7. **Add Tab to Main View:**
```blade
<div x-show="activeTab === 'new'">
    @include('admin.settings.partials.new')
</div>
```

## Performance Optimization

### Cache Settings
```php
// In controller
$settings = cache()->remember(
    "company_{$company->id}_settings",
    3600,
    fn() => $company->companySetting
);

// Clear on update
cache()->forget("company_{$company->id}_settings");
```

### Lazy Load Relationships
```php
// Only load when needed
$company->load('companySetting', 'paymentSetting');
```

## Support & Documentation

For issues or questions:
1. Check this guide
2. Review code comments in models and controller
3. Check Laravel documentation for concepts used
4. Inspect browser console for JavaScript errors

## License & Credits

Built for Softifyx SaaS Platform
Laravel 11.40.0 | PHP 8.4.2
