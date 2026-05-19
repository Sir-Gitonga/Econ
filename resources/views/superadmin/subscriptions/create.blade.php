@extends('superadmin.layouts.app')

@section('page-title', 'Create Subscription Plan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Create New Subscription Plan</h1>

    <form method="POST" action="{{ route('superadmin.subscriptions.store') }}" id="planForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Plan Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g., Professional" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Monthly Price (Ksh)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" step="1" min="0" placeholder="3999" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                <select name="currency" id="currency" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="KES" selected>KES (Ksh)</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" placeholder="Describe this plan in detail..." 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                <div id="features" class="space-y-2 mb-2">
                    <div class="flex space-x-2">
                        <input type="text" name="features[]" placeholder="Feature name" class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
                        <button type="button" onclick="removeFeature(this)" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addFeature()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    + Add Feature
                </button>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Plan
            </button>
            <a href="{{ route('superadmin.subscriptions.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function addFeature() {
    const featuresDiv = document.getElementById('features');
    const newFeature = document.createElement('div');
    newFeature.className = 'flex space-x-2';
    newFeature.innerHTML = `
        <input type="text" name="features[]" placeholder="Feature name" class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
        <button type="button" onclick="removeFeature(this)" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Remove</button>
    `;
    featuresDiv.appendChild(newFeature);
}

function removeFeature(button) {
    button.parentElement.remove();
}

// Handle form submission to clean up empty features
document.getElementById('planForm').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('input[name="features[]"]');
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.remove();
        }
    });
});
</script>
@endsection
