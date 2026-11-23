<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // When resolving through FavoriteProvider model
        $provider = $this->provider ?? $this->resource;
        $profile = $provider->providerProfile;
        $reviews = $provider->providerReviews()->where('status', 'approved')->get();
        $reviewsCount = $reviews ? $reviews->count() : 0;
        $avgRating = $reviewsCount > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        $completedJobs = $provider->providerBookings
            ? $provider->providerBookings->where('status', 'completed')->count()
            : 0;

        return [
            'id'                   => $this->id,
            'provider_id'          => $provider->id,
            'name'                 => $provider->name,
            'avatar_initial'       => strtoupper(substr($provider->name, 0, 1)),
            'is_verified'          => (bool) $provider->is_verified,
            'business_name'        => $profile?->business_name,
            'trade'                => $profile?->category ?? 'General Contractor',
            'city'                 => $profile?->city ?? 'Tunis',
            'hourly_rate'          => $profile?->hourly_rate ? (float) $profile->hourly_rate : null,
            'is_available'         => (bool) ($profile?->is_available ?? true),
            'rating'               => (float) $avgRating,
            'reviews_count'        => $reviewsCount,
            'completed_jobs'       => $completedJobs,
            'notes'                => $this->notes ?? ($this->pivot?->notes ?? null),
            'saved_at'             => ($this->created_at ?? $this->pivot?->created_at)?->toISOString(),
            'profile_url'          => "/providers/{$provider->id}",
        ];
    }
}
