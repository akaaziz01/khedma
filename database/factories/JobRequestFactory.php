<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\JobRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobRequest>
 */
class JobRequestFactory extends Factory
{
    protected $model = JobRequest::class;

    public function definition(): array
    {
        return [
            'request_number' => JobRequest::generateRequestNumber(),
            'client_id'      => User::factory()->create(['role' => UserRole::CLIENT])->id,
            'title'          => fake()->sentence(4),
            'category'       => fake()->randomElement(['Plumbing', 'Electrical', 'Painting', 'Carpentry', 'HVAC']),
            'description'    => fake()->paragraph(),
            'budget'         => fake()->randomFloat(2, 50, 800),
            'city'           => fake()->randomElement(['Tunis', 'Ariana', 'Sousse', 'Sfax', 'Nabeul']),
            'address'        => fake()->address(),
            'urgency'        => fake()->randomElement(['low', 'medium', 'urgent', 'emergency']),
            'preferred_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'status'         => 'open',
        ];
    }
}
