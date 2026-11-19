<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProviderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->providerProfile;
        
        // Use withAvg/withCount attributes if loaded, otherwise fallback
        $avgRating = isset($this->provider_reviews_avg_rating)
            ? round((float) $this->provider_reviews_avg_rating, 1)
            : ($this->providerReviews()->count() > 0 ? round($this->providerReviews()->avg('rating'), 1) : 5.0);

        $reviewsCount = $this->provider_reviews_count ?? $this->providerReviews()->count();

        $completedJobs = $this->completed_jobs_count ?? $this->providerBookings()->where('status', 'completed')->count();

        $activeServices = $this->active_services_count ?? $this->services()->where('status', 'approved')->where('is_active', true)->count();

        // Check favorite status for current authenticated client
        $currentUser = auth('sanctum')->user() ?? $request->user();
        $isFavorited = $currentUser ? $currentUser->hasFavorited($this->id) : false;

        $skills = is_array($profile?->skills)
            ? $profile->skills
            : (is_string($profile?->skills) ? json_decode($profile->skills, true) : []);

        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'avatar_initial'        => strtoupper(substr($this->name, 0, 1)),
            'is_verified'           => (bool) $this->is_verified,
            'business_name'         => $profile?->business_name,
            'trade'                 => $profile?->category ?? 'General Contractor',
            'bio'                   => $profile?->bio,
            'skills'                => array_values($skills ?? []),
            'hourly_rate'           => $profile?->hourly_rate ? (float) $profile->hourly_rate : null,
            'city'                  => $profile?->city ?? 'Tunis',
            'is_available'          => (bool) ($profile?->is_available ?? true),
            'rating'                => (float) $avgRating,
            'reviews_count'         => (int) $reviewsCount,
            'completed_jobs'        => (int) $completedJobs,
            'active_services_count' => (int) $activeServices,
            'is_favorited'          => $isFavorited,
            'joined_at'             => $this->created_at?->toISOString(),
            'member_since'          => $this->created_at?->format('M Y'),
            'profile_url'           => "/providers/{$this->id}",
        ];
    }
}
