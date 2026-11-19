<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'category',
        'bio',
        'skills',
        'hourly_rate',
        'city',
        'is_available',
        'portfolio_photos',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'portfolio_photos' => 'array',
            'hourly_rate' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
