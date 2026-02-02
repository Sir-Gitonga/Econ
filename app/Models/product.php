<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Product Model
 *
 * Represents a product within a company/tenant.
 * Always scoped to the current company.
 */
class Product extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'SKU',
        'featured',
        'quantity',
        'image',
        'category_id',
        'brand_id',
        'company_id',
    ];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        // Apply company scope to automatically filter by current tenant
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * Get the category this product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the brand this product belongs to.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}

