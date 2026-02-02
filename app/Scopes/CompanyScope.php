<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * CompanyScope - Global scope for multi-tenant isolation
 *
 * Automatically filters records to the current company/tenant.
 * Applied to any model that should only return data for the authenticated user's company.
 *
 * Only applies when:
 * - User is authenticated
 * - User has a company_id
 * - Request is on a tenant subdomain (app('company') is set)
 */
class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Only apply scope if we have a current company context
        if (app()->has('company') && app('company')) {
            $builder->where($model->getTable() . '.company_id', app('company')->id);
        }
        // Note: DO NOT call auth()->user() here to avoid circular references during user loading
        // The auth()->user() call creates an infinite loop when loading the user model itself
        // Instead, rely on the middleware to set the app('company') context
    }
}

