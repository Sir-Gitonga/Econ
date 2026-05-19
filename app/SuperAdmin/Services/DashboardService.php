<?php

namespace App\SuperAdmin\Services;

use App\Models\Company;
use App\Models\User;

class DashboardService
{
    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats()
    {
        return [
            'total_tenants' => Company::count(),
            'active_tenants' => Company::where('status', 'active')->count(),
            'inactive_tenants' => Company::where('status', 'inactive')->count(),
            'suspended_tenants' => Company::where('status', 'suspended')->count(),
            'total_users' => User::count(),
            'active_subscriptions' => \App\Models\TenantSubscription::where('status', 'active')->count(),
            'recent_tenants' => Company::latest()->take(5)->get(),
            'total_revenue' => 0, // Placeholder
        ];
    }
}