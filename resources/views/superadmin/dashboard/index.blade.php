@extends('superadmin.layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<!-- KPIs Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <!-- Total Tenants -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-lg shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Tenants</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_tenants'] }}</p>
            </div>
            <svg class="w-12 h-12 text-blue-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3z"></path>
            </svg>
        </div>
        <p class="text-blue-100 text-xs mt-4">{{ $stats['active_tenants'] }} active</p>
    </div>

    <!-- Active Tenants -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-lg shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-green-100 text-sm font-medium">Active Tenants</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['active_tenants'] }}</p>
            </div>
            <svg class="w-12 h-12 text-green-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <p class="text-green-100 text-xs mt-4">Running smoothly</p>
    </div>

    <!-- Suspended Tenants -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-lg shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-red-100 text-sm font-medium">Suspended</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['suspended_tenants'] }}</p>
            </div>
            <svg class="w-12 h-12 text-red-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 0110 2a6 6 0 11-4.477 12.89zm-.07-7.07a1 1 0 10-1.414 1.414L9.586 10l-1.414 1.414a1 1 0 101.414 1.414L11 11.414l1.414 1.414a1 1 0 101.414-1.414L12.414 10l1.414-1.414a1 1 0 10-1.414-1.414L11 8.586l-1.414-1.414z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <p class="text-red-100 text-xs mt-4">Require attention</p>
    </div>

    <!-- Total Users -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-lg shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-purple-100 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_users'] }}</p>
            </div>
            <svg class="w-12 h-12 text-purple-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a9 9 0 01-9 0h18a9 9 0 01-9 0z"></path>
            </svg>
        </div>
        <p class="text-purple-100 text-xs mt-4">Across all tenants</p>
    </div>

    <!-- Subscriptions -->
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-lg shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-indigo-100 text-sm font-medium">Active Subscriptions</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['active_subscriptions'] ?? 0 }}</p>
            </div>
            <svg class="w-12 h-12 text-indigo-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
        </div>
        <p class="text-indigo-100 text-xs mt-4">Generating revenue</p>
    </div>
</div>

<!-- Quick Actions & Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('superadmin.tenants.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                + Create New Tenant
            </a>
            <a href="{{ route('superadmin.subscriptions.create') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                + Create Subscription Plan
            </a>
            <a href="{{ route('superadmin.tenants.index') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                Manage Tenants
            </a>
            <a href="{{ route('superadmin.subscriptions.index') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                Manage Subscriptions
            </a>
        </div>
    </div>

    <!-- Tenant Status Distribution -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tenant Status</h3>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                    <span class="text-sm font-bold text-green-600">{{ $stats['active_tenants'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? ($stats['active_tenants'] / $stats['total_tenants'] * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Inactive</span>
                    <span class="text-sm font-bold text-yellow-600">{{ $stats['inactive_tenants'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? ($stats['inactive_tenants'] / $stats['total_tenants'] * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Suspended</span>
                    <span class="text-sm font-bold text-red-600">{{ $stats['suspended_tenants'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? ($stats['suspended_tenants'] / $stats['total_tenants'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-bold text-gray-900 mb-4">System Health</h3>
        <div class="space-y-3">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                <span class="text-sm text-gray-700">All systems operational</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                <span class="text-sm text-gray-700">Database connected</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                <span class="text-sm text-gray-700">API responding</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                <span class="text-sm text-gray-700">Email service active</span>
            </div>
            <hr class="my-3">
            <p class="text-xs text-gray-500">Last updated: {{ now()->format('H:i A') }}</p>
        </div>
    </div>
</div>

<!-- Recent Tenants Table -->
<div class="bg-white p-6 rounded-lg shadow-lg mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-900">Recent Tenant Registrations</h3>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Company Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Users</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Plan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Registered</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($stats['recent_tenants'] as $tenant)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $tenant->company_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $tenant->email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $tenant->users()->count() }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($tenant->currentPlan)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $tenant->currentPlan->name }}</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Plan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : ($tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $tenant->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        <p>No tenants registered yet.</p>
                        <a href="{{ route('superadmin.tenants.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create the first tenant →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Subscription Plans Overview -->
<div class="bg-white p-6 rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-900">Subscription Plans</h3>
        <a href="{{ route('superadmin.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All →</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $plans = \App\Models\SubscriptionPlan::all();
        @endphp
        @forelse($plans as $plan)
        <div class="border rounded-lg p-4">
            <h4 class="font-bold text-gray-900 mb-2">{{ $plan->name }}</h4>
            <p class="text-2xl font-bold text-blue-600 mb-2">Ksh {{ number_format($plan->price) }}/month</p>
            <p class="text-sm text-gray-600 mb-3">{{ $plan->description ?? 'Premium plan' }}</p>
            <div class="space-y-1 mb-4">
                @if($plan->features)
                    @foreach($plan->features as $feature)
                    <p class="text-xs text-gray-600">✓ {{ $feature }}</p>
                    @endforeach
                @endif
            </div>
            <p class="text-xs text-gray-500">{{ $plan->tenantSubscriptions()->count() }} tenant{{ $plan->tenantSubscriptions()->count() !== 1 ? 's' : '' }}</p>
        </div>
        @empty
        <p class="text-gray-500 col-span-3">No subscription plans created yet.</p>
        @endforelse
    </div>
</div>
@endsection
