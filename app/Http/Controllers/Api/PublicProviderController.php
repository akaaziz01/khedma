<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PublicProviderController extends Controller
{
    /**
     * Get public provider profile with trade, skills, services, rates, portfolio, reviews.
     * Publicly accessible for guests to judge quality before signing up.
     */
    public function show(int $id): JsonResponse
    {
        $provider = User::query()
            ->where('id', $id)
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with([
                'providerProfile',
                'services' => function ($q) {
                    $q->where('status', 'approved')
                        ->where('is_active', true)
                        ->with('serviceCategory:id,name,slug,icon')
                        ->orderBy('price', 'asc');
                },
                'providerReviews' => function ($q) {
                    $q->with('client:id,name')
                        ->orderBy('created_at', 'desc');
                },
            ])
            ->firstOrFail();

        $reviews = $provider->providerReviews;
        $reviewsCount = $reviews->count();
        $averageRating = $reviewsCount > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        // Rating breakdown (1-5 stars)
        $ratingBreakdown = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        // Completed jobs count
        $completedJobs = $provider->providerBookings()->where('status', 'completed')->count();

        $profile = $provider->providerProfile;

        return response()->json([
            'data' => [
                'id'               => $provider->id,
                'name'             => $provider->name,
                'is_verified'      => (bool) $provider->is_verified,
                'joined_at'        => $provider->created_at?->toISOString(),
                'member_since'     => $provider->created_at?->format('F Y'),
                'trade'            => $profile?->category ?? 'General Contractor',
                'business_name'    => $profile?->business_name,
                'bio'              => $profile?->bio,
                'skills'           => $profile?->skills ?? [],
                'hourly_rate'      => $profile?->hourly_rate ? (float) $profile->hourly_rate : null,
                'city'             => $profile?->city,
                'is_available'     => (bool) ($profile?->is_available ?? true),
                'portfolio_photos' => $profile?->portfolio_photos ?? [],
                'stats'            => [
                    'average_rating'   => (float) $averageRating,
                    'reviews_count'    => $reviewsCount,
                    'rating_breakdown' => $ratingBreakdown,
                    'completed_jobs'   => $completedJobs,
                    'services_count'   => $provider->services->count(),
                ],
                'services'         => $provider->services->map(fn ($service) => [
                    'id'             => $service->id,
                    'title'          => $service->title,
                    'description'    => $service->description,
                    'category'       => $service->serviceCategory?->name ?? $service->category,
                    'category_icon'  => $service->serviceCategory?->icon ?? 'bi-tools',
                    'price'          => (float) $service->price,
                    'duration_hours' => (float) $service->duration_hours,
                ]),
                'reviews'          => $reviews->map(fn ($rev) => [
                    'id'               => $rev->id,
                    'client_name'      => $rev->client?->name ?? 'Verified Client',
                    'rating'           => $rev->rating,
                    'comment'          => $rev->comment,
                    'date'             => $rev->created_at?->toISOString(),
                    'created_at_human' => $rev->created_at?->diffForHumans(),
                ]),
            ],
        ]);
    }
}
