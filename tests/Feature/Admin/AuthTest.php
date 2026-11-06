<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
    }

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'password',
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_admin_cannot_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'password' => 'password',
        ]);

        $response = $this->from(route('admin.login'))
            ->post(route('admin.login.post'), [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertSessionHas('error');
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_wrong_password_rejected(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'password',
        ]);

        $response = $this->from(route('admin.login'))
            ->post(route('admin.login.post'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHas('error');
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_routes_require_auth(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
