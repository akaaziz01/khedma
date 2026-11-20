<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['nullable', 'numeric', 'min:0.5', 'max:24'],
        ]);

        $category = \App\Models\ServiceCategory::where('name', $validated['category'])->first();

        $service = $provider->services()->create([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'category_id'    => $category?->id,
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
     * Get a single service owned by the authenticated provider.
     */
    public function getService(Request $request, int $id): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $service = $provider->services()->where('id', $id)->firstOrFail();

        return response()->json([
            'service' => $service,
        ]);
    }

    /**
     * Update an existing service offering and its rate.
     */
    public function updateService(Request $request, int $id): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $service = $provider->services()->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'price'          => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['nullable', 'numeric', 'min:0.5', 'max:24'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'description'    => $validated['description'] ?? null,
            'price'          => $validated['price'],
            'duration_hours' => $validated['duration_hours'] ?? $service->duration_hours ?? 1.0,
        ];

        if (array_key_exists('is_active', $validated)) {
            $updateData['is_active'] = (bool) $validated['is_active'];
        }

        $category = \App\Models\ServiceCategory::where('name', $validated['category'])->first();
        if ($category) {
            $updateData['category_id'] = $category->id;
        }

        $service->update($updateData);

        return response()->json([
            'message' => 'Service updated successfully with new rates and details.',
            'service' => $service->fresh(),
        ]);
    }

    /**
     * Delete a service offering owned by the authenticated provider.
     */
    public function deleteService(Request $request, int $id): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $service = $provider->services()->where('id', $id)->firstOrFail();

        $hasActiveBookings = $service->bookings()
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->exists();

        if ($hasActiveBookings) {
            return response()->json([
                'message' => 'Cannot delete service with active bookings. Please complete or cancel those bookings first.',
            ], 422);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully from your catalog.',
        ]);
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
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
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

    /**
     * Upload a portfolio / past work photo to prove quality of work.
     */
    public function uploadPortfolioPhoto(Request $request): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $validated = $request->validate([
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'photo_url' => ['nullable', 'url', 'max:500'],
            'title'     => ['required', 'string', 'max:150'],
            'caption'   => ['nullable', 'string', 'max:500'],
        ]);

        if (! $request->hasFile('photo') && empty($validated['photo_url'])) {
            return response()->json([
                'message' => 'Please provide an image file or a valid photo URL.',
                'errors' => ['photo' => ['An image file or photo URL is required.']],
            ], 422);
        }

        $photoUrl = '';
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('portfolio', 'public');
            $photoUrl = Storage::url($path);
        } else {
            $photoUrl = $validated['photo_url'];
        }

        $profile = $provider->providerProfile ?: $provider->providerProfile()->create([
            'business_name'    => $provider->name,
            'category'         => 'Plumbing',
            'hourly_rate'      => 40.00,
            'experience_years' => 1,
            'city'             => 'Tunis',
            'is_available'     => true,
        ]);

        $photos = $profile->portfolio_photos ?? [];
        if (! is_array($photos)) {
            $photos = [];
        }

        $newPhoto = [
            'id'         => (string) Str::uuid(),
            'url'        => $photoUrl,
            'title'      => $validated['title'],
            'caption'    => $validated['caption'] ?? '',
            'created_at' => now()->toIso8601String(),
        ];

        $photos[] = $newPhoto;
        $profile->portfolio_photos = $photos;
        $profile->save();

        return response()->json([
            'message'          => 'Work photo uploaded successfully.',
            'photo'            => $newPhoto,
            'portfolio_photos' => $photos,
        ], 201);
    }

    /**
     * Delete a portfolio photo by id or index.
     */
    public function deletePortfolioPhoto(Request $request, string|int $id): JsonResponse
    {
        $provider = $request->user();

        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Unauthorized: User is not a provider.'], 403);
        }

        $profile = $provider->providerProfile;
        if (! $profile) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        $photos = $profile->portfolio_photos ?? [];
        if (! is_array($photos)) {
            $photos = [];
        }

        $found = false;
        $updatedPhotos = [];

        foreach ($photos as $idx => $photo) {
            $photoId = $photo['id'] ?? (string) $idx;
            if ($photoId === (string) $id || (is_numeric($id) && (int) $id === $idx)) {
                $found = true;
                // Optional: remove file from public storage if it was uploaded locally
                if (isset($photo['url']) && str_starts_with($photo['url'], '/storage/portfolio/')) {
                    $relativeFilePath = str_replace('/storage/', '', $photo['url']);
                    Storage::disk('public')->delete($relativeFilePath);
                }
                continue;
            }
            $updatedPhotos[] = $photo;
        }

        if (! $found) {
            return response()->json(['message' => 'Photo not found in your portfolio.'], 404);
        }

        $profile->portfolio_photos = $updatedPhotos;
        $profile->save();

        return response()->json([
            'message'          => 'Portfolio photo removed successfully.',
            'portfolio_photos' => $updatedPhotos,
        ]);
    }
}
