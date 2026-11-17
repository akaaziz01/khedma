<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Khedma Admin',
            'email' => 'admin@khedma.ma',
            'password' => 'AdminPass123!',
            'role' => UserRole::ADMIN,
            'status' => 'active',
        ]);

        $token = $admin->createToken('admin_token', ['role:admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'metrics' => [
                    'total_users',
                    'clients_count',
                    'providers_count',
                    'admins_count',
                    'total_bookings',
                    'platform_gmv',
                ],
                'users',
                'bookings',
            ]);
    }

    public function test_non_admin_client_is_forbidden_from_admin_area(): void
    {
        $client = User::create([
            'name' => 'Regular Client',
            'email' => 'client@khedma.ma',
            'password' => 'Password123!',
            'role' => UserRole::CLIENT,
            'status' => 'active',
        ]);

        $token = $client->createToken('client_token', ['role:client'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Forbidden: Administrator privileges required to access this resource.']);
    }

    public function test_non_admin_provider_is_forbidden_from_admin_area(): void
    {
        $provider = User::create([
            'name' => 'Regular Provider',
            'email' => 'provider@khedma.ma',
            'password' => 'Password123!',
            'role' => UserRole::PROVIDER,
            'status' => 'active',
        ]);

        $token = $provider->createToken('provider_token', ['role:provider'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_guest_is_unauthorized(): void
    {
        $response = $this->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(401);
    }

    public function test_admin_can_suspend_user_and_revoke_their_access(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin2@khedma.ma',
            'password' => 'AdminPass123!',
            'role' => UserRole::ADMIN,
        ]);
        $adminToken = $admin->createToken('admin_token')->plainTextToken;

        $targetClient = User::create([
            'name' => 'Bad User',
            'email' => 'bad@khedma.ma',
            'password' => 'Password123!',
            'role' => UserRole::CLIENT,
            'status' => 'active',
        ]);
        $targetClient->createToken('client_device');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $targetClient->id,
        ]);

        // Admin suspends user
        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson("/api/v1/admin/users/{$targetClient->id}/status", [
                'status' => 'suspended',
            ]);

        $response->assertStatus(200);

        // Check user status updated and tokens revoked
        $this->assertDatabaseHas('users', [
            'id' => $targetClient->id,
            'status' => 'suspended',
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $targetClient->id,
        ]);

        // Suspended user cannot log in
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'bad@khedma.ma',
            'password' => 'Password123!',
        ]);
        $loginRes->assertStatus(403);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin3@khedma.ma',
            'password' => 'AdminPass123!',
            'role' => UserRole::ADMIN,
        ]);
        $adminToken = $admin->createToken('admin_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson("/api/v1/admin/users/{$admin->id}/status", [
                'status' => 'suspended',
            ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Action denied: Administrators cannot suspend their own account.']);
    }

    public function test_admin_dashboard_web_view_is_accessible(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Operations Center');
    }
}
