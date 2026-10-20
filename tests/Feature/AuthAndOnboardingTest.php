<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Gerak');
        $response->assertSee('Masuk ke GerakIn');
    }

    public function test_user_can_register_and_is_redirected_to_onboarding(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti Aminah',
            'username' => 'sitifit',
            'email' => 'siti@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'siti@example.com']);
    }

    public function test_user_without_onboarding_cannot_access_dashboard(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/onboarding');
    }

    public function test_user_can_complete_onboarding(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/onboarding', [
            'gender' => 'female',
            'age' => 28,
            'height_cm' => 162,
            'current_weight_kg' => 64.5,
            'target_weight_kg' => 58.0,
            'fitness_goal' => 'lose_fat',
            'weekly_target_days' => 4,
            'preferred_sports' => ['running', 'gym'],
            'preferred_unit' => 'kg',
            'default_rest_seconds' => 60,
        ]);

        $response->assertRedirect('/dashboard');
        $user->refresh();
        $this->assertNotNull($user->onboarding_completed_at);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'fitness_goal' => 'lose_fat',
            'weekly_target_days' => 4,
        ]);
        $this->assertDatabaseHas('body_logs', [
            'user_id' => $user->id,
            'weight_kg' => 64.5,
        ]);
    }

    public function test_quick_demo_login(): void
    {
        $response = $this->get('/demo-login');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_registered_user_can_login_with_email(): void
    {
        // 1. Register
        $this->post('/register', [
            'name' => 'Bambang Sudirman',
            'username' => 'bambangs',
            'email' => 'bambang@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        // Complete onboarding
        $user = User::where('email', 'bambang@example.com')->first();
        $user->update(['onboarding_completed_at' => now()]);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 2. Login with email
        $response = $this->post('/login', [
            'email' => 'bambang@example.com',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_registered_user_can_login_with_username(): void
    {
        // 1. Register
        $this->post('/register', [
            'name' => 'Rina Wijaya',
            'username' => 'rinawijaya',
            'email' => 'rina@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $user = User::where('email', 'rina@example.com')->first();
        $user->update(['onboarding_completed_at' => now()]);

        $this->post('/logout');
        $this->assertGuest();

        // 2. Login using username in the email/username field
        $response = $this->post('/login', [
            'email' => 'rinawijaya',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_normalizes_case_and_whitespace(): void
    {
        $this->post('/register', [
            'name' => 'Dewi Sartika',
            'username' => 'dewifit',
            'email' => 'dewi@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $user = User::where('email', 'dewi@example.com')->first();
        $user->update(['onboarding_completed_at' => now()]);

        $this->post('/logout');
        $this->assertGuest();

        // Test with uppercase and whitespace
        $response = $this->post('/login', [
            'email' => '  DEWI@EXAMPLE.COM  ',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->post('/register', [
            'name' => 'Agus Prayitno',
            'username' => 'agusp',
            'email' => 'agus@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $this->post('/logout');
        $this->assertGuest();

        $response = $this->post('/login', [
            'email' => 'agus@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
