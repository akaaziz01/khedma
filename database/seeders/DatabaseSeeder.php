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

        // Official Provider Account
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
    }
}
