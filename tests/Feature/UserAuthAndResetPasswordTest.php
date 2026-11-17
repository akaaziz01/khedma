<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserAuthAndResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_sanctum_token(): void
    {
        $user = User::create([
            'name' => 'Sara Client',
            'email' => 'sara@khedma.ma',
            'password' => 'OriginalPass123!',
            'role' => UserRole::CLIENT,
            'status' => 'active',
        ]);
        $user->clientProfile()->create(['city' => 'Tunis']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'sara@khedma.ma',
            'password' => 'OriginalPass123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::create([
            'name' => 'Sara Client',
            'email' => 'sara@khedma.ma',
            'password' => 'OriginalPass123!',
            'role' => UserRole::CLIENT,
        ]);

        $token = $user->createToken('device_1')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::create([
            'name' => 'Sara Client',
            'email' => 'sara@khedma.ma',
            'password' => 'OriginalPass123!',
            'role' => UserRole::CLIENT,
        ]);

        $token1 = $user->createToken('phone')->plainTextToken;
        $user->createToken('laptop');
        $user->createToken('tablet');

        $this->assertDatabaseCount('personal_access_tokens', 3);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out from all devices successfully.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_request_forgot_password_token(): void
    {
        $user = User::create([
            'name' => 'Youssef Pro',
            'email' => 'youssef@khedma.ma',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
        ]);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'youssef@khedma.ma',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'reset_token', 'reset_url']);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'youssef@khedma.ma',
        ]);
    }

    public function test_forgot_password_fails_for_unknown_email(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'unknown@khedma.ma',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Karim Client',
            'email' => 'karim@khedma.ma',
            'password' => 'OldPassword123!',
            'role' => UserRole::CLIENT,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'karim@khedma.ma',
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'access_token', 'user']);

        // Verify password changed
        $user->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword123!', $user->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));

        // Verify user can now log in with the new password
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'karim@khedma.ma',
            'password' => 'BrandNewPassword123!',
        ]);
        $loginRes->assertStatus(200);

        // Verify old password fails
        $failedLogin = $this->postJson('/api/v1/auth/login', [
            'email' => 'karim@khedma.ma',
            'password' => 'OldPassword123!',
        ]);
        $failedLogin->assertStatus(422);
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::create([
            'name' => 'Karim Client',
            'email' => 'karim@khedma.ma',
            'password' => 'OldPassword123!',
            'role' => UserRole::CLIENT,
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'invalid-token-string',
            'email' => 'karim@khedma.ma',
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_auth_and_password_views_are_accessible(): void
    {
        $this->get('/forgot-password')->assertStatus(200)->assertSee('Reset Your Password');
        $this->get('/reset-password')->assertStatus(200)->assertSee('Set New Password');
        $this->get('/login')->assertStatus(200)->assertSee('Forgot password?');
    }
}
