<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Booking;
use App\Models\Review;
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
            ->withCount(['services', 'clientBookings', 'providerBookings'])
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
        $verifiedCount = $users->where('is_verified', true)->count();
        $unverifiedCount = $users->where('is_verified', false)->count();
        $suspendedCount = $users->where('status', 'suspended')->count();

        return response()->json([
            'metrics' => [
                'total_users' => $users->count(),
                'clients_count' => $clientsCount,
                'providers_count' => $providersCount,
                'admins_count' => $adminsCount,
                'verified_users_count' => $verifiedCount,
                'unverified_users_count' => $unverifiedCount,
                'suspended_users_count' => $suspendedCount,
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
     * Filter and search users.
     */
    public function listUsers(Request $request): JsonResponse
    {
        $query = User::with(['clientProfile', 'providerProfile'])
            ->withCount(['services', 'clientBookings', 'providerBookings'])
            ->orderBy('created_at', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('clientProfile', fn ($cp) => $cp->where('city', 'like', "%{$search}%"))
                  ->orWhereHas('providerProfile', fn ($pp) => $pp->where('city', 'like', "%{$search}%")->orWhere('business_name', 'like', "%{$search}%"));
            });
        }

        if ($role = $request->query('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        if ($status = $request->query('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($verification = $request->query('verification')) {
            if ($verification === 'verified') {
                $query->where('is_verified', true);
            } elseif ($verification === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        $perPage = min((int) ($request->query('per_page', 50)), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    /**
     * Get detailed information for a specific user.
     */
    public function showUser(Request $request, int $id): JsonResponse
    {
        $user = User::with([
            'clientProfile',
            'providerProfile',
            'services.serviceCategory',
            'clientBookings' => fn ($q) => $q->latest()->limit(5),
            'providerBookings' => fn ($q) => $q->latest()->limit(5),
            'providerReviews' => fn ($q) => $q->latest()->limit(5),
        ])
        ->withCount(['services', 'clientBookings', 'providerBookings', 'providerReviews'])
        ->findOrFail($id);

        $avgRating = $user->isProvider() 
            ? Review::where('provider_id', $user->id)->avg('rating') 
            : null;

        return response()->json([
            'user' => $user,
            'stats' => [
                'services_count' => $user->services_count,
                'client_bookings_count' => $user->client_bookings_count,
                'provider_bookings_count' => $user->provider_bookings_count,
                'provider_reviews_count' => $user->provider_reviews_count,
                'average_rating' => $avgRating ? round((float) $avgRating, 1) : null,
            ],
        ]);
    }

    /**
     * Verify or unverify a user account.
     */
    public function verifyUser(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_verified' => ['required', 'boolean'],
        ]);

        $targetUser = User::findOrFail($id);
        $targetUser->is_verified = $validated['is_verified'];
        $targetUser->verified_at = $validated['is_verified'] ? now() : null;
        $targetUser->save();

        $actionWord = $targetUser->is_verified ? 'verified' : 'unverified';

        return response()->json([
            'message' => "User {$targetUser->name} has been marked as {$actionWord}.",
            'user' => $targetUser->fresh(['clientProfile', 'providerProfile']),
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

    // =========================================================================
    // Service Moderation
    // =========================================================================

    /**
     * Paginated list of services — filterable by status, category, and free-text search.
     *
     * GET /admin/services
     * Query params:
     *   ?status=pending|approved|rejected
     *   ?category_id=<int>
     *   ?search=<string>   (matches title or provider name)
     *   ?per_page=<int>    (default 20)
     */
    public function listServices(Request $request): JsonResponse
    {
        $query = Service::with(['provider:id,name,email,phone', 'serviceCategory'])
            ->orderBy('created_at', 'desc');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('provider', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage  = min((int) ($request->query('per_page', 20)), 100);
        $services = $query->paginate($perPage);

        return response()->json([
            'data'       => ServiceResource::collection($services->items()),
            'pagination' => [
                'total'        => $services->total(),
                'per_page'     => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page'    => $services->lastPage(),
            ],
        ]);
    }

    /**
     * Approve or reject a service listing.
     *
     * POST /admin/services/{id}/moderate
     * Body: { "action": "approve"|"reject", "rejection_reason": "..." }
     */
    public function moderateService(ModerateServiceRequest $request, int $id): JsonResponse
    {
        $admin    = $request->user();
        $service  = Service::with(['provider:id,name,email', 'serviceCategory'])->findOrFail($id);
        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $service->status           = 'approved';
            $service->rejection_reason = null;
            $service->is_active        = true;
        } else {
            $service->status           = 'rejected';
            $service->rejection_reason = $validated['rejection_reason'];
            $service->is_active        = false;
        }

        $service->moderated_at = now();
        $service->moderated_by = $admin->id;
        $service->save();

        $verb = $validated['action'] === 'approve' ? 'approved' : 'rejected';

        return response()->json([
            'message' => "Service \"{$service->title}\" has been {$verb}.",
            'data'    => new ServiceResource($service),
        ]);
    }

    /**
     * Force-toggle the is_active flag on any service (admin override).
     *
     * PATCH /admin/services/{id}/active
     * Body: { "is_active": true|false }
     */
    public function toggleServiceActive(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $service = Service::findOrFail($id);

        // Can only activate an approved service
        if ($validated['is_active'] && $service->status !== 'approved') {
            return response()->json([
                'message' => 'Cannot activate a service that has not been approved. Moderate it first.',
            ], 422);
        }

        $service->is_active = $validated['is_active'];
        $service->save();

        $state = $service->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'message' => "Service \"{$service->title}\" has been {$state}.",
            'data'    => new ServiceResource($service),
        ]);
    }

    /**
     * Hard-delete a service listing (admin).
     * Blocked if the service has non-cancelled bookings.
     *
     * DELETE /admin/services/{id}
     */
    public function deleteService(Request $request, int $id): JsonResponse
    {
        $service = Service::withCount([
            'bookings as active_bookings_count' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'declined']),
        ])->findOrFail($id);

        if ($service->active_bookings_count > 0) {
            return response()->json([
                'message' => "Cannot delete \"{$service->title}\": it has {$service->active_bookings_count} active booking(s). "
                    . 'Cancel or complete those bookings first.',
            ], 422);
        }

        $title = $service->title;
        $service->delete();

        return response()->json([
            'message' => "Service \"{$title}\" and its records have been permanently removed.",
        ]);
    }
}
