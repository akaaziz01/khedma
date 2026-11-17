<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user and their active role profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isClient()) {
            $user->load('clientProfile');
        } elseif ($user->isProvider()) {
            $user->load('providerProfile');
        }

        return response()->json([
            'user' => new UserResource($user),
            'profile' => $user->active_profile,
        ]);
    }

    /**
     * Update the authenticated user's profile and avatar.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $request, $validated) {
            // 1. Update common user details (ignore role, status, user_id)
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (array_key_exists('phone', $validated)) {
                $user->phone = $validated['phone'];
            }

            // 2. Process avatar upload if present
            if ($request->hasFile('avatar')) {
                // Delete previous avatar file if exists
                if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }

                // Store with auto-generated unique filename on public disk
                $storedPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar_path = $storedPath;
            }

            $user->save();

            // 3. Update or create role-specific profile
            if ($user->isClient()) {
                $clientData = [];
                if (array_key_exists('city', $validated)) {
                    $clientData['city'] = $validated['city'];
                }
                if (array_key_exists('address', $validated)) {
                    $clientData['address'] = $validated['address'];
                }

                if (! empty($clientData) || $user->clientProfile()->exists()) {
                    $user->clientProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        $clientData
                    );
                }
            } elseif ($user->isProvider()) {
                $providerData = [];
                $fields = ['business_name', 'category', 'bio', 'skills', 'hourly_rate', 'city'];
                foreach ($fields as $field) {
                    if (array_key_exists($field, $validated)) {
                        $providerData[$field] = $validated[$field];
                    }
                }

                if (! empty($providerData) || $user->providerProfile()->exists()) {
                    $user->providerProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        $providerData
                    );
                }
            }
            // Admin users do not have a separate profile row
        });

        $user->refresh();
        if ($user->isClient()) {
            $user->load('clientProfile');
        } elseif ($user->isProvider()) {
            $user->load('providerProfile');
        }

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user),
            'profile' => $user->active_profile,
        ]);
    }

    /**
     * Remove the authenticated user's avatar.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        return response()->json([
            'message' => 'Avatar removed successfully.',
            'user' => new UserResource($user->fresh()),
        ]);
    }
}
