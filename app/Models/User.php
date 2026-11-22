<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'is_verified',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function providerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'provider_id');
    }

    public function clientBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function clientJobRequests(): HasMany
    {
        return $this->hasMany(JobRequest::class, 'client_id');
    }

    public function providerProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'provider_id');
    }

    public function providerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'provider_id');
    }

    /**
     * Providers saved by this client.
     */
    public function favoriteProviders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_providers', 'client_id', 'provider_id')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Clients who have favorited this provider.
     */
    public function favoritedByClients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_providers', 'provider_id', 'client_id')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Check if client has saved a given provider.
     */
    public function hasFavorited(int|User $provider): bool
    {
        $providerId = $provider instanceof User ? $provider->id : $provider;
        return $this->favoriteProviders()->where('users.id', $providerId)->exists();
    }

    /**
     * Save a provider to favorites with optional note.
     */
    public function favorite(int|User $provider, ?string $notes = null): FavoriteProvider
    {
        $providerId = $provider instanceof User ? $provider->id : $provider;

        return FavoriteProvider::updateOrCreate(
            ['client_id' => $this->id, 'provider_id' => $providerId],
            ['notes' => $notes]
        );
    }

    /**
     * Remove a provider from favorites.
     */
    public function unfavorite(int|User $provider): bool
    {
        $providerId = $provider instanceof User ? $provider->id : $provider;
        return (bool) FavoriteProvider::where('client_id', $this->id)
            ->where('provider_id', $providerId)
            ->delete();
    }

    public function isClient(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    public function isProvider(): bool
    {
        return $this->role === UserRole::PROVIDER;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function markAsVerified(): bool
    {
        $this->is_verified = true;
        $this->verified_at = now();
        return $this->save();
    }

    public function markAsUnverified(): bool
    {
        $this->is_verified = false;
        $this->verified_at = null;
        return $this->save();
    }

    public function getActiveProfileAttribute()
    {
        if ($this->isClient()) {
            return $this->clientProfile;
        }

        if ($this->isProvider()) {
            return $this->providerProfile;
        }

        return null;
    }
}
