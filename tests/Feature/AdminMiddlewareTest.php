<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_filament_admin_panel()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_admin_users_can_access_filament_admin_panel()
    {
        // Mock the config to include our test admin email
        config(['auth.admins' => ['admin@example.com']]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_guests_are_redirected_to_login()
    {
        $this->get('/admin')
            ->assertStatus(302)
            ->assertRedirect(route('filament.admin.auth.login'));
    }
}
