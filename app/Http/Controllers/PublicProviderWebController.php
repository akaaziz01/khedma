<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class PublicProviderWebController extends Controller
{
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

        return view('providers.show', compact('provider', 'reviews', 'reviewsCount', 'averageRating', 'ratingBreakdown', 'completedJobs'));
    }
}
