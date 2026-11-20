<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderCompareController extends Controller
{
    /**
     * Compare 2 to 4 providers side by side with rates, experience, and reviews.
     */
    public function compare(Request $request): JsonResponse
    {
        $rawIds = $request->input('ids');
        if (is_string($rawIds)) {
            $ids = array_filter(array_map('intval', explode(',', $rawIds)));
        } elseif (is_array($rawIds)) {
            $ids = array_filter(array_map('intval', $rawIds));
        } else {
            $ids = [];
        }

        // Limit to max 4 providers
        $ids = array_slice(array_unique($ids), 0, 4);

        $category = trim((string) $request->input('category', $request->input('trade', '')));

        // If no IDs provided, default to top providers in the requested category or active providers
        if (empty($ids)) {
            $defaultQuery = User::query()
                ->where('role', 'provider')
                ->where('status', 'active')
                ->whereHas('providerProfile');

            if ($category !== '') {
                $defaultQuery->whereHas('providerProfile', function (Builder $q) use ($category) {
                    $q->where('category', 'like', "%{$category}%");
                });
            }

            $ids = $defaultQuery->take(3)->pluck('id')->all();
        }

        $providers = User::query()
            ->whereIn('id', $ids)
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with([
                'providerProfile',
                'services' => function ($q) {
                    $q->where('status', 'approved')
                        ->where('is_active', true)
                        ->orderBy('price', 'asc');
                },
                'providerReviews' => function ($q) {
                    $q->with('client:id,name')
                        ->orderBy('created_at', 'desc');
                },
            ])
            ->get();

        // Calculate comparison data for each provider
        $providerData = $providers->map(function (User $provider) {
            $profile = $provider->providerProfile;
            $reviews = $provider->providerReviews;
            $services = $provider->services;

            $reviewsCount = $reviews->count();
            $averageRating = $reviewsCount > 0 ? round($reviews->avg('rating'), 1) : 5.0;
            $fiveStarCount = $reviews->where('rating', 5)->count();
            $fiveStarPercentage = $reviewsCount > 0 ? round(($fiveStarCount / $reviewsCount) * 100) : 100;
            $completedJobs = $provider->providerBookings()->where('status', 'completed')->count();

            $hourlyRate = $profile?->hourly_rate ? (float) $profile->hourly_rate : null;
            $minServicePrice = $services->min('price') ? (float) $services->min('price') : null;
            $maxServicePrice = $services->max('price') ? (float) $services->max('price') : null;

            $latestReview = $reviews->first();

            return [
                'id'                 => $provider->id,
                'name'               => $provider->name,
                'is_verified'        => (bool) $provider->is_verified,
                'joined_at'          => $provider->created_at?->toISOString(),
                'member_since'       => $provider->created_at?->format('F Y'),
                'trade'              => $profile?->category ?? 'General Contractor',
                'business_name'      => $profile?->business_name,
                'city'               => $profile?->city ?? 'Tunis',
                'is_available'       => (bool) ($profile?->is_available ?? true),
                'bio'                => $profile?->bio,
                'skills'             => $profile?->skills ?? [],
                'portfolio_count'    => is_array($profile?->portfolio_photos) ? count($profile->portfolio_photos) : 0,
                'portfolio_photos'   => $profile?->portfolio_photos ?? [],
                // Rates
                'rates' => [
                    'hourly_rate'       => $hourlyRate,
                    'min_service_price' => $minServicePrice,
                    'max_service_price' => $maxServicePrice,
                    'services_count'    => $services->count(),
                ],
                // Experience
                'experience' => [
                    'years'          => $profile?->experience_years ?? 1,
                    'completed_jobs' => $completedJobs,
                    'is_verified'    => (bool) $provider->is_verified,
                ],
                // Reviews
                'reviews' => [
                    'average_rating'       => (float) $averageRating,
                    'reviews_count'        => $reviewsCount,
                    'five_star_percentage' => $fiveStarPercentage,
                    'latest_quote'         => $latestReview ? [
                        'comment'     => $latestReview->comment,
                        'rating'      => $latestReview->rating,
                        'client_name' => $latestReview->client?->name ?? 'Verified Client',
                    ] : null,
                ],
                'services_summary' => $services->map(fn ($s) => [
                    'id'             => $s->id,
                    'title'          => $s->title,
                    'price'          => (float) $s->price,
                    'duration_hours' => (float) $s->duration_hours,
                ]),
                // Computed highlights (will be populated below)
                'highlights' => [],
            ];
        });

        // Compute relative highlights if more than 1 provider compared
        if ($providerData->count() > 1) {
            // Best hourly price
            $ratesWithValues = $providerData->whereNotNull('rates.hourly_rate');
            if ($ratesWithValues->isNotEmpty()) {
                $minRate = $ratesWithValues->min('rates.hourly_rate');
                $providerData = $providerData->map(function ($p) use ($minRate) {
                    if (($p['rates']['hourly_rate'] ?? null) === $minRate) {
                        $p['highlights'][] = [
                            'type'  => 'price',
                            'label' => 'Best Hourly Rate',
                            'icon'  => 'bi-cash-coin',
                            'color' => 'emerald',
                        ];
                    }
                    return $p;
                });
            }

            // Highest rated
            $maxRating = $providerData->max('reviews.average_rating');
            $maxReviews = $providerData->max('reviews.reviews_count');
            if ($maxRating > 0) {
                $providerData = $providerData->map(function ($p) use ($maxRating, $maxReviews) {
                    if ($p['reviews']['average_rating'] === $maxRating && ($p['reviews']['reviews_count'] === $maxReviews || $maxReviews === 0)) {
                        $p['highlights'][] = [
                            'type'  => 'rating',
                            'label' => 'Top Rated',
                            'icon'  => 'bi-star-fill',
                            'color' => 'amber',
                        ];
                    }
                    return $p;
                });
            }

            // Most experienced (years or completed jobs)
            $maxExp = $providerData->max('experience.years');
            if ($maxExp > 1) {
                $providerData = $providerData->map(function ($p) use ($maxExp) {
                    if ($p['experience']['years'] === $maxExp) {
                        $p['highlights'][] = [
                            'type'  => 'experience',
                            'label' => 'Most Experienced',
                            'icon'  => 'bi-award-fill',
                            'color' => 'blue',
                        ];
                    }
                    return $p;
                });
            }
        }

        // Available active providers for dropdown selector
        $availableProviders = User::query()
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with('providerProfile')
            ->get(['id', 'name', 'is_verified'])
            ->map(fn ($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'is_verified'   => (bool) $p->is_verified,
                'trade'         => $p->providerProfile?->category ?? 'Contractor',
                'city'          => $p->providerProfile?->city ?? 'Tunisia',
                'hourly_rate'   => $p->providerProfile?->hourly_rate ? (float) $p->providerProfile->hourly_rate : null,
            ]);

        return response()->json([
            'success'             => true,
            'providers'           => $providerData->values(),
            'available_providers' => $availableProviders,
            'selected_ids'        => $ids,
        ]);
    }
}
