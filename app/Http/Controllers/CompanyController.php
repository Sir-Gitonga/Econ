<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function showRegisterForm()
    {
        return view('company.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255|unique:companies,company_name',
            'email' => 'required|email|max:255|unique:companies,email',
            'phone' => 'required',
            'address' => 'required',
            'business_type' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
        ], [
            'company_name.unique' => 'This business name is already registered.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Generate slug for subdomain
        $slug = Str::slug($request->company_name);

        // Prevent duplicate slugs
        if (Company::where('slug', $slug)->exists()) {
            return back()->withErrors([
                'company_name' => 'A company with a similar name already exists. Try a different name.'
            ])->withInput();
        }

        try {
            // Create company
            $company = Company::create([
                'company_name' => $request->company_name,
                'slug' => $slug,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'business_type' => $request->business_type,
                'password' => Hash::make($request->password),
                'domain' => "{$slug}.localhost",
            ]);

            // Verify company was created
            if (!$company || !$company->id) {
                throw new \Exception('Company creation failed - no ID returned');
            }

            // Create admin user linked to this company
            $user = User::create([
                'name' => $company->company_name . ' Admin',
                'email' => $company->email,
                'password' => $company->password, // already hashed
                'mobile' => $company->phone,
                'company_id' => $company->id,
                'role' => 'admin',
            ]);

            // Verify user was created
            if (!$user || !$user->id) {
                throw new \Exception('Admin user creation failed');
            }

            // Log successful registration
            \Log::info('Company registered successfully', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'slug' => $company->slug,
            ]);

            // Redirect to tenant subdomain home with proper port
            $tenantUrl = "http://{$company->slug}.localhost:8000/";

            return redirect()->away($tenantUrl);
        } catch (\Exception $e) {
            \Log::error('Company registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => [
                    'company_name' => $request->company_name,
                    'email' => $request->email,
                ]
            ]);

            return back()->withErrors([
                'error' => 'Registration failed: ' . $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
