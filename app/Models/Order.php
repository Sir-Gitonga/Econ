<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Order Model
 *
 * Represents customer orders within a company/tenant.
 * Always scoped to the current company.
 */
class Order extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['user_id', 'subtotal', 'discount', 'tax', 'total', 'firstname', 'lastname', 'mobile', 'email', 'line1', 'line2', 'city', 'province', 'country', 'zipcode', 'status', 'is_shipping_different', 'delivered_date', 'canceled_date', 'company_id'];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * Get the user who placed this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get items in this order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get transaction for this order.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
