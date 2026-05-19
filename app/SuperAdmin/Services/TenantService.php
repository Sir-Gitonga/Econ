<?php

namespace App\SuperAdmin\Services;

use App\Models\Company;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Get all tenants with pagination.
     */
    public function getAllTenants()
    {
        return Company::with(['currentPlan', 'users'])
            ->withCount('users')
            ->paginate(15);
    }

    /**
     * Create a new tenant.
     */
    public function createTenant(array $data)
    {
        $data['slug'] = Str::slug($data['company_name']);
        $data['password'] = bcrypt($data['password']);

        return Company::create($data);
    }

    /**
     * Update a tenant.
     */
    public function updateTenant(Company $tenant, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        if (isset($data['company_name'])) {
            $data['slug'] = Str::slug($data['company_name']);
        }

        $tenant->update($data);

        return $tenant;
    }

    /**
     * Delete a tenant.
     */
    public function deleteTenant(Company $tenant)
    {
        // Soft delete or hard delete based on requirements
        $tenant->delete();
    }

    /**
     * Suspend a tenant.
     */
    public function suspendTenant(Company $tenant)
    {
        $tenant->update(['status' => 'suspended']);
    }

    /**
     * Activate a tenant.
     */
    public function activateTenant(Company $tenant)
    {
        $tenant->update(['status' => 'active']);
    }
}