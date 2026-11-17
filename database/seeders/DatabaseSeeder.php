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
     * Seed the application's database with authentic Tunisian data.
     */
    public function run(): void
    {
        // 1. Clients (Tunisia)
        $client1 = User::updateOrCreate(
            ['email' => 'client@khedma.tn'],
            [
                'name' => 'Mariem Ben Salem',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+216 20 112 233',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client1->clientProfile()->updateOrCreate(
            ['user_id' => $client1->id],
            [
                'city' => 'Tunis',
                'address' => 'Résidence Les Pins, Les Berges du Lac 2',
            ]
        );

        // Also keep client@khedma.ma as alias for existing tests/convenience
        $clientAlias = User::updateOrCreate(
            ['email' => 'client@khedma.ma'],
            [
                'name' => 'Mariem Ben Salem',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+216 20 112 233',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $clientAlias->clientProfile()->updateOrCreate(
            ['user_id' => $clientAlias->id],
            [
                'city' => 'Tunis',
                'address' => 'Résidence Les Pins, Les Berges du Lac 2',
            ]
        );

        $client2 = User::updateOrCreate(
            ['email' => 'amina.trabelsi@example.tn'],
            [
                'name' => 'Amina Trabelsi',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+216 22 334 455',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client2->clientProfile()->updateOrCreate(
            ['user_id' => $client2->id],
            [
                'city' => 'Sousse',
                'address' => 'Boulevard du 14 Janvier, Kantaoui',
            ]
        );

        $client3 = User::updateOrCreate(
            ['email' => 'karim.mejri@example.tn'],
            [
                'name' => 'Karim Mejri',
                'password' => 'Password123!',
                'role' => UserRole::CLIENT,
                'phone' => '+216 98 445 566',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $client3->clientProfile()->updateOrCreate(
            ['user_id' => $client3->id],
            [
                'city' => 'Sfax',
                'address' => 'Route de Téniour Km 2, Sfax',
            ]
        );

        // 2. Main Provider (Tunis / Ariana)
        $provider = User::updateOrCreate(
            ['email' => 'provider@khedma.tn'],
            [
                'name' => 'Youssef El Amrani',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+216 98 998 877',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $provider->providerProfile()->updateOrCreate(
            ['user_id' => $provider->id],
            [
                'business_name' => 'Amrani Plomberie & Chauffage Grand Tunis',
                'category' => 'Plumbing',
                'bio' => 'Plombier chauffagiste certifié avec plus de 10 ans d\'expérience sur le Grand Tunis (Tunis, Ariana, Ben Arous, La Marsa). Détection de fuite acoustique, dépannage chauffe-eau, et rénovation sanitaire.',
                'skills' => ['Recherche de fuite', 'Installation Chauffe-eau', 'Rénovation Sanitaire', 'Débouchage haute pression', 'Tuyauterie Cuivre & Multicouche'],
                'hourly_rate' => 35.00,
                'city' => 'Tunis',
                'is_available' => true,
            ]
        );

        // Provider alias
        $providerAlias = User::updateOrCreate(
            ['email' => 'provider@khedma.ma'],
            [
                'name' => 'Youssef El Amrani',
                'password' => 'Password123!',
                'role' => UserRole::PROVIDER,
                'phone' => '+216 98 998 877',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $providerAlias->providerProfile()->updateOrCreate(
            ['user_id' => $providerAlias->id],
            [
                'business_name' => 'Amrani Plomberie & Chauffage Grand Tunis',
                'category' => 'Plumbing',
                'bio' => 'Plombier chauffagiste certifié avec plus de 10 ans d\'expérience sur le Grand Tunis.',
                'skills' => ['Recherche de fuite', 'Installation Chauffe-eau', 'Rénovation Sanitaire'],
                'hourly_rate' => 35.00,
                'city' => 'Tunis',
                'is_available' => true,
            ]
        );

        // 3. Admin user
        User::updateOrCreate(
            ['email' => 'admin@khedma.tn'],
            [
                'name' => 'Khedma Tunisie Admin',
                'password' => 'AdminPass123!',
                'role' => UserRole::ADMIN,
                'phone' => '+216 71 000 000',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['email' => 'admin@khedma.ma'],
            [
                'name' => 'Khedma Tunisie Admin',
                'password' => 'AdminPass123!',
                'role' => UserRole::ADMIN,
                'phone' => '+216 71 000 000',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 4. Tunisian Services Catalog (Prices in TND)
        $service1 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Diagnostic & Réparation Fuite d\'Eau Urgente'],
            [
                'category' => 'Plumbing',
                'description' => 'Détection acoustique et caméra thermique de fuites encastrées dans les murs et dalles, avec réparation immédiate.',
                'price' => 45.00,
                'duration_hours' => 1.5,
                'is_active' => true,
            ]
        );

        $service2 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Installation & Détartrage Chauffe-Eau (Électrique / Gaz)'],
            [
                'category' => 'Plumbing',
                'description' => 'Fixation murale sécurisée, raccordement gaz/électricité, groupe de sécurité et test de pression.',
                'price' => 80.00,
                'duration_hours' => 2.5,
                'is_active' => true,
            ]
        );

        $service3 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Pose Sanitaire & Robinetterie Salle de Bain'],
            [
                'category' => 'Plumbing',
                'description' => 'Installation meuble vasque, WC suspendu ou standard, colonne de douche et robinetterie moderne.',
                'price' => 120.00,
                'duration_hours' => 4.0,
                'is_active' => true,
            ]
        );

        $service4 = Service::updateOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Débouchage Canalisation & Égout Haute Pression'],
            [
                'category' => 'Plumbing',
                'description' => 'Passage de furet mécanique et débouchage hydrocureur pour éviers, siphons et conduites principales.',
                'price' => 50.00,
                'duration_hours' => 1.0,
                'is_active' => true,
            ]
        );

        // 5. Tunisian Bookings
        Booking::updateOrCreate(
            ['booking_number' => 'KHD-TN-001'],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'service_id' => $service1->id,
                'service_title' => $service1->title,
                'category' => 'Plumbing',
                'status' => 'pending',
                'total_price' => 45.00,
                'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
                'address' => 'Résidence Les Pins, Les Berges du Lac 2',
                'city' => 'Tunis',
                'client_notes' => 'Fuite sous l\'évier de la cuisine qui s\'infiltre sous le parquet. Besoin d\'une intervention rapide.',
            ]
        );

        Booking::updateOrCreate(
            ['booking_number' => 'KHD-TN-002'],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'service_id' => $service2->id,
                'service_title' => $service2->title,
                'category' => 'Plumbing',
                'status' => 'accepted',
                'total_price' => 80.00,
                'scheduled_at' => now()->addDays(2)->setHour(14)->setMinute(30),
                'address' => 'Boulevard du 14 Janvier, Kantaoui',
                'city' => 'Sousse',
                'client_notes' => 'Nouveau chauffe-eau électrique 80L acheté, à installer dans la salle d\'eau.',
                'provider_notes' => 'Rendez-vous confirmé. Matériel de fixation fourni.',
            ]
        );

        Booking::updateOrCreate(
            ['booking_number' => 'KHD-TN-003'],
            [
                'client_id' => $client3->id,
                'provider_id' => $provider->id,
                'service_id' => $service3->id,
                'service_title' => $service3->title,
                'category' => 'Plumbing',
                'status' => 'in_progress',
                'total_price' => 120.00,
                'scheduled_at' => now()->subHours(2),
                'address' => 'Route de Téniour Km 2',
                'city' => 'Sfax',
                'client_notes' => 'Remplacement robinetterie et installation douche à l\'italienne.',
                'provider_notes' => 'Travaux de raccordement en cours.',
            ]
        );

        $completedBooking1 = Booking::updateOrCreate(
            ['booking_number' => 'KHD-TN-004'],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'service_id' => $service4->id,
                'service_title' => $service4->title,
                'category' => 'Plumbing',
                'status' => 'completed',
                'total_price' => 50.00,
                'scheduled_at' => now()->subDays(2)->setHour(16)->setMinute(0),
                'completed_at' => now()->subDays(2)->setHour(17)->setMinute(15),
                'address' => 'Résidence Les Pins, Les Berges du Lac 2',
                'city' => 'Tunis',
                'client_notes' => 'Canalisation bouchée balcon.',
                'provider_notes' => 'Débouchage réussi au furet rotatif.',
            ]
        );

        Review::updateOrCreate(
            ['booking_id' => $completedBooking1->id],
            [
                'client_id' => $client1->id,
                'provider_id' => $provider->id,
                'rating' => 5,
                'comment' => 'Si Youssef est intervenu très rapidement à Ennasr / Lac 2. Travail propre, efficace et ponctuel. Yaatik essaha !',
            ]
        );

        $completedBooking2 = Booking::updateOrCreate(
            ['booking_number' => 'KHD-TN-005'],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'service_id' => $service1->id,
                'service_title' => $service1->title,
                'category' => 'Plumbing',
                'status' => 'completed',
                'total_price' => 60.00,
                'scheduled_at' => now()->subDays(5)->setHour(11)->setMinute(0),
                'completed_at' => now()->subDays(5)->setHour(13)->setMinute(0),
                'address' => 'Boulevard du 14 Janvier, Kantaoui',
                'city' => 'Sousse',
                'client_notes' => 'Vanne d\'arrêt principale qui fuit.',
                'provider_notes' => 'Remplacement vanne et joint téflon.',
            ]
        );

        Review::updateOrCreate(
            ['booking_id' => $completedBooking2->id],
            [
                'client_id' => $client2->id,
                'provider_id' => $provider->id,
                'rating' => 5,
                'comment' => 'Excellent service à Sousse. Professionnel, prix transparents en Dinars Tunisiens sans mauvaises surprises.',
            ]
        );
    }
}
