<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Brand Model
 *
 * Represents product brands within a company/tenant.
 * Always scoped to the current company.
 */
class Brand extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['name', 'slug', 'image', 'company_id'];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * Get products from this brand.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
