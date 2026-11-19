<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\FavoriteProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FavoriteProvider>
 */
class FavoriteProviderFactory extends Factory
{
    protected $model = FavoriteProvider::class;

    public function definition(): array
    {
        return [
            'client_id'   => User::factory()->create(['role' => UserRole::CLIENT])->id,
            'provider_id' => User::factory()->create(['role' => UserRole::PROVIDER])->id,
            'notes'       => fake()->optional()->sentence(),
        ];
    }
}
