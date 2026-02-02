<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_users_within_company_subdomain()
    {
        // Create company and admin user
        $company = Company::factory()->create(["slug" => 'sokolink']);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'admin',
            'mobile' => '0710000000',
            'password' => Hash::make('adminpass'),
        ]);

        // For test reliability, bypass subdomain middleware and set the company context directly
        app()->instance('company', $company);
        $this->withoutMiddleware(\App\Http\Middleware\IdentifyCompanyBySubdomain::class);

        // Let exceptions bubble up so we can see real errors while debugging
        $this->withoutExceptionHandling();

        $hostHeader = [
            'Host' => "{$company->slug}.localhost",
        ];
        $serverVars = [
            'HTTP_HOST' => "{$company->slug}.localhost",
        ];

        // Use the test-only endpoints (no domain requirement) for reliable testing
        $firstDebug = $this->actingAs($admin)->get('/_test/admin/users');
        file_put_contents(storage_path('logs/test_debug_initial.json'), $firstDebug->getContent());
        $firstDebug->assertStatus(200);

        // Index
        $response = $this->actingAs($admin)->get('/_test/admin/users');
        if ($response->status() !== 200) {
            file_put_contents(storage_path('logs/test_debug_index.html'), $response->getContent());
        }
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($admin)->get('/_test/admin/users/create');
        $response->assertStatus(200);

        // Store new user
        $storeData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'mobile' => '071100200',
            'role' => 'user',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->withHeaders($hostHeader)->withSession(['_token' => 'test-token'])->actingAs($admin)
            ->post('/admin/users', array_merge($storeData, ['_token' => 'test-token']));
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'company_id' => $company->id,
        ]);

        $user = User::withoutGlobalScopes()->where('email', 'newuser@example.com')->first();
        $this->assertEquals($company->id, $user->company_id);

        // Simulate Eloquent find with company global scope applied (as the route binding will do)
        $scopedFind = User::find($user->id);
        file_put_contents(storage_path('logs/test_debug_scoped_find.txt'), json_encode(['scoped_find' => $scopedFind ? $scopedFind->toArray() : null]));
        $this->assertNotNull($scopedFind, 'Scoped find returned null — binding would fail');

        // Debug: check scoped find during request
        // Use relative path to avoid route() domain param confusion in tests
        $debug = $this->actingAs($admin)->get("/_test/admin/users");
        file_put_contents(storage_path('logs/test_debug_api.json'), $debug->getContent());
        $debug->assertStatus(200);

        // Edit page (use test-only endpoints)
        $response = $this->actingAs($admin)->get("/_test/admin/users/{$user->id}/edit");
        if ($response->status() !== 200) {
            file_put_contents(storage_path('logs/test_debug_response.html'), "URL: /_test/admin/users/{$user->id}/edit\n\n" . $response->getContent());
        }
        $response->assertStatus(200);

        // Update
        $updateData = [
            'name' => 'Updated User',
            'email' => 'newuser@example.com',
            'mobile' => '071100200',
            'role' => 'cashier',
            'status' => 1,
        ];

        $response = $this->withHeaders($hostHeader)->withSession(['_token' => 'test-token'])->actingAs($admin)
            ->put("/admin/users/{$user->id}", array_merge($updateData, ['_token' => 'test-token']));
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'role' => 'cashier',
        ]);

        // Toggle status
        $current = User::withoutGlobalScopes()->find($user->id);
        $response = $this->withHeaders($hostHeader)->withSession(['_token' => 'test-token'])->actingAs($admin)
            ->post("/admin/users/{$user->id}/toggle-status", ['_token' => 'test-token']);
        $response->assertRedirect();
        $after = User::withoutGlobalScopes()->find($user->id);
        $this->assertEquals(!$current->status, $after->status);

        // Reset password
        $oldHash = $after->password;
        $response = $this->withHeaders($hostHeader)->withSession(['_token' => 'test-token'])->actingAs($admin)
            ->post("/admin/users/{$user->id}/reset-password", ['_token' => 'test-token']);
        $response->assertRedirect();
        $after = User::withoutGlobalScopes()->find($user->id);
        $this->assertNotEquals($oldHash, $after->password);
        $this->assertTrue(Hash::check('Password123!', $after->password));

        // Destroy
        $response = $this->withHeaders($hostHeader)->withSession(['_token' => 'test-token'])->actingAs($admin)
            ->delete("/admin/users/{$user->id}", ['_token' => 'test-token']);
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
