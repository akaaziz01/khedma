<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\ClientProfile;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function admin(): User
    {
        return User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => 'active',
            'is_verified' => true,
        ]);
    }

    private function client(array $attributes = [], array $profileAttributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::CLIENT,
            'status' => 'active',
            'is_verified' => false,
        ], $attributes));

        ClientProfile::create(array_merge([
            'user_id' => $user->id,
            'city' => 'Tunis',
            'address' => 'Avenue Habib Bourguiba',
        ], $profileAttributes));

        return $user;
    }

    private function provider(array $attributes = [], array $profileAttributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::PROVIDER,
            'status' => 'active',
            'is_verified' => false,
        ], $attributes));

        ProviderProfile::create(array_merge([
            'user_id' => $user->id,
            'business_name' => 'Amrani Services',
            'category' => 'Plumbing',
            'bio' => 'Experienced plumber in Tunis',
            'skills' => ['Leak repair', 'Pipe fitting'],
            'hourly_rate' => 40.00,
            'city' => 'Ariana',
            'is_available' => true,
        ], $profileAttributes));

        return $user;
    }

    // =========================================================================
    // View & Metrics Tests
    // =========================================================================

    public function test_admin_dashboard_metrics_include_user_verification_and_counts(): void
    {
        $admin = $this->admin();
        $this->client(['is_verified' => true]);
        $this->provider(['is_verified' => false]);
        $this->client(['status' => 'suspended']);

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();

        $res->assertJsonStructure([
            'metrics' => [
                'total_users',
                'clients_count',
                'providers_count',
                'admins_count',
                'verified_users_count',
                'unverified_users_count',
                'suspended_users_count',
            ],
            'users',
            'bookings',
        ]);

        $this->assertEquals(4, $res->json('metrics.total_users'));
        $this->assertEquals(2, $res->json('metrics.verified_users_count')); // Admin + 1 client
        $this->assertEquals(2, $res->json('metrics.unverified_users_count'));
        $this->assertEquals(1, $res->json('metrics.suspended_users_count'));
    }

    public function test_admin_can_view_single_user_details(): void
    {
        $admin = $this->admin();
        $provider = $this->provider([
            'name' => 'Mohamed Ali',
            'email' => 'mohamed.ali@khedma.tn',
            'phone' => '+216 99 123 456',
        ]);

        $client = $this->client();

        Service::factory()->create([
            'provider_id' => $provider->id,
            'title' => 'Plumbing Inspection',
        ]);

        $booking = Booking::create([
            'booking_number' => 'BK-TEST-1',
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_title' => 'Plumbing Inspection',
            'category' => 'Plumbing',
            'status' => 'completed',
            'total_price' => 50,
            'scheduled_at' => now(),
            'address' => 'Tunis',
            'city' => 'Tunis',
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'rating' => 5,
            'comment' => 'Excellent work!',
        ]);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/users/{$provider->id}")
            ->assertOk();

        $res->assertJsonPath('user.name', 'Mohamed Ali');
        $res->assertJsonPath('user.provider_profile.business_name', 'Amrani Services');
        $res->assertJsonPath('stats.services_count', 1);
        $res->assertJsonPath('stats.provider_bookings_count', 1);
        $res->assertJsonPath('stats.provider_reviews_count', 1);
        $res->assertJsonPath('stats.average_rating', 5);
    }

    // =========================================================================
    // Search & Filter Tests
    // =========================================================================

    public function test_admin_can_search_users_by_name(): void
    {
        $admin = $this->admin();
        $this->client(['name' => 'Salma Mansour', 'email' => 'salma@khedma.tn']);
        $this->client(['name' => 'Firas Chahed', 'email' => 'firas@khedma.tn']);

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?search=Salma')
            ->assertOk();

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals('Salma Mansour', $res->json('data.0.name'));
    }

    public function test_admin_can_search_users_by_email_or_phone(): void
    {
        $admin = $this->admin();
        $this->provider([
            'name' => 'Samir Sassi',
            'email' => 'samir.sassi@pro.tn',
            'phone' => '+216 25 999 888',
        ]);

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?search=25 999')
            ->assertOk();

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals('Samir Sassi', $res->json('data.0.name'));
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = $this->admin();
        $this->client(['name' => 'Client One']);
        $this->client(['name' => 'Client Two']);
        $this->provider(['name' => 'Provider One']);

        $resClients = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?role=client')
            ->assertOk();

        $this->assertCount(2, $resClients->json('data'));

        $resProviders = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?role=provider')
            ->assertOk();

        $this->assertCount(1, $resProviders->json('data'));
    }

    public function test_admin_can_filter_users_by_status(): void
    {
        $admin = $this->admin();
        $this->client(['name' => 'Active Client', 'status' => 'active']);
        $this->client(['name' => 'Suspended Client', 'status' => 'suspended']);

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?status=suspended')
            ->assertOk();

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals('Suspended Client', $res->json('data.0.name'));
    }

    public function test_admin_can_filter_users_by_verification_status(): void
    {
        $admin = $this->admin();
        $this->provider(['name' => 'Verified Pro', 'is_verified' => true]);
        $this->provider(['name' => 'Unverified Pro', 'is_verified' => false]);

        $resVerified = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?verification=verified')
            ->assertOk();

        $verifiedNames = collect($resVerified->json('data'))->pluck('name');
        $this->assertTrue($verifiedNames->contains('Verified Pro'));
        $this->assertFalse($verifiedNames->contains('Unverified Pro'));

        $resUnverified = $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?verification=unverified')
            ->assertOk();

        $unverifiedNames = collect($resUnverified->json('data'))->pluck('name');
        $this->assertTrue($unverifiedNames->contains('Unverified Pro'));
        $this->assertFalse($unverifiedNames->contains('Verified Pro'));
    }

    // =========================================================================
    // Verify & Unverify Tests
    // =========================================================================

    public function test_admin_can_verify_and_unverify_user(): void
    {
        $admin = $this->admin();
        $targetUser = $this->provider(['is_verified' => false, 'verified_at' => null]);

        // Verify
        $resVerify = $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/verify", [
                'is_verified' => true,
            ])
            ->assertOk();

        $this->assertTrue($resVerify->json('user.is_verified'));
        $this->assertNotNull($resVerify->json('user.verified_at'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'is_verified' => 1,
        ]);

        // Unverify
        $resUnverify = $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/verify", [
                'is_verified' => false,
            ])
            ->assertOk();

        $this->assertFalse($resUnverify->json('user.is_verified'));
        $this->assertNull($resUnverify->json('user.verified_at'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'is_verified' => 0,
            'verified_at' => null,
        ]);
    }

    // =========================================================================
    // Suspend & Reactivate Tests
    // =========================================================================

    public function test_admin_can_suspend_and_reactivate_user(): void
    {
        $admin = $this->admin();
        $targetUser = $this->client(['status' => 'active']);
        $targetUser->createToken('auth_token');

        // Suspend
        $resSuspend = $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/status", [
                'status' => 'suspended',
            ])
            ->assertOk();

        $this->assertEquals('suspended', $resSuspend->json('user.status'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'status' => 'suspended',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Reactivate
        $resActivate = $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/status", [
                'status' => 'active',
            ])
            ->assertOk();

        $this->assertEquals('active', $resActivate->json('user.status'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$admin->id}/status", [
                'status' => 'suspended',
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Action denied: Administrators cannot suspend their own account.');
    }

    // =========================================================================
    // Delete Tests
    // =========================================================================

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->admin();
        $targetUser = $this->client(['name' => 'To Be Removed']);

        $res = $this->actingAs($admin)
            ->deleteJson("/api/v1/admin/users/{$targetUser->id}")
            ->assertOk();

        $res->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
        $this->assertDatabaseMissing('client_profiles', ['user_id' => $targetUser->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->deleteJson("/api/v1/admin/users/{$admin->id}")
            ->assertStatus(422)
            ->assertJsonPath('message', 'Action denied: Administrators cannot delete their own account.');
    }

    // =========================================================================
    // Authorization Tests
    // =========================================================================

    public function test_non_admin_cannot_manage_users(): void
    {
        $client = $this->client();
        $otherUser = $this->provider();

        $this->actingAs($client)
            ->getJson('/api/v1/admin/users')
            ->assertForbidden();

        $this->actingAs($client)
            ->patchJson("/api/v1/admin/users/{$otherUser->id}/verify", ['is_verified' => true])
            ->assertForbidden();

        $this->actingAs($client)
            ->patchJson("/api/v1/admin/users/{$otherUser->id}/status", ['status' => 'suspended'])
            ->assertForbidden();

        $this->actingAs($client)
            ->deleteJson("/api/v1/admin/users/{$otherUser->id}")
            ->assertForbidden();
    }
}
