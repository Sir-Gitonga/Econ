<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Category Model
 *
 * Represents product categories within a company/tenant.
 * Always scoped to the current company.
 */
class Category extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['name', 'slug', 'image', 'parent_id', 'company_id'];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * Get products in this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get parent category (for hierarchical categories).
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
