<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteProvider extends Model
{
    use HasFactory;

    protected $table = 'favorite_providers';

    protected $fillable = [
        'client_id',
        'provider_id',
        'notes',
    ];

    /**
     * The client who favorited the provider.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * The provider that was bookmarked.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
