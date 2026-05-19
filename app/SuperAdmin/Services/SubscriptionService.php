<?php

namespace App\SuperAdmin\Services;

use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;

class SubscriptionService
{
    /**
     * Get all subscription plans.
     */
    public function getAllPlans()
    {
        return SubscriptionPlan::withCount('tenantSubscriptions')->paginate(15);
    }

    /**
     * Create a new subscription plan.
     */
    public function createPlan(array $data)
    {
        return SubscriptionPlan::create($data);
    }

    /**
     * Update a subscription plan.
     */
    public function updatePlan(SubscriptionPlan $plan, array $data)
    {
        $plan->update($data);
        return $plan;
    }

    /**
     * Delete a subscription plan.
     */
    public function deletePlan(SubscriptionPlan $plan)
    {
        $plan->delete();
    }

    /**
     * Assign a plan to a tenant.
     */
    public function assignPlanToTenant(Company $tenant, int $planId, string $expiresAt)
    {
        // Deactivate current subscription
        $tenant->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

        // Create new subscription
        TenantSubscription::create([
            'company_id' => $tenant->id,
            'plan_id' => $planId,
            'expires_at' => $expiresAt,
            'status' => 'active',
        ]);

        // Update tenant's current plan
        $tenant->update(['current_plan_id' => $planId]);
    }
}