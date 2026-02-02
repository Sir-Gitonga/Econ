<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    // Ensure only logged-in vendors can access this controller
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a list of vendor’s products.
     */
    public function index()
    {
        $vendorId = Auth::id();
        $products = Product::where('vendor_id', $vendorId)->latest()->get();

        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        return view('vendor.products.create');
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $vendorId = Auth::id();

        $data = [
            'vendor_id'   => $vendorId,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category'    => $request->category,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("vendors/{$vendorId}/products", 'public');
            $data['image'] = $path;
        }

        Product::create($data);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $vendorId = Auth::id();
        $product = Product::where('vendor_id', $vendorId)->findOrFail($id);

        return view('vendor.products.edit', compact('product'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $vendorId = Auth::id();
        $product = Product::where('vendor_id', $vendorId)->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product->fill([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category'    => $request->category,
        ]);

        // Replace image if new one uploaded
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store("vendors/{$vendorId}/products", 'public');
            $product->image = $path;
        }

        $product->save();

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $vendorId = Auth::id();
        $product = Product::where('vendor_id', $vendorId)->findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
