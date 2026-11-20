<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with the essential administrator account.
     */
    public function run(): void
    {
        // Primary Admin Account
        User::updateOrCreate(
            ['email' => 'admin@khedma.tn'],
            [
                'name' => 'Khedma Tunisie Admin',
                'password' => 'AdminPass123!',
                'role' => UserRole::ADMIN,
                'phone' => '+216 71 000 000',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Admin alias for test/environment compatibility
        User::updateOrCreate(
            ['email' => 'admin@khedma.ma'],
            [
                'name' => 'Khedma Tunisie Admin',
                'password' => 'AdminPass123!',
                'role' => UserRole::ADMIN,
                'phone' => '+216 71 000 000',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Official Client Account
        $client = User::updateOrCreate(
            ['email' => 'client@khedma.tn'],
            [
                'name' => 'Client Khedma',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+216 98 123 456',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $client->clientProfile()->updateOrCreate(
            ['user_id' => $client->id],
            [
                'city' => 'Tunis',
                'address' => 'Avenue Habib Bourguiba, Tunis',
            ]
        );

        // Official Provider Account 1: Provider Khedma (Tunis, 45 TND/hr, 8 yrs)
        $provider = User::updateOrCreate(
            ['email' => 'provider@khedma.tn'],
            [
                'name' => 'Provider Khedma',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+216 20 123 456',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $provider->providerProfile()->updateOrCreate(
            ['user_id' => $provider->id],
            [
                'business_name' => 'Khedma Pro Services',
                'category' => 'Plumbing',
                'hourly_rate' => 45.00,
                'experience_years' => 8,
                'city' => 'Tunis',
                'bio' => 'Certified plumbing and home maintenance professional in Tunis. Available for emergency repairs and renovations.',
                'skills' => ['Emergency Plumbing', 'Pipe Repair', 'Water Heaters', 'Sanitary Installation'],
                'is_available' => true,
                'portfolio_photos' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189',
                        'title' => 'Bathroom Pipe Installation',
                        'caption' => 'High quality copper piping and drain system',
                    ],
                ],
            ]
        );

        // Seed realistic services for Provider Khedma
        \App\Models\Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Emergency Pipe Leak Repair'],
            [
                'category' => 'Plumbing',
                'description' => 'Fast on-site diagnostic and repair for water leaks, burst pipes, and drain overflow.',
                'price' => 50.00,
                'duration_hours' => 1.5,
                'status' => 'approved',
                'is_active' => true,
            ]
        );
        \App\Models\Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Water Heater Diagnostics & Repair'],
            [
                'category' => 'Plumbing',
                'description' => 'Electric and gas water heater maintenance, thermostat replacement, and descaling.',
                'price' => 80.00,
                'duration_hours' => 2.0,
                'status' => 'approved',
                'is_active' => true,
            ]
        );
        \App\Models\Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Sanitary & Faucet Replacement'],
            [
                'category' => 'Plumbing',
                'description' => 'Modern kitchen and bathroom mixer taps, sink fittings, and toilet tank valve replacement.',
                'price' => 45.00,
                'duration_hours' => 1.0,
                'status' => 'approved',
                'is_active' => true,
            ]
        );

        // Official Provider Account 2: Sami Trabelsi (Ariana, 35 TND/hr, 12 yrs, Lowest Rate)
        $provider2 = User::updateOrCreate(
            ['email' => 'sami.pro@khedma.tn'],
            [
                'name' => 'Sami Trabelsi',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+216 98 234 567',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $provider2->providerProfile()->updateOrCreate(
            ['user_id' => $provider2->id],
            [
                'business_name' => 'Trabelsi Plomberie Express',
                'category' => 'Plumbing',
                'hourly_rate' => 35.00,
                'experience_years' => 12,
                'city' => 'Ariana',
                'bio' => 'Master artisan with 12 years of experience in residential piping, leak detection, and solar water heaters.',
                'skills' => ['Leak Detection', 'Boiler Maintenance', 'Copper Soldering', 'Sanitary Fitting'],
                'is_available' => true,
                'portfolio_photos' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd',
                        'title' => 'Central Heating Piping',
                        'caption' => 'Installed complete residential heating circuit',
                    ],
                ],
            ]
        );

        // Official Provider Account 3: Karim Ben Amor (Sousse, 40 TND/hr, 6 yrs)
        $provider3 = User::updateOrCreate(
            ['email' => 'karim.pro@khedma.tn'],
            [
                'name' => 'Karim Ben Amor',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+216 55 345 678',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $provider3->providerProfile()->updateOrCreate(
            ['user_id' => $provider3->id],
            [
                'business_name' => 'Ben Amor Rénovations',
                'category' => 'Plumbing',
                'hourly_rate' => 40.00,
                'experience_years' => 6,
                'city' => 'Sousse',
                'bio' => 'Fast and reliable plumbing services across the Sahel region. Specializing in bathroom remodels and rapid emergency repairs.',
                'skills' => ['Bathroom Remodeling', 'Drain Unblocking', 'Emergency Response', 'PEX Piping'],
                'is_available' => true,
                'portfolio_photos' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1621905251918-48416bd8575a',
                        'title' => 'Modern Shower Drain',
                        'caption' => 'Italian walk-in shower plumbing installation',
                    ],
                ],
            ]
        );

        // Create initial verified review from client to Sami Trabelsi
        $clientUser = User::where('email', 'client@khedma.tn')->first();
        if ($clientUser) {
            $booking = \App\Models\Booking::firstOrCreate(
                ['booking_number' => 'KB-SAM-01'],
                [
                    'client_id'      => $clientUser->id,
                    'provider_id'    => $provider2->id,
                    'service_title'  => 'Emergency Leak Repair',
                    'category'       => 'Plumbing',
                    'status'         => 'completed',
                    'total_price'    => 70.00,
                    'scheduled_at'   => now()->subDays(5),
                    'completed_at'   => now()->subDays(4),
                    'address'        => 'Les Berges du Lac 2',
                    'city'           => 'Tunis',
                ]
            );

            \App\Models\Review::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'client_id'   => $clientUser->id,
                    'provider_id' => $provider2->id,
                    'rating'      => 5,
                    'comment'     => 'Excellent service! Sami arrived within 30 minutes and fixed our water pipe with great professionalism.',
                ]
            );
        }
    }
}
