<?php

namespace Tests\Feature\Client;

use App\Enums\UserRole;
use App\Models\FavoriteProvider;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteProviderTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(): User
    {
        return User::factory()->create([
            'role'   => UserRole::CLIENT,
            'status' => 'active',
        ]);
    }

    private function createProvider(array $userAttributes = [], array $profileAttributes = []): User
    {
        $provider = User::factory()->create(array_merge([
            'role'        => UserRole::PROVIDER,
            'status'      => 'active',
            'is_verified' => true,
        ], $userAttributes));

        ProviderProfile::create(array_merge([
            'user_id'       => $provider->id,
            'business_name' => 'Elite Craft Services',
            'category'      => 'Plumbing',
            'city'          => 'Tunis',
            'hourly_rate'   => 40.00,
            'bio'           => 'Licensed master plumber with 8 years of experience.',
            'skills'        => ['Piping', 'Leak Detection'],
            'is_available'  => true,
        ], $profileAttributes));

        return $provider;
    }

    // =========================================================================
    // Auth & Permission Guards
    // =========================================================================

    public function test_unauthenticated_guest_cannot_access_favorites_api(): void
    {
        $this->getJson('/api/v1/client/favorites')->assertUnauthorized();
        $this->postJson('/api/v1/client/favorites/1')->assertUnauthorized();
        $this->deleteJson('/api/v1/client/favorites/1')->assertUnauthorized();
        $this->getJson('/api/v1/client/favorites/1/status')->assertUnauthorized();
    }

    // =========================================================================
    // List Favorites
    // =========================================================================

    public function test_client_can_fetch_empty_favorites_list(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)
            ->getJson('/api/v1/client/favorites');

        $response->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_client_favorites_list_returns_full_metadata_and_supports_search(): void
    {
        $client = $this->createClient();
        $provider1 = $this->createProvider(['name' => 'Kamel Plumber'], ['category' => 'Plumbing']);
        $provider2 = $this->createProvider(['name' => 'Sami Electrician'], ['category' => 'Electricity']);

        // Add a review to provider1
        Review::create([
            'booking_id'  => null,
            'client_id'   => $client->id,
            'provider_id' => $provider1->id,
            'rating'      => 5,
            'comment'     => 'Excellent plumbing service!',
        ]);

        $client->favorite($provider1, 'My go-to plumber for emergencies');
        $client->favorite($provider2);

        // Fetch all favorites
        $response = $this->actingAs($client)
            ->getJson('/api/v1/client/favorites');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2);

        $firstItem = $response->json('data.0');
        $this->assertArrayHasKey('provider_id', $firstItem);
        $this->assertArrayHasKey('trade', $firstItem);
        $this->assertArrayHasKey('rating', $firstItem);
        $this->assertArrayHasKey('hourly_rate', $firstItem);
        $this->assertArrayHasKey('city', $firstItem);

        // Test search filter by name
        $searchRes = $this->actingAs($client)
            ->getJson('/api/v1/client/favorites?q=Kamel');

        $searchRes->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Kamel Plumber');

        // Test trade filter
        $tradeRes = $this->actingAs($client)
            ->getJson('/api/v1/client/favorites?trade=Electricity');

        $tradeRes->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Sami Electrician');
    }

    // =========================================================================
    // Add / Store Favorites
    // =========================================================================

    public function test_client_can_save_provider_to_favorites(): void
    {
        $client = $this->createClient();
        $provider = $this->createProvider();

        $response = $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$provider->id}", [
                'notes' => 'Recommended by neighborhood group',
            ]);

        $response->assertCreated()
            ->assertJsonPath('is_favorited', true)
            ->assertJsonPath('data.provider_id', $provider->id)
            ->assertJsonPath('data.notes', 'Recommended by neighborhood group');

        $this->assertDatabaseHas('favorite_providers', [
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
            'notes'       => 'Recommended by neighborhood group',
        ]);
    }

    public function test_saving_same_provider_again_updates_notes_and_is_idempotent(): void
    {
        $client = $this->createClient();
        $provider = $this->createProvider();

        // First save
        $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$provider->id}", ['notes' => 'First note'])
            ->assertCreated();

        // Second save updates note without duplicate error
        $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$provider->id}", ['notes' => 'Updated note'])
            ->assertCreated();

        $this->assertDatabaseCount('favorite_providers', 1);
        $this->assertDatabaseHas('favorite_providers', [
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
            'notes'       => 'Updated note',
        ]);
    }

    // =========================================================================
    // Validation & Error Handling
    // =========================================================================

    public function test_client_cannot_favorite_themselves(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$client->id}");

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'You cannot add your own account to favorites.']);
    }

    public function test_client_cannot_favorite_non_existent_provider(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)
            ->postJson('/api/v1/client/favorites/99999');

        $response->assertNotFound();
    }

    public function test_client_cannot_favorite_another_client_or_admin(): void
    {
        $client = $this->createClient();
        $otherClient = $this->createClient();
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$otherClient->id}")
            ->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Only active service providers can be added to favorites.']);

        $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$admin->id}")
            ->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Only active service providers can be added to favorites.']);
    }

    public function test_client_cannot_favorite_inactive_provider(): void
    {
        $client = $this->createClient();
        $inactiveProvider = $this->createProvider(['status' => 'suspended']);

        $response = $this->actingAs($client)
            ->postJson("/api/v1/client/favorites/{$inactiveProvider->id}");

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'This provider is currently inactive or suspended.']);
    }

    // =========================================================================
    // Status Check & Removal
    // =========================================================================

    public function test_client_can_check_favorite_status_via_api(): void
    {
        $client = $this->createClient();
        $provider = $this->createProvider();

        // Not favorited yet
        $res1 = $this->actingAs($client)
            ->getJson("/api/v1/client/favorites/{$provider->id}/status");

        $res1->assertOk()
            ->assertJsonPath('is_favorited', false);

        // Favorite provider
        $client->favorite($provider);

        // Now favorited
        $res2 = $this->actingAs($client)
            ->getJson("/api/v1/client/favorites/{$provider->id}/status");

        $res2->assertOk()
            ->assertJsonPath('is_favorited', true);
    }

    public function test_client_can_remove_provider_from_favorites(): void
    {
        $client = $this->createClient();
        $provider = $this->createProvider();
        $client->favorite($provider);

        $this->assertDatabaseHas('favorite_providers', [
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
        ]);

        $response = $this->actingAs($client)
            ->deleteJson("/api/v1/client/favorites/{$provider->id}");

        $response->assertOk()
            ->assertJsonPath('is_favorited', false);

        $this->assertDatabaseMissing('favorite_providers', [
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
        ]);
    }
}
