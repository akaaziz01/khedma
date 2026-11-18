<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'provider_id'    => User::factory()->create(['role' => 'provider'])->id,
            'title'          => fake()->sentence(3),
            'description'    => fake()->paragraph(),
            'category'       => fake()->word(),
            'category_id'    => null,
            'price'          => fake()->randomFloat(2, 10, 500),
            'duration_hours' => fake()->randomElement([0.5, 1.0, 1.5, 2.0, 3.0, 4.0]),
            'is_active'      => false,
            'status'         => 'pending',
            'rejection_reason' => null,
            'moderated_at'   => null,
            'moderated_by'   => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'      => 'approved',
            'is_active'   => true,
            'moderated_at' => now(),
        ]);
    }

    public function rejected(string $reason = 'Does not meet quality standards.'): static
    {
        return $this->state(fn () => [
            'status'           => 'rejected',
            'is_active'        => false,
            'rejection_reason' => $reason,
            'moderated_at'     => now(),
        ]);
    }
}

