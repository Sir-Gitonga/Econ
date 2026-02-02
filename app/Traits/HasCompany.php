<?php

namespace App\Traits;

use App\Models\Company;

trait HasCompany
{
    /**
     * Boot the HasCompany trait.
     * Automatically set company_id on model creation.
     */
    protected static function bootHasCompany()
    {
        static::creating(function ($model) {
            // Auto-assign company_id when creating if not already set
            if (empty($model->company_id) && app()->has('company')) {
                $company = app('company');
                if ($company && isset($company->id)) {
                    $model->company_id = $company->id;
                }
            }
        });
    }

    /**
     * Scope queries to a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|null  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (app()->has('company') ? app('company')->id : null);

        if ($companyId) {
            return $query->where($this->getTable() . '.company_id', $companyId);
        }

        return $query;
    }

    /**
     * Get the company that owns this model.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
