<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@esdep.mt.gov.br',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'email' => 'user@esdep.mt.gov.br',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/reservations');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/calendar');
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_routes(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/reservations');
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get('/admin/calendar');
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin/reservations');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/calendar');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_can_access_dashboard(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
