<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'Basic plan for small businesses',
                'price' => 3999,
                'features' => ['Up to 5 users', 'Basic support', '1GB storage'],
            ],
            [
                'name' => 'Pro',
                'description' => 'Professional plan for growing businesses',
                'price' => 7999,
                'features' => ['Up to 20 users', 'Priority support', '10GB storage', 'Advanced analytics'],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Enterprise plan for large organizations',
                'price' => 12999,
                'features' => ['Unlimited users', '24/7 support', '100GB storage', 'Custom integrations', 'Dedicated account manager'],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}