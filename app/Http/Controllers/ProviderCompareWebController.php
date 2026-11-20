<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderCompareWebController extends Controller
{
    /**
     * Show side-by-side provider comparison page for clients and guests.
     */
    public function index(Request $request): View
    {
        $rawIds = $request->input('ids');
        if (is_string($rawIds)) {
            $ids = array_filter(array_map('intval', explode(',', $rawIds)));
        } elseif (is_array($rawIds)) {
            $ids = array_filter(array_map('intval', $rawIds));
        } else {
            $ids = [];
        }

        $ids = array_slice(array_unique($ids), 0, 4);
        $category = trim((string) $request->input('category', $request->input('trade', '')));

        // If no IDs passed, default to available active providers in the category
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

        // Calculate highlights (Lowest rate, Top rated, Most experienced)
        $minRate = null;
        $maxRating = null;
        $maxExp = null;

        if ($providers->count() > 1) {
            $rates = $providers->map(fn ($p) => $p->providerProfile?->hourly_rate)->filter();
            if ($rates->isNotEmpty()) {
                $minRate = (float) $rates->min();
            }

            $ratings = $providers->map(fn ($p) => $p->providerReviews->count() > 0 ? (float) $p->providerReviews->avg('rating') : 5.0);
            $maxRating = $ratings->max();

            $exps = $providers->map(fn ($p) => $p->providerProfile?->experience_years ?? 1)->filter();
            $maxExp = $exps->max();
        }

        // Available active providers for dropdown selector
        $availableProviders = User::query()
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with('providerProfile')
            ->get();

        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('providers.compare', compact(
            'providers',
            'ids',
            'availableProviders',
            'categories',
            'category',
            'minRate',
            'maxRating',
            'maxExp'
        ));
    }
}
