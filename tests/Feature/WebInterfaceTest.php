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
}
