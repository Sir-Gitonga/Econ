# Settings Module - Code Examples & Usage

## Table of Contents
1. [Accessing Settings](#accessing-settings)
2. [Displaying Settings](#displaying-settings)
3. [Using Settings in Controllers](#using-settings-in-controllers)
4. [Common Patterns](#common-patterns)
5. [Email Configuration](#email-configuration)
6. [Payment Integration](#payment-integration)
7. [Dynamic Styling](#dynamic-styling)

---

## Accessing Settings

### In Controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Get current user's company
        $company = Auth::user()->company;
        
        // Access each settings group
        $generalSettings = $company->companySetting;
        $paymentSettings = $company->paymentSetting;
        $businessSettings = $company->businessSetting;
        
        // Use in logic
        $taxRate = $businessSettings->tax_rate;
        $currency = $generalSettings->currency;
        $invoicePrefix = $businessSettings->invoice_prefix;
        
        // Create order...
    }
}
```

### In Blade Template
```blade
<!-- Single attribute access -->
<h1>{{ auth()->user()->company->companySetting->company_name }}</h1>

<!-- With null coalescing -->
<p>{{ auth()->user()->company->companySetting->email ?? 'No email configured' }}</p>

<!-- In loops -->
@if(auth()->user()->company->paymentSetting->gateway === 'mpesa')
    <button>Pay with M-PESA</button>
@endif
```

### Cache Settings for Better Performance
```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CachedSettings
{
    public function getCompanySettings()
    {
        $companyId = auth()->user()->company_id;
        
        return Cache::remember(
            "company_{$companyId}_settings",
            3600, // 1 hour
            function () {
                return auth()->user()->company->companySetting;
            }
        );
    }
    
    public function clearSettingsCache()
    {
        Cache::forget("company_" . auth()->user()->company_id . "_settings");
    }
}
```

---

## Displaying Settings

### Company Logo in Header
```blade
<div class="header-logo">
    @php
        $logo = auth()->user()->company->companySetting->logo_url;
    @endphp
    <img src="{{ $logo }}" alt="Logo" class="h-10">
</div>
```

### Company Name & Contact
```blade
<div class="company-info">
    <h2>{{ auth()->user()->company->companySetting->company_name }}</h2>
    <p>📧 {{ auth()->user()->company->companySetting->email }}</p>
    <p>📱 {{ auth()->user()->company->companySetting->phone }}</p>
    <p>💬 WhatsApp: {{ auth()->user()->company->companySetting->whatsapp }}</p>
</div>
```

### Theme Colors as CSS Variables
```blade
@php
    $appearance = auth()->user()->company->appearanceSetting;
@endphp

<style>
    :root {
        --primary-color: {{ $appearance->primary_color }};
        --secondary-color: {{ $appearance->secondary_color }};
        --theme-mode: {{ $appearance->theme }};
    }
</style>

<!-- Use in CSS -->
<style>
    .btn-primary {
        background-color: var(--primary-color);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
    }
    
    @media (prefers-color-scheme: {{ $appearance->theme === 'dark' ? 'dark' : 'light' }}) {
        body { /* style based on theme */ }
    }
</style>
```

### Invoice Details
```blade
@php
    $business = auth()->user()->company->businessSetting;
    $general = auth()->user()->company->companySetting;
@endphp

<div class="invoice-header">
    <p><strong>From:</strong></p>
    <p>{{ $general->company_name }}</p>
    <p>{{ $general->address }}</p>
    <p>Tax PIN: {{ $business->vat_pin }}</p>
</div>

<div class="invoice-items">
    <!-- Items here -->
</div>

<div class="invoice-total">
    <p>Subtotal: {{ $subtotal }}</p>
    <p>Tax ({{ $business->tax_rate }}%): {{ $tax = $subtotal * ($business->tax_rate / 100) }}</p>
    <p><strong>Total: {{ $subtotal + $tax }} {{ $general->currency }}</strong></p>
</div>
```

---

## Using Settings in Controllers

### Generate Next Invoice Number
```php
<?php

class InvoiceController extends Controller
{
    public function create()
    {
        $business = auth()->user()->company->businessSetting;
        
        // Get next invoice number (auto-increments)
        $invoiceNumber = $business->getNextInvoiceNumber();
        // Example output: INV-001001
        
        return view('invoices.create', [
            'invoiceNumber' => $invoiceNumber,
            'prefix' => $business->invoice_prefix,
        ]);
    }
}
```

### Apply Tax to Orders
```php
<?php

class OrderController extends Controller
{
    public function calculateTotal($subtotal)
    {
        $taxRate = auth()->user()->company->businessSetting->tax_rate;
        
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
```

### Format Currency for Display
```php
<?php

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $currency = auth()->user()->company->companySetting->currency;
        
        return view('products.show', [
            'product' => $product,
            'formattedPrice' => number_format($product->price, 2) . ' ' . $currency,
        ]);
    }
}
```

### Check Payment Gateway Configuration
```php
<?php

class PaymentController extends Controller
{
    public function process(Order $order)
    {
        $payment = auth()->user()->company->paymentSetting;
        
        // Route based on configured gateway
        if ($payment->gateway === 'mpesa' || $payment->gateway === 'both') {
            return $this->processMpesaPayment($order, $payment);
        }
        
        if ($payment->gateway === 'intasend' || $payment->gateway === 'both') {
            return $this->processIntasendPayment($order, $payment);
        }
        
        return back()->with('error', 'No payment gateway configured');
    }
    
    private function processMpesaPayment($order, $payment)
    {
        // Use $payment->mpesa_paybill, $payment->mpesa_consumer_key, etc.
        // Payment logic here
    }
}
```

### Send Email Using Company SMTP
```php
<?php

class OrderConfirmationMail extends Mailable
{
    public function envelope()
    {
        $settings = auth()->user()->company->communicationSetting;
        
        return new Envelope(
            from: new Address(
                $settings->smtp_from_address,
                $settings->smtp_from_name
            ),
            subject: 'Order Confirmation',
        );
    }
}
```

### Check Session Timeout Setting
```php
<?php

class SessionMiddleware
{
    public function handle($request, $next)
    {
        $timeout = auth()->user()->company->businessSetting->session_timeout_minutes;
        
        // Configure session timeout
        config(['session.lifetime' => $timeout]);
        
        return $next($request);
    }
}
```

### Check 2FA Requirement
```php
<?php

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $company = auth()->user()->company;
        
        if ($company->businessSetting->two_factor_enabled) {
            // Redirect to 2FA verification
            return redirect()->route('2fa.verify');
        }
        
        return redirect()->route('admin.dashboard');
    }
}
```

---

## Common Patterns

### Pattern 1: Settings with Fallback
```php
<?php

// If setting not found, use default
$taxRate = optional(auth()->user()->company->businessSetting)->tax_rate ?? 0;
$currency = optional(auth()->user()->company->companySetting)->currency ?? 'KES';
```

### Pattern 2: Create Settings if Not Exist
```php
<?php

class SettingsTrait
{
    public function ensureSettings()
    {
        $company = auth()->user()->company;
        
        CompanySetting::firstOrCreate(
            ['company_id' => $company->id],
            ['company_name' => $company->company_name]
        );
        
        AppearanceSetting::firstOrCreate(['company_id' => $company->id]);
        PaymentSetting::firstOrCreate(['company_id' => $company->id]);
        CommunicationSetting::firstOrCreate(['company_id' => $company->id]);
        BusinessSetting::firstOrCreate(['company_id' => $company->id]);
    }
}
```

### Pattern 3: Settings Validation Helper
```php
<?php

class SettingsValidator
{
    public static function isPaymentConfigured(): bool
    {
        $payment = auth()->user()->company->paymentSetting;
        
        $mpesaConfigured = !empty($payment->mpesa_paybill) && 
                          !empty($payment->mpesa_consumer_key);
        
        $intasendConfigured = !empty($payment->intasend_publishable_key) && 
                             !empty($payment->intasend_secret_key);
        
        return $mpesaConfigured || $intasendConfigured;
    }
    
    public static function isEmailConfigured(): bool
    {
        $comm = auth()->user()->company->communicationSetting;
        
        return !empty($comm->smtp_host) && 
               !empty($comm->smtp_username) && 
               !empty($comm->smtp_password);
    }
}

// Usage
if (!SettingsValidator::isPaymentConfigured()) {
    return redirect()->route('admin.settings')->with('error', 'Configure payment gateway first');
}
```

### Pattern 4: Settings Observer for Audit Trail
```php
<?php

namespace App\Observers;

use App\Models\CompanySetting;

class CompanySettingObserver
{
    public function updated(CompanySetting $setting)
    {
        \Log::info('Settings updated', [
            'company_id' => $setting->company_id,
            'changes' => $setting->getChanges(),
            'user_id' => auth()->id(),
        ]);
    }
}

// Register in AppServiceProvider
public function boot()
{
    CompanySetting::observe(CompanySettingObserver::class);
}
```

---

## Email Configuration

### Using Company SMTP Settings
```php
<?php

// config/mail.php
'mailers' => [
    'company_smtp' => [
        'transport' => 'smtp',
        'host' => config('COMPANY_SMTP_HOST'),
        'port' => config('COMPANY_SMTP_PORT'),
        'encryption' => config('COMPANY_SMTP_ENCRYPTION'),
        'username' => config('COMPANY_SMTP_USERNAME'),
        'password' => config('COMPANY_SMTP_PASSWORD'),
    ],
],
```

### Dynamically Send Email
```php
<?php

use Illuminate\Support\Facades\Mail;

class OrderNotification
{
    public static function send($order)
    {
        $comm = auth()->user()->company->communicationSetting;
        $general = auth()->user()->company->companySetting;
        
        // Check if email notifications enabled
        if (!$comm->email_notifications_enabled) {
            return;
        }
        
        Mail::to($order->customer_email)
            ->from($comm->smtp_from_address, $comm->smtp_from_name)
            ->send(new OrderConfirmation($order));
    }
}
```

---

## Payment Integration

### M-PESA Configuration
```php
<?php

class MpesaPaymentService
{
    private $payment;
    
    public function __construct()
    {
        $this->payment = auth()->user()->company->paymentSetting;
    }
    
    public function processPayment($amount, $phone)
    {
        $mpesaConfig = [
            'paybill' => $this->payment->mpesa_paybill,
            'consumer_key' => $this->payment->mpesa_consumer_key,
            'consumer_secret' => $this->payment->mpesa_consumer_secret,
            'passkey' => $this->payment->mpesa_passkey,
            'environment' => $this->payment->mpesa_environment,
        ];
        
        // Initialize Mpesa SDK with config
        // Call Mpesa API
    }
}
```

### IntaSend Configuration
```php
<?php

class IntasendPaymentService
{
    private $payment;
    
    public function __construct()
    {
        $this->payment = auth()->user()->company->paymentSetting;
    }
    
    public function processPayment($amount, $email)
    {
        $intasendConfig = [
            'publishable_key' => $this->payment->intasend_publishable_key,
            'secret_key' => $this->payment->intasend_secret_key,
            'mode' => $this->payment->intasend_mode,
        ];
        
        // Initialize IntaSend SDK with config
        // Call IntaSend API
    }
}
```

---

## Dynamic Styling

### Apply Company Colors to Buttons
```blade
<button style="background-color: {{ auth()->user()->company->appearanceSetting->primary_color }};">
    Pay Now
</button>

<a href="#" style="color: {{ auth()->user()->company->appearanceSetting->secondary_color }};">
    Learn More
</a>
```

### Theme-Based Styling
```blade
@php
    $theme = auth()->user()->company->appearanceSetting->theme;
    $isDark = $theme === 'dark';
@endphp

<div class="@if($isDark) dark-theme @else light-theme @endif">
    <!-- Content here -->
</div>

@if($isDark)
    <style>
        body { background: #1f2937; color: #f3f4f6; }
    </style>
@else
    <style>
        body { background: #f9fafb; color: #111827; }
    </style>
@endif
```

### Dynamic Invoice Template
```blade
@php
    $template = auth()->user()->company->appearanceSetting->invoice_template;
@endphp

@switch($template)
    @case('default')
        @include('invoices.templates.default')
        @break
    @case('modern')
        @include('invoices.templates.modern')
        @break
    @case('professional')
        @include('invoices.templates.professional')
        @break
    @case('detailed')
        @include('invoices.templates.detailed')
        @break
@endswitch
```

---

## Advanced: Custom Helper Function

Create `app/Helpers/SettingsHelper.php`:

```php
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class SettingsHelper
{
    public static function company()
    {
        return Auth::user()->company;
    }
    
    public static function general()
    {
        return self::company()->companySetting;
    }
    
    public static function appearance()
    {
        return self::company()->appearanceSetting;
    }
    
    public static function payment()
    {
        return self::company()->paymentSetting;
    }
    
    public static function business()
    {
        return self::company()->businessSetting;
    }
    
    public static function communication()
    {
        return self::company()->communicationSetting;
    }
    
    public static function currency()
    {
        return self::general()->currency ?? 'KES';
    }
    
    public static function taxRate()
    {
        return self::business()->tax_rate ?? 0;
    }
    
    public static function invoicePrefix()
    {
        return self::business()->invoice_prefix ?? 'INV';
    }
    
    public static function formatMoney($amount)
    {
        return number_format($amount, 2) . ' ' . self::currency();
    }
}
```

Register in `config/app.php`:
```php
'aliases' => [
    'SettingsHelper' => App\Helpers\SettingsHelper::class,
]
```

Usage:
```blade
<!-- In Blade -->
{{ SettingsHelper::formatMoney(1000) }}
<!-- Output: 1,000.00 KES -->

{{ SettingsHelper::company()->company_name }}
{{ SettingsHelper::appearance()->primary_color }}
```

---

## Testing Examples

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\CompanySetting;

class SettingsTest extends TestCase
{
    public function test_can_access_settings()
    {
        $company = Company::factory()->create();
        $setting = CompanySetting::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($company->users()->first())
             ->get(route('admin.settings'))
             ->assertStatus(200);
    }
    
    public function test_settings_are_company_scoped()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $setting1 = CompanySetting::factory()->create(['company_id' => $company1->id]);
        $setting2 = CompanySetting::factory()->create(['company_id' => $company2->id]);
        
        $this->assertNotEquals($setting1->id, $setting2->id);
    }
}
```

---

**Examples Version:** 1.0.0  
**Last Updated:** January 24, 2026
