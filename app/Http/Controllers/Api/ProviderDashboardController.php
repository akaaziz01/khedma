<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderDashboardController extends Controller
{
    /**
     * Get real provider dashboard metrics and data.
     */
    public function data(Request $request): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json([
                'message' => 'Unauthorized: User is not a provider.',
            ], 403);
        }

        $provider->load(['providerProfile', 'services']);

        $bookings = Booking::where('provider_id', $provider->id)
            ->with(['client:id,name,email,phone', 'review'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $reviews = $provider->providerReviews()
            ->with('client:id,name')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $completedBookings = $bookings->where('status', 'completed');
        $totalRevenue = $completedBookings->sum('total_price');
        $completedJobsCount = $completedBookings->count();
        $activeJobsCount = $bookings->whereIn('status', ['accepted', 'in_progress'])->count();
        $pendingLeadsCount = $bookings->where('status', 'pending')->count();
        $averageRating = round($reviews->avg('rating') ?: 5.0, 1);
        $totalReviewsCount = $reviews->count();

        // Spotlight-inspired category breakdown
        $categoryCounts = $bookings->groupBy('category')->map(function ($group) use ($bookings) {
            $count = $group->count();
            return [
                'count' => $count,
                'percent' => $bookings->count() > 0 ? round(($count / $bookings->count()) * 100) : 0,
            ];
        });

        return response()->json([
            'provider' => [
                'id' => $provider->id,
                'name' => $provider->name,
                'email' => $provider->email,
                'phone' => $provider->phone,
                'profile' => $provider->providerProfile,
            ],
            'metrics' => [
                'total_revenue' => (float) $totalRevenue,
                'completed_jobs' => $completedJobsCount,
                'active_jobs' => $activeJobsCount,
                'pending_leads' => $pendingLeadsCount,
                'average_rating' => (float) $averageRating,
                'total_reviews' => $totalReviewsCount,
                'is_available' => (bool) ($provider->providerProfile?->is_available ?? true),
                'hourly_rate' => (float) ($provider->providerProfile?->hourly_rate ?? 0),
            ],
            'bookings' => $bookings,
            'services' => $provider->services()->orderBy('created_at', 'desc')->get(),
            'reviews' => $reviews,
            'category_breakdown' => $categoryCounts,
        ]);
    }

    /**
     * Update booking status (e.g. accepted, in_progress, completed, declined).
     */
    public function updateBookingStatus(Request $request, int $id): JsonResponse
    {
        $provider = $request->user();

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'accepted', 'in_progress', 'completed', 'declined', 'cancelled'])],
            'provider_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $booking = Booking::where('id', $id)
            ->where('provider_id', $provider->id)
            ->firstOrFail();

        $booking->status = $validated['status'];
        if (isset($validated['provider_notes'])) {
            $booking->provider_notes = $validated['provider_notes'];
        }

        if ($validated['status'] === 'completed' && ! $booking->completed_at) {
            $booking->completed_at = now();
        }

        $booking->save();

        return response()->json([
            'message' => "Booking {$booking->booking_number} marked as {$booking->status}.",
            'booking' => $booking->fresh(['client', 'review']),
        ]);
    }

    /**
     * Toggle availability status.
     */
    public function toggleAvailability(Request $request): JsonResponse
    {
        $provider = $request->user();
        $profile = $provider->providerProfile;

        if (! $profile) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        $profile->is_available = ! $profile->is_available;
        $profile->save();

        return response()->json([
            'message' => $profile->is_available ? 'You are now marked as Available for jobs.' : 'You are now marked as Offline.',
            'is_available' => (bool) $profile->is_available,
        ]);
    }

    /**
     * Add a new service offering.
     */
    public function createService(Request $request): JsonResponse
    {
        $provider = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['nullable', 'numeric', 'min:0.5', 'max:24'],
        ]);

        $service = $provider->services()->create([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'description'    => $validated['description'] ?? null,
            'price'          => $validated['price'],
            'duration_hours' => $validated['duration_hours'] ?? 1.0,
            'is_active'      => false,      // inactive until admin approves
            'status'         => 'pending',  // awaits moderation
        ]);

        return response()->json([
            'message' => 'Service added successfully. It will be visible after admin approval.',
            'service' => $service,
        ], 201);
    }

    /**
     * Update provider profile details (rate, bio, city, skills).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $provider = $request->user();
        $profile = $provider->providerProfile;

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'city' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array'],
            'portfolio_photos' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if (isset($validated['phone'])) {
            $provider->phone = $validated['phone'];
            $provider->save();
        }

        if ($profile) {
            $profile->update($validated);
        }

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => $profile->fresh(),
            'user' => $provider->fresh(['providerProfile']),
        ]);
    }
}
