<?php

namespace App\SuperAdmin;

use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\SuperAdmin\Requests\StoreSubscriptionRequest;
use App\SuperAdmin\Requests\UpdateSubscriptionRequest;
use App\SuperAdmin\Services\SubscriptionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of subscription plans.
     */
    public function index()
    {
        $plans = $this->subscriptionService->getAllPlans();

        return view('superadmin.subscriptions.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create()
    {
        return view('superadmin.subscriptions.create');
    }

    /**
     * Store a newly created subscription plan.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $plan = $this->subscriptionService->createPlan($request->validated());

        return redirect()->route('superadmin.subscriptions.show', $plan)
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Display the specified subscription plan.
     */
    public function show(SubscriptionPlan $subscription)
    {
        $subscription->load('tenantSubscriptions.company');
        
        $plan = $subscription;
        $activeSubscriptions = $subscription->tenantSubscriptions()
            ->where('status', 'active')
            ->count();
        $totalSubscriptions = $subscription->tenantSubscriptions()->count();
        $monthlyRevenue = $plan->price * $activeSubscriptions;
        $recentSubscriptions = $subscription->tenantSubscriptions()
            ->with('company')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('superadmin.subscriptions.show', compact('plan', 'activeSubscriptions', 'totalSubscriptions', 'monthlyRevenue', 'recentSubscriptions'));
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $subscription)
    {
        return view('superadmin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified subscription plan.
     */
    public function update(UpdateSubscriptionRequest $request, SubscriptionPlan $subscription)
    {
        $this->subscriptionService->updatePlan($subscription, $request->validated());

        return redirect()->route('superadmin.subscriptions.show', $subscription)
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified subscription plan.
     */
    public function destroy(SubscriptionPlan $subscription)
    {
        $this->subscriptionService->deletePlan($subscription);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Assign a subscription plan to a tenant.
     */
    public function assignToTenant(Request $request, Company $tenant)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'expires_at' => 'required|date|after:today',
        ]);

        $this->subscriptionService->assignPlanToTenant($tenant, $request->plan_id, $request->expires_at);

        return redirect()->back()
            ->with('success', 'Subscription assigned successfully.');
    }
}