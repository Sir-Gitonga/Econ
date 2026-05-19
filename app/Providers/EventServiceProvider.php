<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderCreated;
use App\Listeners\CacheDashboardStats;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderCreated::class => [
            CacheDashboardStats::class,
        ],
    ];
}
