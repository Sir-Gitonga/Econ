@extends('superadmin.layouts.app')

@section('page-title', 'Tenant Details')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $tenant->company_name }}</h1>
        <div class="space-x-2">
            <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                Edit
            </a>
            <a href="{{ route('superadmin.tenants.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold mb-4">Company Information</h2>
            <dl class="space-y-2">
                <div>
                    <dt class="font-medium text-gray-700">Name:</dt>
                    <dd>{{ $tenant->company_name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Email:</dt>
                    <dd>{{ $tenant->email }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Phone:</dt>
                    <dd>{{ $tenant->phone ?: 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Domain:</dt>
                    <dd>{{ $tenant->domain ?: 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Status:</dt>
                    <dd>
                        <span class="px-2 py-1 rounded text-xs {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : ($tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Created:</dt>
                    <dd>{{ $tenant->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-4">Subscription Information</h2>
            @if($tenant->currentPlan)
                <dl class="space-y-2">
                    <div>
                        <dt class="font-medium text-gray-700">Current Plan:</dt>
                        <dd>{{ $tenant->currentPlan->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Price:</dt>
                        <dd>${{ number_format($tenant->currentPlan->price, 2) }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-gray-500">No active subscription</p>
            @endif

            <h3 class="text-md font-semibold mt-4 mb-2">Subscription History</h3>
            @forelse($tenant->subscriptions as $subscription)
                <div class="border rounded p-3 mb-2">
                    <div class="flex justify-between">
                        <span>{{ $subscription->plan->name }}</span>
                        <span class="text-sm text-gray-500">{{ $subscription->status }}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Expires: {{ $subscription->expires_at->format('M d, Y') }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No subscription history</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Users ({{ $tenant->users->count() }})</h2>
        @if($tenant->users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Role</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenant->users as $user)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">{{ $user->role ?: 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $user->status ?: 'Active' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No users found</p>
        @endif
    </div>
</div>
@endsection