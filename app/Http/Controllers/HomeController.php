<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Check if this is a tenant request (middleware sets app('company'))
        $company = app()->has('company') ? app('company') : null;

        if ($company) {
            // Tenant domain: show company-specific content
            // All queries are auto-scoped by CompanyScope, but we're explicit here
            $slides = Slide::where('status', 1)->take(3)->get();
            $categories = Category::orderBy('name')->get();

            $sproducts = Product::whereNotNull('sale_price')
                ->where('sale_price', '<>', '')
                ->inRandomOrder()
                ->take(8)
                ->get();

            $fproducts = Product::where('featured', 1)->take(8)->get();

            return view('index', compact('slides', 'categories', 'sproducts', 'fproducts'));
        }

        // Main domain view: show landing page
        $slides = collect();
        $categories = collect();
        $sproducts = collect();
        $fproducts = collect();

        return view('welcome', compact('slides', 'categories', 'sproducts', 'fproducts'));
    }
    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email',
        'phone' => 'required|numeric|digits:10',
       'message' => 'required|string|max:1000'
    ]);

    $contact = new Contact();
    $contact->name = $request->name;
    $contact->email = $request->email;
    $contact->phone = $request->phone;
    $contact->message = $request->message;
    $contact->save();
    return redirect()->back()->with('success','Your message has been sent successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name','LIKE',"%{$query}%")->get()->take(8);
        return response()->json($results);
    }
}
