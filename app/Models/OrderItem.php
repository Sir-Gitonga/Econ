<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * OrderItem Model
 *
 * Represents items within an order (company/tenant).
 * Always scoped to the current company.
 */
class OrderItem extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price', 'subtotal', 'refunded_quantity', 'company_id'];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * Get the product for this order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order this item belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
