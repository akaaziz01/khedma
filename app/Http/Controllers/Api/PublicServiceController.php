<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicServiceController extends Controller
{
    /**
     * Search and list approved, active services for guests and clients.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query()
            ->where('services.status', 'approved')
            ->where('services.is_active', true)
            ->whereHas('provider', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->with([
                'provider:id,name,email,phone,is_verified,created_at',
                'provider.providerProfile',
                'serviceCategory',
            ]);

        // Keyword filter: searches title, description, provider name, business name, skills
        $keyword = trim((string) $request->input('keyword', $request->input('q', '')));
        if ($keyword !== '') {
            $query->where(function (Builder $sub) use ($keyword) {
                $sub->where('services.title', 'like', "%{$keyword}%")
                    ->orWhere('services.description', 'like', "%{$keyword}%")
                    ->orWhereHas('provider', function (Builder $pq) use ($keyword) {
                        $pq->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('providerProfile', function (Builder $ppq) use ($keyword) {
                                $ppq->where('business_name', 'like', "%{$keyword}%")
                                    ->orWhere('skills', 'like', "%{$keyword}%")
                                    ->orWhere('category', 'like', "%{$keyword}%");
                            });
                    });
            });
        }

        // Trade / Category filter (accepts trade string, category slug, or category_id)
        $trade = trim((string) $request->input('trade', $request->input('category', $request->input('category_id', ''))));
        if ($trade !== '') {
            if (is_numeric($trade)) {
                $query->where('services.category_id', (int) $trade);
            } else {
                $query->where(function (Builder $sub) use ($trade) {
                    $sub->where('services.category', 'like', "%{$trade}%")
                        ->orWhereHas('serviceCategory', function (Builder $cq) use ($trade) {
                            $cq->where('slug', $trade)
                                ->orWhere('name', 'like', "%{$trade}%");
                        });
                });
            }
        }

        // Location / City filter (matches provider profile city)
        $location = trim((string) $request->input('location', $request->input('city', '')));
        if ($location !== '') {
            $query->whereHas('provider.providerProfile', function (Builder $pq) use ($location) {
                $pq->where('city', 'like', "%{$location}%");
            });
        }

        // Price range filters
        if ($request->filled('min_price') && is_numeric($request->input('min_price'))) {
            $query->where('services.price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price') && is_numeric($request->input('max_price'))) {
            $query->where('services.price', '<=', (float) $request->input('max_price'));
        }

        // Availability filter (providers who are currently available for jobs)
        if ($request->boolean('available_only') || $request->input('availability') === 'available' || $request->input('availability') === '1') {
            $query->whereHas('provider.providerProfile', function (Builder $pq) {
                $pq->where('is_available', true);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc'    => $query->orderBy('price', 'asc'),
            'price_desc'   => $query->orderBy('price', 'desc'),
            'duration_asc' => $query->orderBy('duration_hours', 'asc'),
            default        => $query->orderBy('created_at', 'desc'),
        };

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => ServiceResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    /**
     * Show single public service details.
     */
    public function show(int $id): JsonResponse
    {
        $service = Service::query()
            ->where('services.status', 'approved')
            ->where('services.is_active', true)
            ->whereHas('provider', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->with([
                'provider:id,name,email,phone,is_verified,created_at',
                'provider.providerProfile',
                'serviceCategory',
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => new ServiceResource($service),
        ]);
    }

    /**
     * Filter metadata (available categories and cities with active services).
     */
    public function filters(): JsonResponse
    {
        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        // Distinct cities from active providers who have approved services
        $cities = ProviderProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->whereHas('user', function (Builder $uq) {
                $uq->where('status', 'active')
                    ->whereHas('services', function (Builder $sq) {
                        $sq->where('status', 'approved')->where('is_active', true);
                    });
            })
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return response()->json([
            'categories' => $categories,
            'cities'     => $cities,
        ]);
    }
}
