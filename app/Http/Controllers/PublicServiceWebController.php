<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicServiceWebController extends Controller
{
    /**
     * Browse and search services page for guests and clients.
     */
    public function index(Request $request): View
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

        $location = trim((string) $request->input('location', $request->input('city', '')));
        if ($location !== '') {
            $query->whereHas('provider.providerProfile', function (Builder $pq) use ($location) {
                $pq->where('city', 'like', "%{$location}%");
            });
        }

        if ($request->filled('min_price') && is_numeric($request->input('min_price'))) {
            $query->where('services.price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price') && is_numeric($request->input('max_price'))) {
            $query->where('services.price', '<=', (float) $request->input('max_price'));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc'    => $query->orderBy('price', 'asc'),
            'price_desc'   => $query->orderBy('price', 'desc'),
            'duration_asc' => $query->orderBy('duration_hours', 'asc'),
            default        => $query->orderBy('created_at', 'desc'),
        };

        $services = $query->paginate(12)->withQueryString();

        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $cities = ProviderProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->whereHas('user', function (Builder $uq) {
                $uq->where('status', 'active');
            })
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return view('services.index', compact('services', 'categories', 'cities', 'keyword', 'trade', 'location', 'sort'));
    }

    /**
     * View single service details.
     */
    public function show(int $id): View
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

        return view('services.show', compact('service'));
    }
}
