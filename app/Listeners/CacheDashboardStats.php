<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\DashboardStatsService;

class CacheDashboardStats
{
    /**
     * Handle the event.
     * Stores up-to-date dashboard totals in the cache so the next
     * admin dashboard render can be fast. Cache key is company-specific.
     *
     * @param  \App\Events\OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $companyId = $event->order->company_id;
        $cacheKey = "dashboard_stats_company_{$companyId}";

        $service = app(DashboardStatsService::class);
        $data = $service->dashboardTotals();

        // store for a while; listener will refresh on every new order
        cache()->put($cacheKey, $data, now()->addMinutes(30));
    }
}
