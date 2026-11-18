<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $client   = User::factory()->create(['role' => 'client']);
        $provider = User::factory()->create(['role' => 'provider']);

        return [
            'booking_number' => strtoupper(Str::random(8)),
            'client_id'      => $client->id,
            'provider_id'    => $provider->id,
            'service_id'     => null,
            'service_title'  => fake()->sentence(3),
            'category'       => fake()->word(),
            'status'         => 'pending',
            'total_price'    => fake()->randomFloat(2, 20, 500),
            'scheduled_at'   => fake()->dateTimeBetween('now', '+30 days'),
            'address'        => fake()->streetAddress(),
            'city'           => 'Tunis',
            'client_notes'   => null,
            'provider_notes' => null,
            'completed_at'   => null,
        ];
    }

    public function forService(Service $service): static
    {
        return $this->state(fn () => [
            'service_id'    => $service->id,
            'service_title' => $service->title,
            'provider_id'   => $service->provider_id,
            'category'      => $service->category,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}

