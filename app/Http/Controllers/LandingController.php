<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Category;
use App\Models\Slide;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * LandingController
 *
 * Handles all landing page routes (main domain only).
 * No tenant context - displays marketing/public information.
 */
class LandingController extends Controller
{
    /**
     * Show pricing page.
     */
    public function pricing()
    {
        $plans = [
            [
                'name' => 'Starter',
                'price' => 29,
                'features' => ['Up to 100 products', 'Basic support', 'Custom domain'],
            ],
            [
                'name' => 'Professional',
                'price' => 99,
                'features' => ['Unlimited products', 'Priority support', 'Analytics', 'Custom domain'],
            ],
            [
                'name' => 'Enterprise',
                'price' => 299,
                'features' => ['Everything', 'Dedicated support', 'API access', 'Custom integrations'],
            ],
        ];

        return view('landing.pricing', compact('plans'));
    }

    /**
     * Show features page.
     */
    public function features()
    {
        $features = [
            [
                'title' => 'Multi-Tenant SaaS',
                'description' => 'Each store is completely isolated with its own data and customization.',
            ],
            [
                'title' => 'Product Management',
                'description' => 'Easily manage products, categories, and inventory.',
            ],
            [
                'title' => 'Order Management',
                'description' => 'Track and manage customer orders in real-time.',
            ],
            [
                'title' => 'Payment Processing',
                'description' => 'Secure payment handling with multiple payment methods.',
            ],
        ];

        return view('landing.features', compact('features'));
    }
}
