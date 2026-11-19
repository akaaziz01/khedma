<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebInterfaceTest extends TestCase
{
    public function test_welcome_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('KHEDMA');
        $response->assertSee('I Need a Service');
        $response->assertSee('I Offer Services');
        $response->assertSee('bootstrap-icons.min.css');
        $response->assertSee('toggleTheme()');
    }

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Join Khedma');
        $response->assertSee('I am a Client');
        $response->assertSee('I am a Provider');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
        $response->assertSee('Quick-Fill Demo Accounts');
    }

    public function test_dashboard_page_is_accessible(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_admin_dashboard_has_sidebar_and_no_header_menu(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        
        // Assert dedicated Admin sidebar exists
        $response->assertSee('id="adminSidebar"', false);
        $response->assertSee('Admin Operations Console');
        $response->assertSee('Users & Moderation', false);
        $response->assertSee('Platform Bookings');
        $response->assertSee('Services Catalog');
        $response->assertSee('bootstrap-icons.min.css');
        $response->assertSee('toggleTheme()');
        $response->assertSee('/admin/users');
        $response->assertSee('/admin/bookings');
        $response->assertSee('/admin/services');

        // Assert consumer top header menu is NOT present in admin view
        $response->assertDontSee('Become a Provider');
        $response->assertDontSee('id="navDashboardItem"', false);
    }

    public function test_admin_users_page_is_accessible(): void
    {
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('userSearchInput');
        $response->assertSee('userDetailsModal');
        $response->assertSee('deleteUserModal');
        $response->assertSee('toggleUserVerification');
        $response->assertSee('Users & Moderation', false);
    }

    public function test_admin_bookings_page_is_accessible(): void
    {
        $response = $this->get('/admin/bookings');
        $response->assertStatus(200);
        $response->assertSee('metricTotalBookings');
        $response->assertSee('bookingsTableBody');
        $response->assertSee('Platform Bookings');
    }

    public function test_admin_services_page_is_accessible(): void
    {
        $response = $this->get('/admin/services');
        $response->assertStatus(200);
        $response->assertSee('categoryModal');
        $response->assertSee('rejectModal');
        $response->assertSee('categoriesTableBody');
        $response->assertSee('Services Catalog');
    }

    public function test_admin_route_redirects_to_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/dashboard');
    }
}
