<?php

namespace Database\Factories;

use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentReport>
 */
class IncidentReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'reported_client_id' => null,
            'booking_id' => null,
            'type' => 'issue',
            'subject' => fake()->sentence(4),
            'details' => fake()->paragraph(),
            'status' => 'open',
            'admin_notes' => null,
        ];
    }
}
