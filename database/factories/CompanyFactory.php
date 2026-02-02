<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->company();
        $slug = Str::slug($name) . '-' . Str::random(4);

        return [
            'company_name' => $name,
            'slug' => $slug,
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'domain' => null,
            'logo' => null,
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'business_type' => 'retail',
            'password' => null,
        ];
    }
}
