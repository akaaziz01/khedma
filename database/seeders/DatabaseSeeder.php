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
    }
}
