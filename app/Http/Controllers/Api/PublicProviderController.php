<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicProviderSummaryResource;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProviderController extends Controller
{
    /**
     * Search and list verified providers with multi-criteria filters.
     * Filterable by trade, price range, min rating, availability, location, and keywords.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with(['providerProfile'])
            ->withCount('providerReviews')
            ->withAvg('providerReviews', 'rating')
            ->withCount(['providerBookings as completed_jobs_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withCount(['services as active_services_count' => function ($q) {
                $q->where('status', 'approved')->where('is_active', true);
            }]);

        // 1. Keyword search (name, business_name, bio, skills, trade)
        $keyword = trim((string) $request->input('keyword', $request->input('q', '')));
        if ($keyword !== '') {
            $query->where(function (Builder $sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('providerProfile', function (Builder $pq) use ($keyword) {
                        $pq->where('business_name', 'like', "%{$keyword}%")
                            ->orWhere('bio', 'like', "%{$keyword}%")
                            ->orWhere('category', 'like', "%{$keyword}%")
                            ->orWhere('skills', 'like', "%{$keyword}%");
                    });
            });
        }

        // 2. Trade / Category filter
        $trade = trim((string) $request->input('trade', $request->input('category', '')));
        if ($trade !== '') {
            $query->whereHas('providerProfile', function (Builder $pq) use ($trade) {
                $pq->where('category', 'like', "%{$trade}%");
            });
        }

        // 3. Location / City filter
        $location = trim((string) $request->input('location', $request->input('city', '')));
        if ($location !== '') {
            $query->whereHas('providerProfile', function (Builder $pq) use ($location) {
                $pq->where('city', 'like', "%{$location}%");
            });
        }

        // 4. Hourly Rate Range (min_price / min_rate, max_price / max_rate)
        $minPrice = $request->input('min_price', $request->input('min_rate'));
        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            $minVal = (float) $minPrice;
            $query->whereHas('providerProfile', function (Builder $pq) use ($minVal) {
                $pq->where('hourly_rate', '>=', $minVal);
            });
        }

        $maxPrice = $request->input('max_price', $request->input('max_rate'));
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            $maxVal = (float) $maxPrice;
            $query->whereHas('providerProfile', function (Builder $pq) use ($maxVal) {
                $pq->where('hourly_rate', '<=', $maxVal);
            });
        }

        // 5. Minimum Rating
        if ($request->filled('min_rating') && is_numeric($request->input('min_rating'))) {
            $minRating = (float) $request->input('min_rating');
            $query->whereIn('users.id', function ($sub) use ($minRating) {
                $sub->select('provider_id')
                    ->from('reviews')
                    ->groupBy('provider_id')
                    ->havingRaw('AVG(rating) >= ' . (float) $minRating);
            });
        }

        // 6. Availability filter
        if ($request->boolean('available_only') || $request->input('availability') === 'available') {
            $query->whereHas('providerProfile', function (Builder $pq) {
                $pq->where('is_available', true);
            });
        }

        // 7. Verified Pro filter
        if ($request->boolean('is_verified')) {
            $query->where('is_verified', true);
        }

        // 8. Sorting
        $sort = $request->input('sort', 'rating_desc');
        match ($sort) {
            'price_asc'     => $query->join('provider_profiles', 'users.id', '=', 'provider_profiles.user_id')
                                    ->orderBy('provider_profiles.hourly_rate', 'asc')
                                    ->select('users.*'),
            'price_desc'    => $query->join('provider_profiles', 'users.id', '=', 'provider_profiles.user_id')
                                    ->orderBy('provider_profiles.hourly_rate', 'desc')
                                    ->select('users.*'),
            'reviews_desc'  => $query->orderBy('provider_reviews_count', 'desc'),
            'newest'        => $query->orderBy('created_at', 'desc'),
            default         => $query->orderByRaw('COALESCE(provider_reviews_avg_rating, 5.0) desc')->orderBy('provider_reviews_count', 'desc'),
        };

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => PublicProviderSummaryResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    /**
     * Filter metadata (available trades, operating cities, and rate bounds).
     */
    public function filters(): JsonResponse
    {
        $trades = ProviderProfile::whereNotNull('category')
            ->where('category', '!=', '')
            ->whereHas('user', fn ($uq) => $uq->where('status', 'active'))
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $cities = ProviderProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->whereHas('user', fn ($uq) => $uq->where('status', 'active'))
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        $maxHourlyRate = (float) (ProviderProfile::whereHas('user', fn ($uq) => $uq->where('status', 'active'))
            ->max('hourly_rate') ?: 100);

        return response()->json([
            'trades'          => $trades,
            'cities'          => $cities,
            'max_hourly_rate' => $maxHourlyRate,
        ]);
    }

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

        $currentUser = auth('sanctum')->user();
        $isFavorited = $currentUser ? $currentUser->hasFavorited($provider->id) : false;

        return response()->json([
            'data' => [
                'id'               => $provider->id,
                'name'             => $provider->name,
                'is_favorited'     => $isFavorited,
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
