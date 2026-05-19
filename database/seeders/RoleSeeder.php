<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Role;

/**
 * RoleSeeder
 * 
 * Seeds default roles for each company.
 * Should run after company seeding.
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();

        // Define default roles
        $roleDefinitions = [
            [
                'name' => 'admin',
                'label' => 'Administrator',
                'description' => 'Full access to all features and user management',
            ],
            [
                'name' => 'cashier',
                'label' => 'Cashier',
                'description' => 'Can perform POS sales and view their own sales',
            ],
            [
                'name' => 'user',
                'label' => 'Customer/User',
                'description' => 'Regular user/customer with limited access',
            ],
        ];

        // Create roles for each company
        foreach ($companies as $company) {
            foreach ($roleDefinitions as $roleDef) {
                // Check if role already exists for this company
                $exists = Role::where('company_id', $company->id)
                    ->where('name', $roleDef['name'])
                    ->exists();

                if (!$exists) {
                    Role::create([
                        'company_id' => $company->id,
                        'name' => $roleDef['name'],
                        'label' => $roleDef['label'],
                        'description' => $roleDef['description'],
                    ]);
                }
            }
        }

        $this->command->info('Roles seeded successfully for all companies.');
    }
}
