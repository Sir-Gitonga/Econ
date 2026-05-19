<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Company Model
 *
 * Represents a tenant/company in the multi-tenant SaaS.
 * Stored in central database, not tenant-specific.
 */
class Company extends Model
{
    use HasFactory;

    // Prevent lazy loading to avoid circular reference memory issues
    protected $with = [];
    protected $withCount = [];

    protected $fillable = [
        'company_name',
        'slug',
        'email',
        'phone',
        'domain',
        'logo',
        'address',
        'city',
        'country',
        'business_type',
        'password',
        'status',
        'current_plan_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get all users belonging to this company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all roles for this company.
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get all products for this company.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all categories for this company.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get company settings
     */
    public function companySetting()
    {
        return $this->hasOne(CompanySetting::class);
    }

    /**
     * Get appearance settings
     */
    public function appearanceSetting()
    {
        return $this->hasOne(AppearanceSetting::class);
    }

    /**
     * Get payment settings
     */
    public function paymentSetting()
    {
        return $this->hasOne(PaymentSetting::class);
    }

    /**
     * Get communication settings
     */
    public function communicationSetting()
    {
        return $this->hasOne(CommunicationSetting::class);
    }

    /**
     * Get business settings
     */
    public function businessSetting()
    {
        return $this->hasOne(BusinessSetting::class);
    }

    /**
     * Get the subdomain for this company.
     * Format: companyname.example.com
     */
    public function getSubdomainAttribute(): string
    {
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'example.com';
        return "{$this->slug}.{$mainDomain}";
    }

    /**
     * Get SMS settings
     */
    public function smsSetting()
    {
        return $this->hasOne(CompanySmsSetting::class);
    }

    /**
     * Get WhatsApp settings
     */
    public function whatsappSetting()
    {
        return $this->hasOne(CompanyWhatsappSetting::class);
    }

    /**
     * Get tenant subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    /**
     * Get current subscription plan
     */
    public function currentPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'current_plan_id');
    }

    /**
     * Get the full URL for accessing this company's storefront.
     */
    public function getUrlAttribute(): string
    {
        $scheme = config('app.url') ? parse_url(config('app.url'), PHP_URL_SCHEME) : 'https';
        return "{$scheme}://{$this->subdomain}";
    }

    /**
     * Check if tenant is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is valid
     */
    public function hasValidSubscription()
    {
        $currentSub = $this->subscriptions()->where('status', 'active')->first();
        return $currentSub && !$currentSub->isExpired();
    }

    /**
     * Get payments for this company
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get successful payments
     */
    public function completedPayments()
    {
        return $this->payments()->where('status', 'completed');
    }

    /**
     * Check if company is in trial period
     */
    public function isInTrial(): bool
    {
        if (!$this->trial_started_at || !$this->trial_ends_at) {
            return false;
        }
        return now()->lessThanOrEqualTo($this->trial_ends_at);
    }

    /**
     * Get remaining trial days
     */
    public function getRemainingTrialDays(): int
    {
        if (!$this->isInTrial()) {
            return 0;
        }
        return (int) now()->diffInDays($this->trial_ends_at);
    }

    /**
     * Check if trial payment notification should show (day 25+)
     */
    public function shouldShowPaymentDueNotification(): bool
    {
        if (!$this->trial_started_at) {
            return false;
        }
        $daysElapsed = $this->trial_started_at->diffInDays(now());
        return $daysElapsed >= 25 && !$this->trial_notified_day_25;
    }

    /**
     * Check if payment is available (not within 10 days of expiry)
     */
    public function isPaymentAvailable(): bool
    {
        if (!$this->subscription_expires_at) {
            return true; // No expiry set, payment available
        }
        $daysUntilExpiry = now()->diffInDays($this->subscription_expires_at);
        return $daysUntilExpiry > 10; // Can only pay if more than 10 days until expiry
    }

    /**
     * Get days until subscription expiry
     */
    public function getDaysUntilExpiry(): int
    {
        if (!$this->subscription_expires_at) {
            return 999; // No expiry set
        }
        $days = now()->diffInDays($this->subscription_expires_at);
        return max(0, $days);
    }

    /**
     * Start trial period (30 days from now)
     */
    public function startTrial(): void
    {
        $this->update([
            'trial_started_at' => now(),
            'trial_ends_at' => now()->addDays(30),
            'subscription_status' => 'trial',
            'trial_notified_day_25' => false,
        ]);
    }

    /**
     * Activate subscription with given plan
     */
    public function activateSubscription(SubscriptionPlan $plan): void
    {
        $this->update([
            'current_plan_id' => $plan->id,
            'subscription_started_at' => now(),
            'subscription_expires_at' => now()->addMonth(),
            'subscription_status' => 'active',
        ]);
    }
}




