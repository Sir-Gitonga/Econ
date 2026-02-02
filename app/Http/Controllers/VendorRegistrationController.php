<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorRegistrationController extends Controller
{
    public function showForm()
    {
        return view('vendor.register');
    }

    public function store(Request $request)
    {
        // Validate vendor and customer registration
        $rules = [
            'email' => 'required|email|unique:users,email|unique:vendors,email',
            'name' => 'required|string',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:customer,vendor',
        ];

        if ($request->role === 'vendor') {
            $rules['phone'] = 'required|string|unique:vendors,phone';
            $rules['country'] = 'required|string';
            $rules['business_name'] = 'required|string|unique:vendors,business_name';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            // ✅ Vendor registration
            if ($request->role === 'vendor') {
                $vendor = Vendor::create([
                    'business_name' => $request->business_name,
                    'subdomain' => Str::slug($request->business_name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'country' => $request->country,
                ]);

                $user = User::create([
                    'vendor_id' => $vendor->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'admin',
                ]);

                // 🔧 Automatically create isolated vendor tables
                $this->createVendorTables($vendor->subdomain);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Vendor registered successfully!',
                    'subdomain' => $vendor->subdomain . '.' . config('app.base_domain'),
                ]);
            }

            // ✅ Customer registration
            else {
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'customer',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Customer registered successfully!',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dynamically create vendor-specific tables (isolated per vendor).
     */
    protected function createVendorTables($subdomain)
    {
        $vendorSchema = 'vendor_' . str_replace('-', '_', $subdomain);

        // Create schema if not exists
        DB::statement("CREATE SCHEMA IF NOT EXISTS `$vendorSchema`");

        // Create slides table
        DB::statement("
            CREATE TABLE IF NOT EXISTS `$vendorSchema`.`slides` (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                image VARCHAR(255),
                description TEXT,
                status ENUM('active','inactive') DEFAULT 'active',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");

        // Create users/customers table
        DB::statement("
            CREATE TABLE IF NOT EXISTS `$vendorSchema`.`customers` (
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
    }

    // AJAX validation endpoints
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists() ||
                  Vendor::where('email', $request->email)->exists();
        return response()->json(['valid' => !$exists]);
    }

    public function checkPhone(Request $request)
    {
        $exists = Vendor::where('phone', $request->phone)->exists();
        return response()->json(['valid' => !$exists]);
    }

    public function checkName(Request $request)
    {
        $exists = Vendor::where('business_name', $request->business_name)->exists();
        return response()->json(['valid' => !$exists]);
    }
}
