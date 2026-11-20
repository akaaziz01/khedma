<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_profile_without_auth(): void
    {
        $response = $this->putJson('/api/v1/profile', [
            'name' => 'Hacker Name',
        ]);

        $response->assertUnauthorized();
    }

    public function test_provider_can_update_their_profile(): void
    {
        $provider = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'name'   => 'Old Name',
            'phone'  => '+216 20 000 000',
            'status' => 'active',
        ]);

        ProviderProfile::create([
            'user_id'       => $provider->id,
            'business_name' => 'Old Business',
            'category'      => 'Plumbing',
            'city'          => 'Tunis',
            'hourly_rate'   => 35.00,
            'is_available'  => true,
            'skills'        => ['Pipes'],
        ]);

        Sanctum::actingAs($provider);

        $response = $this->putJson('/api/v1/profile', [
            'name'          => 'Youssef El Amrani',
            'phone'         => '+216 20 123 456',
            'city'          => 'Ariana',
            'business_name' => 'Amrani Plomberie Express',
            'category'      => 'Plumbing',
            'hourly_rate'   => 45.00,
            'bio'           => 'Licensed plumber with 12 years of experience.',
            'skills'        => ['Emergency Plumbing', 'Leak Detection', 'Boilers'],
            'is_available'  => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Profile updated successfully.')
            ->assertJsonPath('user.name', 'Youssef El Amrani')
            ->assertJsonPath('user.phone', '+216 20 123 456');

        $this->assertDatabaseHas('users', [
            'id'    => $provider->id,
            'name'  => 'Youssef El Amrani',
            'phone' => '+216 20 123 456',
        ]);

        $this->assertDatabaseHas('provider_profiles', [
            'user_id'       => $provider->id,
            'business_name' => 'Amrani Plomberie Express',
            'city'          => 'Ariana',
            'hourly_rate'   => 45.00,
        ]);
    }

    public function test_client_can_update_their_profile(): void
    {
        $client = User::factory()->create([
            'role' => UserRole::CLIENT,
            'name' => 'Initial Client',
        ]);

        Sanctum::actingAs($client);

        $response = $this->putJson('/api/v1/profile', [
            'name'    => 'Ali Mansour',
            'phone'   => '+216 98 765 432',
            'city'    => 'Sousse',
            'address' => 'Route de la Plage, Sousse',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.name', 'Ali Mansour');

        $this->assertDatabaseHas('users', [
            'id'   => $client->id,
            'name' => 'Ali Mansour',
        ]);

        $this->assertDatabaseHas('client_profiles', [
            'user_id' => $client->id,
            'city'    => 'Sousse',
            'address' => 'Route de la Plage, Sousse',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile/password', [
            'current_password'      => 'OldPassword123!',
            'password'              => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Password updated successfully.');

        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile/password', [
            'current_password'      => 'WrongPassword!',
            'password'              => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_profile_web_page_is_accessible(): void
    {
        $response = $this->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Personal Information');
        $response->assertSee('Security & Password', false);
        $response->assertSee('My Profile');
    }

    public function test_provider_can_update_experience_years_on_profile(): void
    {
        $provider = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'status' => 'active',
        ]);

        ProviderProfile::create([
            'user_id'          => $provider->id,
            'business_name'    => 'Khedma Electrical',
            'category'         => 'Electricity',
            'city'             => 'Tunis',
            'hourly_rate'      => 40.00,
            'experience_years' => 3,
            'is_available'     => true,
        ]);

        Sanctum::actingAs($provider);

        $response = $this->putJson('/api/v1/profile', [
            'name'             => $provider->name,
            'experience_years' => 9,
            'hourly_rate'      => 55.00,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('provider_profiles', [
            'user_id'          => $provider->id,
            'experience_years' => 9,
            'hourly_rate'      => 55.00,
        ]);
    }
}
