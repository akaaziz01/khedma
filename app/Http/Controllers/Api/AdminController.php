<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Get platform overview metrics and management records.
     */
    public function data(Request $request): JsonResponse
    {
        $users = User::with(['clientProfile', 'providerProfile'])
            ->orderBy('created_at', 'desc')
            ->get();

        $bookings = Booking::with(['client:id,name,email,phone', 'provider:id,name,email,phone'])
            ->orderBy('created_at', 'desc')
            ->get();

        $servicesCount = Service::count();

        $completedBookings = $bookings->where('status', 'completed');
        $totalPlatformGMV = $completedBookings->sum('total_price');

        $clientsCount = $users->where('role', UserRole::CLIENT)->count();
        $providersCount = $users->where('role', UserRole::PROVIDER)->count();
        $adminsCount = $users->where('role', UserRole::ADMIN)->count();

        return response()->json([
            'metrics' => [
                'total_users' => $users->count(),
                'clients_count' => $clientsCount,
                'providers_count' => $providersCount,
                'admins_count' => $adminsCount,
                'total_bookings' => $bookings->count(),
                'completed_bookings' => $completedBookings->count(),
                'active_bookings' => $bookings->whereIn('status', ['accepted', 'in_progress'])->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'platform_gmv' => (float) $totalPlatformGMV,
                'services_count' => $servicesCount,
            ],
            'users' => $users,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Update a user's account status (active vs suspended).
     */
    public function updateUserStatus(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();

        if ($admin->id === $id) {
            return response()->json([
                'message' => 'Action denied: Administrators cannot suspend their own account.',
            ], 422);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'pending'])],
        ]);

        $targetUser = User::findOrFail($id);
        $targetUser->status = $validated['status'];
        $targetUser->save();

        // If suspending, revoke all their active tokens immediately for security
        if ($validated['status'] === 'suspended') {
            $targetUser->tokens()->delete();
        }

        return response()->json([
            'message' => "User {$targetUser->name} status has been updated to {$targetUser->status}.",
            'user' => $targetUser->fresh(['clientProfile', 'providerProfile']),
        ]);
    }

    /**
     * Delete user account.
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();

        if ($admin->id === $id) {
            return response()->json([
                'message' => 'Action denied: Administrators cannot delete their own account.',
            ], 422);
        }

        $targetUser = User::findOrFail($id);
        $name = $targetUser->name;
        $targetUser->delete();

        return response()->json([
            'message' => "User {$name} and associated records were successfully removed.",
        ]);
    }
}
