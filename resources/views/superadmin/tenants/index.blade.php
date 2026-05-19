@extends('superadmin.layouts.app')

@section('page-title', 'Tenants Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Tenants</h1>
    <a href="{{ route('superadmin.tenants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Create New Tenant
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left">Company Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Users</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Plan</th>
                    <th class="px-4 py-2 text-left">Created</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $tenant->company_name }}</td>
                    <td class="px-4 py-2">{{ $tenant->email }}</td>
                    <td class="px-4 py-2">{{ $tenant->users_count }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : ($tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $tenant->currentPlan ? $tenant->currentPlan->name : 'No Plan' }}</td>
                    <td class="px-4 py-2">{{ $tenant->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                        @if($tenant->status === 'active')
                            <form method="POST" action="{{ route('superadmin.tenants.suspend', $tenant) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="button" data-confirm="suspend" data-message="Are you sure you want to suspend this tenant?" class="text-red-600 hover:text-red-800">Suspend</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('superadmin.tenants.activate', $tenant) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="button" data-confirm="activate" data-message="Are you sure you want to activate this tenant?" class="text-green-600 hover:text-green-800">Activate</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-confirm="delete" data-message="Are you sure you want to delete this tenant? This action cannot be undone." class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-2 text-center text-gray-500">No tenants found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tenants->hasPages())
        <div class="px-4 py-3 bg-gray-50 border-t">
            {{ $tenants->links() }}
        </div>
    @endif
</div>
@endsection