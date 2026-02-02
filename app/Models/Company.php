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
     * Get the full URL for accessing this company's storefront.
     */
    public function getUrlAttribute(): string
    {
        $scheme = config('app.url') ? parse_url(config('app.url'), PHP_URL_SCHEME) : 'https';
        return "{$scheme}://{$this->subdomain}";
    }
}

