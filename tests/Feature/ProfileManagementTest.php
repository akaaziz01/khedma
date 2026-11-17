<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClientProfile;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_gets_401_on_get_and_post_profile(): void
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
        $this->postJson('/api/v1/profile', ['name' => 'Guest'])->assertStatus(401);
        $this->deleteJson('/api/v1/profile/avatar')->assertStatus(401);
    }

    public function test_client_updates_name_phone_city_address(): void
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'client@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
            'phone' => '+21620000000',
        ]);
        $user->clientProfile()->create([
            'city' => 'Tunis',
            'address' => 'Old Address',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/profile', [
            'name' => 'Updated Client Name',
            'email' => 'client@example.com',
            'phone' => '+216 98 123 456',
            'city' => 'Sousse',
            'address' => 'Avenue Habib Bourguiba, Sousse',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Profile updated successfully.')
            ->assertJsonPath('user.name', 'Updated Client Name')
            ->assertJsonPath('user.phone', '+216 98 123 456')
            ->assertJsonPath('profile.city', 'Sousse')
            ->assertJsonPath('profile.address', 'Avenue Habib Bourguiba, Sousse');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Client Name',
            'phone' => '+216 98 123 456',
        ]);

        $this->assertDatabaseHas('client_profiles', [
            'user_id' => $user->id,
            'city' => 'Sousse',
            'address' => 'Avenue Habib Bourguiba, Sousse',
        ]);
    }

    public function test_provider_updates_profile_fields(): void
    {
        $provider = User::create([
            'name' => 'Karim Pro',
            'email' => 'karim@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
            'phone' => '+21620112233',
        ]);
        $provider->providerProfile()->create([
            'category' => 'Plumbing',
            'business_name' => 'Karim Plombier',
            'city' => 'Tunis',
            'hourly_rate' => 30.00,
        ]);

        Sanctum::actingAs($provider);

        $response = $this->postJson('/api/v1/profile', [
            'name' => 'Karim El Plombier',
            'email' => 'karim@example.com',
            'phone' => '+216 55 998 877',
            'business_name' => 'Karim Plomberie Express',
            'category' => 'HVAC & AC',
            'city' => 'Ariana',
            'bio' => '15 years installing heating and AC systems across Ariana.',
            'skills' => ['copper welding', 'gas pipes', 'ac recharge'],
            'hourly_rate' => 55.00,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'Karim El Plombier')
            ->assertJsonPath('profile.business_name', 'Karim Plomberie Express')
            ->assertJsonPath('profile.category', 'HVAC & AC')
            ->assertJsonPath('profile.city', 'Ariana')
            ->assertJsonPath('profile.hourly_rate', '55.00')
            ->assertJsonPath('profile.skills', ['copper welding', 'gas pipes', 'ac recharge']);

        $this->assertDatabaseHas('provider_profiles', [
            'user_id' => $provider->id,
            'business_name' => 'Karim Plomberie Express',
            'category' => 'HVAC & AC',
            'bio' => '15 years installing heating and AC systems across Ariana.',
            'city' => 'Ariana',
        ]);
    }

    public function test_email_validation_rules(): void
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);
        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        Sanctum::actingAs($userA);

        // Invalid format rejected
        $this->postJson('/api/v1/profile', [
            'name' => 'User A',
            'email' => 'not-an-email',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);

        // Another user's email rejected
        $this->postJson('/api/v1/profile', [
            'name' => 'User A',
            'email' => 'userb@example.com',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);

        // Own email accepted
        $this->postJson('/api/v1/profile', [
            'name' => 'User A Renamed',
            'email' => 'usera@example.com',
        ])->assertStatus(200);
    }

    public function test_provider_without_phone_or_category_rejected(): void
    {
        $provider = User::create([
            'name' => 'Provider Incomplete',
            'email' => 'proincomp@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
            'phone' => '+21620112233',
        ]);
        $provider->providerProfile()->create([
            'category' => 'Plumbing',
        ]);

        Sanctum::actingAs($provider);

        // Missing phone rejected
        $this->postJson('/api/v1/profile', [
            'name' => 'Provider Incomplete',
            'email' => 'proincomp@example.com',
            'category' => 'Plumbing',
            'phone' => '',
        ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

        // Missing category rejected
        $this->postJson('/api/v1/profile', [
            'name' => 'Provider Incomplete',
            'email' => 'proincomp@example.com',
            'phone' => '+21620112233',
            'category' => '',
        ])->assertStatus(422)->assertJsonValidationErrors(['category']);
    }

    public function test_invalid_or_non_governorate_city_rejected(): void
    {
        $client = User::create([
            'name' => 'Client Test',
            'email' => 'clientgov@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);
        $client->clientProfile()->create([
            'city' => 'Tunis',
        ]);

        Sanctum::actingAs($client);

        // Invalid/non-governorate rejected when changing city
        $this->postJson('/api/v1/profile', [
            'name' => 'Client Test',
            'email' => 'clientgov@example.com',
            'city' => 'Paris',
        ])->assertStatus(422)->assertJsonValidationErrors(['city']);

        // Valid Tunisian governorate accepted
        $this->postJson('/api/v1/profile', [
            'name' => 'Client Test',
            'email' => 'clientgov@example.com',
            'city' => 'Nabeul',
        ])->assertStatus(200);

        // User Requirement 2: existing city not in governorate list is not blocked if unchanged
        $client->clientProfile()->update(['city' => 'Marsa Old Custom City']);
        $this->postJson('/api/v1/profile', [
            'name' => 'Client Test Renamed',
            'email' => 'clientgov@example.com',
            'city' => 'Marsa Old Custom City',
        ])->assertStatus(200);
    }

    public function test_valid_avatar_stored_replaced_and_deleted(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Photo User',
            'email' => 'photo@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        Sanctum::actingAs($user);

        // 1. Upload initial avatar
        $file1 = UploadedFile::fake()->image('avatar1.jpg', 200, 200);
        $response1 = $this->post('/api/v1/profile', [
            'name' => 'Photo User',
            'email' => 'photo@example.com',
            'avatar' => $file1,
        ]);

        $response1->assertStatus(200);
        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
        $oldPath = $user->avatar_path;

        // 2. Upload replacement avatar -> old file deleted
        $file2 = UploadedFile::fake()->image('avatar2.png', 300, 300);
        $response2 = $this->post('/api/v1/profile', [
            'name' => 'Photo User',
            'email' => 'photo@example.com',
            'avatar' => $file2,
        ]);

        $response2->assertStatus(200);
        $user->refresh();

        $this->assertNotEquals($oldPath, $user->avatar_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar_path);

        // 3. DELETE /api/v1/profile/avatar removes it
        $deleteResponse = $this->deleteJson('/api/v1/profile/avatar');
        $deleteResponse->assertStatus(200)
            ->assertJsonPath('message', 'Avatar removed successfully.');

        Storage::disk('public')->assertMissing($user->avatar_path);
        $user->refresh();
        $this->assertNull($user->avatar_path);
        $this->assertNull($user->avatar_url);
    }

    public function test_non_image_wrong_mime_and_too_large_files_rejected(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Validation User',
            'email' => 'valuser@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        Sanctum::actingAs($user);

        // Non-image / PDF rejected
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $this->post('/api/v1/profile', [
            'name' => 'Validation User',
            'email' => 'valuser@example.com',
            'avatar' => $pdfFile,
        ])->assertStatus(422)->assertJsonValidationErrors(['avatar']);

        // Oversized file (>2 MB) rejected
        $largeImage = UploadedFile::fake()->image('huge.jpg')->size(2500);
        $this->post('/api/v1/profile', [
            'name' => 'Validation User',
            'email' => 'valuser@example.com',
            'avatar' => $largeImage,
        ])->assertStatus(422)->assertJsonValidationErrors(['avatar']);
    }

    public function test_sending_role_or_status_is_ignored(): void
    {
        $user = User::create([
            'name' => 'Regular Client',
            'email' => 'regular@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/profile', [
            'name' => 'Regular Client Updated',
            'email' => 'regular@example.com',
            'role' => 'admin',
            'status' => 'suspended',
            'user_id' => 9999,
        ]);

        $response->assertStatus(200);
        $user->refresh();

        $this->assertEquals(UserRole::CLIENT, $user->role);
        $this->assertEquals('active', $user->status);
    }

    public function test_client_sending_provider_only_fields_does_not_create_provider_profile(): void
    {
        $client = User::create([
            'name' => 'Sneaky Client',
            'email' => 'sneaky@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        Sanctum::actingAs($client);

        $response = $this->postJson('/api/v1/profile', [
            'name' => 'Sneaky Client',
            'email' => 'sneaky@example.com',
            'business_name' => 'Sneaky Electric Co',
            'category' => 'Electricity',
            'bio' => 'Should not be saved',
            'hourly_rate' => 100,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('provider_profiles', [
            'user_id' => $client->id,
        ]);
    }
}
