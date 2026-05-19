@extends('superadmin.layouts.app')

@section('page-title', 'Subscription Plans')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Subscription Plans</h1>
    <a href="{{ route('superadmin.subscriptions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Create New Plan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($plans as $plan)
    <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">Ksh {{ number_format($plan->price) }}<span class="text-lg text-gray-600">/month</span></p>
            </div>
        </div>

        
        <p class="text-gray-600 mb-4 flex-grow">{{ $plan->description ?? 'Premium subscription plan' }}</p>

        <div class="mb-4">
            <h4 class="font-semibold text-gray-900 mb-2">Features:</h4>
            @if($plan->features && count($plan->features) > 0)
                <ul class="space-y-1">
                    @foreach($plan->features as $feature)
                    <li class="text-sm text-gray-600">✓ {{ $feature }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">No features defined</p>
            @endif
        </div>

        <div class="border-t pt-4 mb-4">
            <p class="text-sm text-gray-600">
                <span class="font-semibold">{{ $plan->tenantSubscriptions()->count() }}</span> tenant{{ $plan->tenantSubscriptions()->count() !== 1 ? 's' : '' }} using this plan
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('superadmin.subscriptions.show', $plan) }}" class="flex-1 bg-indigo-600 text-white text-center px-3 py-2 rounded hover:bg-indigo-700 text-sm">View Details</a>
            <a href="{{ route('superadmin.subscriptions.edit', $plan) }}" class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded hover:bg-blue-700 text-sm">Edit</a>
            <form method="POST" action="{{ route('superadmin.subscriptions.destroy', $plan) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="delete" data-message="Delete this plan? Active subscriptions will not be affected." class="w-full bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-500 mb-4">No subscription plans created yet.</p>
        <a href="{{ route('superadmin.subscriptions.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Create First Plan
        </a>
    </div>
    @endforelse
</div>

@if($plans->hasPages())
    <div class="mt-8">
        {{ $plans->links() }}
    </div>
@endif
@endsection
