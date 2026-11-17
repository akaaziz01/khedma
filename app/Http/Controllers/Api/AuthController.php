<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterProviderRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new client.
     */
    public function registerClient(RegisterClientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => UserRole::CLIENT,
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);

            $user->clientProfile()->create([
                'city' => $validated['city'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            return $user;
        });

        $user->load('clientProfile');
        $token = $user->createToken('auth_token', ['role:client'])->plainTextToken;

        return response()->json([
            'message' => 'Client account created successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Register a new service provider.
     */
    public function registerProvider(RegisterProviderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => UserRole::PROVIDER,
                'phone' => $validated['phone'],
                'status' => 'active',
            ]);

            $user->providerProfile()->create([
                'business_name' => $validated['business_name'] ?? null,
                'category' => $validated['category'],
                'bio' => $validated['bio'] ?? null,
                'skills' => $validated['skills'] ?? [],
                'hourly_rate' => $validated['hourly_rate'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);

            return $user;
        });

        $user->load('providerProfile');
        $token = $user->createToken('auth_token', ['role:provider'])->plainTextToken;

        return response()->json([
            'message' => 'Provider account created successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Authenticate user & return token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is suspended or pending approval.',
            ], 403);
        }

        $relation = $user->isClient() ? 'clientProfile' : ($user->isProvider() ? 'providerProfile' : null);
        if ($relation) {
            $user->load($relation);
        }

        $roleSlug = $user->role instanceof UserRole ? $user->role->value : $user->role;
        $token = $user->createToken('auth_token', ["role:{$roleSlug}"])->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isClient()) {
            $user->load('clientProfile');
        } elseif ($user->isProvider()) {
            $user->load('providerProfile');
        }

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Logout and revoke all tokens across all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully.',
        ]);
    }

    /**
     * Send password reset token / link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        // Generate password reset token via Laravel broker
        $token = Password::broker()->createToken($user);

        // Build reset link
        $resetUrl = url("/reset-password?token={$token}&email=" . urlencode($user->email));

        return response()->json([
            'message' => 'Password reset instructions have been generated.',
            'reset_token' => $token,
            'reset_url' => $resetUrl,
        ]);
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Revoke all prior active tokens for security
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $request->email)->first();
            $roleSlug = $user->role instanceof UserRole ? $user->role->value : $user->role;
            $newToken = $user->createToken('auth_token', ["role:{$roleSlug}"])->plainTextToken;

            return response()->json([
                'message' => 'Your password has been successfully reset! You can now log in with your new password.',
                'access_token' => $newToken,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
