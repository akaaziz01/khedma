<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Clients
        $client1 = User::updateOrCreate(
            ['email' => 'client@khedma.ma'],
            [
                'name' => 'Fatima Zahra',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+212611223344',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client1->clientProfile()->updateOrCreate(
            ['user_id' => $client1->id],
            [
                'city' => 'Casablanca',
                'address' => '45 Boulevard d\'Anfa, Appt 12',
            ]
        );

        $client2 = User::updateOrCreate(
            ['email' => 'amina.bensouda@example.com'],
            [
                'name' => 'Amina Bensouda',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+212622334455',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client2->clientProfile()->updateOrCreate(
            ['user_id' => $client2->id],
            [
                'city' => 'Casablanca',
                'address' => 'Rue Jean Jaurès, Maarif',
            ]
        );

        $client3 = User::updateOrCreate(
            ['email' => 'karim.tazi@example.com'],
            [
                'name' => 'Karim Tazi',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+212633445566',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client3->clientProfile()->updateOrCreate(
            ['user_id' => $client3->id],
            [
                'city' => 'Casablanca',
                'address' => 'Résidence Les Palmiers, Bourgogne',
            ]
        );

        // 2. Main Provider (Youssef El Amrani)
        $provider = User::updateOrCreate(
            ['email' => 'provider@khedma.ma'],
            [
                'name' => 'Youssef El Amrani',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+212699887766',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $provider->providerProfile()->updateOrCreate(
            ['user_id' => $provider->id],
            [
                'business_name' => 'Amrani Plumbing & HVAC Services',
                'category' => 'Plumbing',
                'bio' => 'Certified Master Plumber with over 10 years of experience across Casablanca and Rabat. Specialized in emergency leak detection, sanitary installations, and high-efficiency water heater maintenance.',
                'skills' => ['Emergency Leak Diagnostics', 'Water Heater Fitting', 'Bathroom Renovation', 'High-Pressure Flush', 'Copper & PEX Piping'],
                'hourly_rate' => 150.00,
                'city' => 'Casablanca',
                'is_available' => true,
            ]
        );

        // 3. Admin user
        User::updateOrCreate(
            ['email' => 'admin@khedma.ma'],
            [
                'name' => 'Khedma Administrator',
                'password' => 'AdminPass123!',
                'role' => UserRole::ADMIN,
                'phone' => '+212600000000',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 4. Provider Real Services Catalog
        $service1 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Emergency Pipe Leak Repair & Diagnostics'],
            [
                'category' => 'Plumbing',
                'description' => 'Fast acoustic and thermal camera detection of concealed water leaks in walls and floors, plus immediate pipe repair or replacement.',
                'price' => 180.00,
                'duration_hours' => 1.5,
                'is_active' => true,
            ]
        );

        $service2 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Electric & Gas Water Heater Installation'],
            [
                'category' => 'Plumbing',
                'description' => 'Safe installation, pressure valve testing, electrical connection, and descaling maintenance for all leading heater brands.',
                'price' => 350.00,
                'duration_hours' => 2.5,
                'is_active' => true,
            ]
        );

        $service3 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Full Sanitary Fixtures & Faucet Fitting'],
            [
                'category' => 'Plumbing',
                'description' => 'Installation of Italian walk-in showers, suspended toilets, vanity sinks, and modern thermostatic mixing valves.',
                'price' => 500.00,
                'duration_hours' => 4.0,
                'is_active' => true,
            ]
        );

        $service4 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'High-Pressure Drain & Sewer Unclogging'],
            [
                'category' => 'Plumbing',
                'description' => 'Mechanical snake and hydro-jetting clearance of severe blockages in kitchen drains, toilets, and main outlet pipes.',
                'price' => 150.00,
                'duration_hours' => 1.0,
                'is_active' => true,
            ]
        );

        // 5. Provider Real Bookings
        // Pending booking (Fatima Zahra)
        Booking::updateOrCreate(
            ['booking_number' => 'KHD-2026-001'],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'service_id' => $service1->id,
                'service_title' => $service1->title,
                'category' => 'Plumbing',
                'status' => 'pending',
                'total_price' => 180.00,
                'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
                'address' => '45 Boulevard d\'Anfa, Appt 12',
                'city' => 'Casablanca',
                'client_notes' => 'There is a persistent leak under the kitchen sink dripping onto the cabinetry and floor. Need urgent inspection.',
            ]
        );

        // Accepted booking (Amina Bensouda)
        Booking::updateOrCreate(
            ['booking_number' => 'KHD-2026-002'],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'service_id' => $service2->id,
                'service_title' => $service2->title,
                'category' => 'Plumbing',
                'status' => 'accepted',
                'total_price' => 350.00,
                'scheduled_at' => now()->addDays(2)->setHour(14)->setMinute(30),
                'address' => 'Rue Jean Jaurès, Maarif',
                'city' => 'Casablanca',
                'client_notes' => 'New 80L electric boiler purchased, ready to mount on bathroom wall.',
                'provider_notes' => 'Confirmed with client. Bringing safety pressure valves and mounting brackets.',
            ]
        );

        // In Progress booking (Karim Tazi)
        Booking::updateOrCreate(
            ['booking_number' => 'KHD-2026-003'],
            [
                'client_id' => $client3->id,
                'provider_id' => $provider->id,
                'service_id' => $service3->id,
                'service_title' => $service3->title,
                'category' => 'Plumbing',
                'status' => 'in_progress',
                'total_price' => 500.00,
                'scheduled_at' => now()->subHours(2),
                'address' => 'Résidence Les Palmiers, Bourgogne',
                'city' => 'Casablanca',
                'client_notes' => 'Replacing old bathtub with shower enclosure and new sink.',
                'provider_notes' => 'Currently fitting copper water feed lines and drain connection.',
            ]
        );

        // Completed booking 1 with Review (Fatima Zahra)
        $completedBooking1 = Booking::updateOrCreate(
            ['booking_number' => 'KHD-2026-004'],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'service_id' => $service4->id,
                'service_title' => $service4->title,
                'category' => 'Plumbing',
                'status' => 'completed',
                'total_price' => 150.00,
                'scheduled_at' => now()->subDays(2)->setHour(16)->setMinute(0),
                'completed_at' => now()->subDays(2)->setHour(17)->setMinute(15),
                'address' => '45 Boulevard d\'Anfa, Appt 12',
                'city' => 'Casablanca',
                'client_notes' => 'Blocked main drain in balcony area.',
                'provider_notes' => 'Unclogged using high pressure rotary snake. Flow restored 100%.',
            ]
        );

        Review::updateOrCreate(
            ['booking_id' => $completedBooking1->id],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'rating' => 5,
                'comment' => 'Youssef arrived promptly within 30 minutes and resolved our blocked pipe cleanly. Super professional and polite!',
            ]
        );

        // Completed booking 2 with Review (Amina Bensouda)
        $completedBooking2 = Booking::updateOrCreate(
            ['booking_number' => 'KHD-2026-005'],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'service_id' => $service1->id,
                'service_title' => $service1->title,
                'category' => 'Plumbing',
                'status' => 'completed',
                'total_price' => 220.00,
                'scheduled_at' => now()->subDays(5)->setHour(11)->setMinute(0),
                'completed_at' => now()->subDays(5)->setHour(13)->setMinute(0),
                'address' => 'Rue Jean Jaurès, Maarif',
                'city' => 'Casablanca',
                'client_notes' => 'Valve leaking near water meter.',
                'provider_notes' => 'Replaced main stopcock valve and added PTFE seal.',
            ]
        );

        Review::updateOrCreate(
            ['booking_id' => $completedBooking2->id],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'rating' => 5,
                'comment' => 'Excellent workmanship. Repaired the main stopcock valve cleanly with zero mess. Fair pricing and transparent.',
            ]
        );
    }
}
