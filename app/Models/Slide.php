<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * Slide Model
 *
 * Represents homepage banner slides within a company/tenant.
 * Always scoped to the current company.
 */
class Slide extends Model
{
    use HasCompany;

    protected $fillable = ['tagline', 'title', 'subtitle', 'link', 'image', 'status', 'company_id'];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
