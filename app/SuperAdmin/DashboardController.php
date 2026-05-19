<?php

namespace App\SuperAdmin;

use App\Models\Company;
use App\Models\User;
use App\SuperAdmin\Services\DashboardService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show the super admin dashboard.
     */
    public function index()
    {
        $stats = $this->dashboardService->getDashboardStats();

        return view('superadmin.dashboard.index', compact('stats'));
    }
}