<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Coupon Model
 *
 * Represents discount coupons within a company/tenant.
 * Always scoped to the current company.
 */
class Coupon extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['code', 'type', 'value', 'cart_value', 'expiry_date', 'company_id'];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
