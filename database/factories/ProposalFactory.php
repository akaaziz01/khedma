<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\JobRequest;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        $deliveryDays = fake()->numberBetween(1, 14);

        return [
            'proposal_number'          => Proposal::generateProposalNumber(),
            'job_request_id'           => JobRequest::factory(),
            'provider_id'              => User::factory()->create(['role' => UserRole::PROVIDER])->id,
            'price'                    => fake()->randomFloat(2, 40, 600),
            'delivery_time'            => "{$deliveryDays} day(s)",
            'delivery_days'            => $deliveryDays,
            'estimated_duration_hours' => fake()->randomFloat(1, 1, 12),
            'message'                  => fake()->paragraph(),
            'status'                   => 'pending',
        ];
    }
}
