<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyRegistrationController extends Controller
{
    public function showForm()
    {
        return view('company.register');
    }

    public function store(Request $request)
    {
        // Validate input for company registration
        $validated = $request->validate([
            'email' => 'required|email|unique:companies,email',
            'company_name' => 'required|string|unique:companies,company_name',
            'phone' => 'required|string|min:10',
            'address' => 'required|string|min:5',
            'city' => 'nullable|string',
            'country' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        DB::beginTransaction();

        try {
            // Create company record
            $company = Company::create([
                'company_name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create admin user for the company
            $user = User::create([
                'company_id' => $company->id,
                'name' => $validated['company_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
            ]);

            // Create company-specific settings records
            $this->createCompanySettings($company);

            DB::commit();

            return redirect()->route('login')->with('success', 'Company registered successfully! Please log in with your credentials.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create default company settings on registration
     */
    private function createCompanySettings($company)
    {
        \App\Models\CompanySetting::firstOrCreate(['company_id' => $company->id]);
        \App\Models\AppearanceSetting::firstOrCreate(['company_id' => $company->id]);
        \App\Models\PaymentSetting::firstOrCreate(['company_id' => $company->id]);
        \App\Models\CommunicationSetting::firstOrCreate(['company_id' => $company->id]);
        \App\Models\BusinessSetting::firstOrCreate(['company_id' => $company->id]);
    }

    /**
     * Dynamically create vendor-specific tables for their subdomain.
     * You can extend this function later.
     */
    protected function createVendorTables($subdomain)
    {
        $vendorDB = 'vendor_' . str_replace('-', '_', $subdomain);

        // Example: create a separate database for the vendor if you want
        DB::statement("CREATE DATABASE IF NOT EXISTS `$vendorDB`");

        // Switch to vendor DB
        DB::statement("USE `$vendorDB`");

        // Create slides table
        DB::statement("
            CREATE TABLE IF NOT EXISTS slides (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                image VARCHAR(255),
                description TEXT,
                status ENUM('active','inactive') DEFAULT 'active',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");

        // Create users (customers) table for vendor
        DB::statement("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255) UNIQUE,
                password VARCHAR(255),
                phone VARCHAR(20) NULL,
                address VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");

        // You can add more vendor tables (e.g. orders, products, etc.)
    }

    // AJAX validation methods
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists() ||
                  Company::where('email', $request->email)->exists();
        return response()->json(['valid' => !$exists]);
    }

    public function checkPhone(Request $request)
    {
        $exists = Company::where('phone', $request->phone)->exists();
        return response()->json(['valid' => !$exists]);
    }

    public function checkName(Request $request)
    {
        $exists = Company::where('name', $request->name)->exists();
        return response()->json(['valid' => !$exists]);
    }
}
