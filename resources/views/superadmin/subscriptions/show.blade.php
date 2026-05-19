@extends('superadmin.layouts.app')

@section('page-title', $plan->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Plan Details -->
    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold">{{ $plan->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $plan->description }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('superadmin.subscriptions.edit', $plan->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Edit
                    </a>
                    <a href="{{ route('superadmin.subscriptions.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Back
                    </a>
                </div>
            </div>

            <hr class="my-6">

            <!-- Pricing Section -->
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Pricing</h2>
                <div class="bg-blue-50 p-6 rounded border border-blue-200">
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold text-blue-600">Ksh {{ number_format($plan->price) }}</span>
                        <span class="text-gray-600 ml-2">/month</span>
                    </div>
                    <p class="text-gray-600 mt-2">Billed monthly for each subscribed company</p>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Features Included</h2>
                @if($plan->features && count($plan->features) > 0)
                    <ul class="space-y-3">
                        @foreach($plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">No features defined for this plan.</p>
                @endif
            </div>

            <!-- Plan Info -->
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 p-4 rounded">
                    <span class="text-gray-600">Created</span>
                    <p class="font-medium">{{ $plan->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <span class="text-gray-600">Last Updated</span>
                    <p class="font-medium">{{ $plan->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Stats -->
    <div class="space-y-6">
        <!-- Subscription Stats Card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-4">Subscription Stats</h3>
            <div class="space-y-4">
                <div class="border-l-4 border-green-600 pl-4">
                    <p class="text-gray-600 text-sm">Active Subscriptions</p>
                    <p class="text-3xl font-bold text-green-600">{{ $activeSubscriptions }}</p>
                </div>
                <div class="border-l-4 border-yellow-600 pl-4">
                    <p class="text-gray-600 text-sm">Total Subscriptions</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalSubscriptions }}</p>
                </div>
                <div class="border-l-4 border-blue-600 pl-4">
                    <p class="text-gray-600 text-sm">Monthly Revenue</p>
                    <p class="text-3xl font-bold text-blue-600">Ksh {{ number_format($monthlyRevenue) }}</p>
                </div>
            </div>
        </div>

        <!-- Action Card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-4">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('superadmin.subscriptions.edit', $plan->id) }}" class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded hover:bg-blue-700">
                    Edit Plan
                </a>
                <form method="POST" action="{{ route('superadmin.subscriptions.destroy', $plan->id) }}" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="button" data-confirm="delete" data-message="Delete this plan? Active subscriptions will not be affected." class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Delete Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Recent Subscriptions Table -->
@if($recentSubscriptions->count() > 0)
<div class="mt-6 bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Recent Subscriptions to This Plan</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">Company</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Expires</th>
                    <th class="px-4 py-2 text-left">Subscribed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSubscriptions as $subscription)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <a href="{{ route('superadmin.tenants.show', $subscription->company_id) }}" class="text-blue-600 hover:underline">
                                {{ $subscription->company->company_name }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $subscription->company->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-medium @if($subscription->status === 'active') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if($subscription->expires_at)
                                {{ $subscription->expires_at->format('M d, Y') }}
                            @else
                                Never
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $subscription->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="mt-6 bg-blue-50 border border-blue-200 p-6 rounded-lg">
    <p class="text-blue-800">No companies have subscribed to this plan yet.</p>
</div>
@endif
@endsection
