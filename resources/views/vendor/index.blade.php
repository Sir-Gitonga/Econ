@extends('layouts.vendor') {{-- use your existing vendor layout file --}}
@section('title', 'Vendor Dashboard')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Welcome, {{ Auth::user()->name }}</h1>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow rounded-2xl p-4 text-center">
            <h3 class="text-gray-500 text-sm uppercase">Total Products</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $totalProducts ?? 0 }}</p>
        </div>

        <div class="bg-white shadow rounded-2xl p-4 text-center">
            <h3 class="text-gray-500 text-sm uppercase">Total Customers</h3>
            <p class="text-3xl font-bold text-green-600">{{ $totalCustomers ?? 0 }}</p>
        </div>

        <div class="bg-white shadow rounded-2xl p-4 text-center">
            <h3 class="text-gray-500 text-sm uppercase">Slides</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $totalSlides ?? 0 }}</p>
        </div>

        <div class="bg-white shadow rounded-2xl p-4 text-center">
            <h3 class="text-gray-500 text-sm uppercase">Sales</h3>
            <p class="text-3xl font-bold text-purple-600">Ksh {{ number_format($totalSales ?? 0, 2) }}</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white shadow rounded-2xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('vendor.products.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                + Add Product
            </a>
            <a href="{{ route('vendor.products.index') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-gray-900 transition">
                Manage Products
            </a>
            <a href="{{ route('vendor.customers.index') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                Manage Customers
            </a>
            <a href="{{ route('vendor.slides.index') }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition">
                Manage Slides
            </a>
        </div>
    </div>

    {{-- Recent Products --}}
    <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Products</h2>
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b text-left">Product</th>
                    <th class="py-2 px-4 border-b text-left">Category</th>
                    <th class="py-2 px-4 border-b text-center">Stock</th>
                    <th class="py-2 px-4 border-b text-center">Price</th>
                    <th class="py-2 px-4 border-b text-center">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentProducts ?? [] as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b">{{ $product->name }}</td>
                        <td class="py-2 px-4 border-b">{{ $product->category }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $product->stock }}</td>
                        <td class="py-2 px-4 border-b text-center">Ksh {{ number_format($product->price, 2) }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $product->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">No products added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
