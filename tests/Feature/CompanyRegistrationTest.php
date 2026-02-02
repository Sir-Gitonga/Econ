<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;

class CompanyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registering_on_subdomain_associates_user_with_company_and_redirects()
    {
        // Create a company with slug 'shopif'
        $company = Company::factory()->create([
            'slug' => 'shopif',
            'company_name' => 'Shopif',
        ]);

        // Simulate posting to register on subdomain host
        $response = $this->post('http://shopif.softifyx.localhost:8000/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'mobile' => '0712345678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('tenant.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'company_id' => $company->id,
        ]);
    }
}
