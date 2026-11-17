<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_as_client(): void
    {
        $payload = [
            'name' => 'Sara Client',
            'email' => 'sara@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'phone' => '+21620000001',
            'city' => 'Tunis',
            'address' => 'Avenue Habib Bourguiba, Tunis',
        ];

        $response = $this->postJson('/api/v1/auth/register/client', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'phone',
                    'status',
                    'client_profile' => [
                        'city',
                        'address',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'sara@example.com',
            'role' => UserRole::CLIENT->value,
        ]);

        $this->assertDatabaseHas('client_profiles', [
            'city' => 'Tunis',
            'address' => 'Avenue Habib Bourguiba, Tunis',
        ]);
    }

    public function test_guest_can_register_as_provider(): void
    {
        $payload = [
            'name' => 'Ahmed Electrician',
            'email' => 'ahmed@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'phone' => '+21620000002',
            'category' => 'Electricity',
            'business_name' => 'Ahmed Elec Services',
            'bio' => 'Certified electrician with 7 years of experience.',
            'skills' => ['wiring', 'circuit breakers', 'smart lighting'],
            'hourly_rate' => 35.00,
            'city' => 'Sousse',
        ];

        $response = $this->postJson('/api/v1/auth/register/provider', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'phone',
                    'status',
                    'provider_profile' => [
                        'category',
                        'business_name',
                        'bio',
                        'skills',
                        'hourly_rate',
                        'city',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ahmed@example.com',
            'role' => UserRole::PROVIDER->value,
        ]);

        $this->assertDatabaseHas('provider_profiles', [
            'category' => 'Electricity',
            'business_name' => 'Ahmed Elec Services',
            'city' => 'Sousse',
        ]);
    }

    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $payload = [
            'name' => 'Fail User',
            'email' => 'fail@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPassword!',
        ];

        $response = $this->postJson('/api/v1/auth/register/client', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_email_already_exists(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'role' => UserRole::CLIENT,
        ]);

        $payload = [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register/client', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_provider_registration_fails_without_required_category_and_phone(): void
    {
        $payload = [
            'name' => 'Incomplete Provider',
            'email' => 'incomplete@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->postJson('/api/v1/auth/register/provider', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'category']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'email', 'role'],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_access_profile_and_logout(): void
    {
        $user = User::create([
            'name' => 'Active Client',
            'email' => 'active@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);
        $user->clientProfile()->create([
            'city' => 'Sfax',
            'address' => 'Route de Téniour',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        // Access protected endpoint
        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/user');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'active@example.com')
            ->assertJsonPath('user.client_profile.city', 'Sfax');

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
