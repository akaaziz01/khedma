<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProviderWebController extends Controller
{
    /**
     * Directory page: Search & filter providers across Tunisia.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with(['providerProfile', 'services' => fn ($q) => $q->where('status', 'approved')->where('is_active', true)])
            ->withCount('providerReviews')
            ->withAvg('providerReviews', 'rating')
            ->withCount(['providerBookings as completed_jobs_count' => fn ($q) => $q->where('status', 'completed')])
            ->withCount(['services as active_services_count' => fn ($q) => $q->where('status', 'approved')->where('is_active', true)]);

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

        // 4. Hourly Rate Range (min_price, max_price)
        $minPrice = $request->input('min_price', $request->input('min_rate'));
        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            $minVal = (float) $minPrice;
            $query->whereHas('providerProfile', fn (Builder $pq) => $pq->where('hourly_rate', '>=', $minVal));
        }

        $maxPrice = $request->input('max_price', $request->input('max_rate'));
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            $maxVal = (float) $maxPrice;
            $query->whereHas('providerProfile', fn (Builder $pq) => $pq->where('hourly_rate', '<=', $maxVal));
        }

        // 5. Minimum Rating (min_rating)
        if ($request->filled('min_rating') && is_numeric($request->input('min_rating'))) {
            $minRating = (float) $request->input('min_rating');
            $query->having('provider_reviews_avg_rating', '>=', $minRating);
        }

        // 6. Availability filter (available_only)
        if ($request->boolean('available_only') || $request->input('availability') === 'available') {
            $query->whereHas('providerProfile', fn (Builder $pq) => $pq->where('is_available', true));
        }

        // 7. Verified Pro filter (is_verified)
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
        $providers = $query->paginate($perPage)->withQueryString();

        // Metadata for filter dropdowns
        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $cities = ProviderProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->whereHas('user', fn ($uq) => $uq->where('status', 'active'))
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        $maxRateInDb = (float) (ProviderProfile::whereHas('user', fn ($uq) => $uq->where('status', 'active'))
            ->max('hourly_rate') ?: 100);

        return view('providers.index', compact('providers', 'categories', 'cities', 'maxRateInDb'));
    }

    /**
     * Show provider's public profile page for guests and prospective clients.
     */
    public function show(int $id): View
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
                        ->with('serviceCategory')
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

        $ratingBreakdown = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        $completedJobs = $provider->providerBookings()->where('status', 'completed')->count();

        $currentUser = auth()->user() ?? auth('sanctum')->user();
        $isFavorited = $currentUser ? $currentUser->hasFavorited($provider->id) : false;

        return view('providers.show', compact('provider', 'reviews', 'reviewsCount', 'averageRating', 'ratingBreakdown', 'completedJobs', 'isFavorited'));
    }
}
