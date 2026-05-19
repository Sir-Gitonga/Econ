<?php

namespace App\SuperAdmin;

use App\Models\Company;
use App\SuperAdmin\Requests\StoreTenantRequest;
use App\SuperAdmin\Requests\UpdateTenantRequest;
use App\SuperAdmin\Services\TenantService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = $this->tenantService->getAllTenants();

        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('superadmin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(StoreTenantRequest $request)
    {
        $tenant = $this->tenantService->createTenant($request->validated());

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Company $tenant)
    {
        $tenant->load(['users', 'currentPlan', 'subscriptions.plan']);

        return view('superadmin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Company $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(UpdateTenantRequest $request, Company $tenant)
    {
        $this->tenantService->updateTenant($tenant, $request->validated());

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Company $tenant)
    {
        $this->tenantService->deleteTenant($tenant);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Suspend the specified tenant.
     */
    public function suspend(Company $tenant)
    {
        $this->tenantService->suspendTenant($tenant);

        return redirect()->back()
            ->with('success', 'Tenant suspended successfully.');
    }

    /**
     * Activate the specified tenant.
     */
    public function activate(Company $tenant)
    {
        $this->tenantService->activateTenant($tenant);

        return redirect()->back()
            ->with('success', 'Tenant activated successfully.');
    }
}