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
        $response->assertSee('Mode Pengujian Cepat');
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
}
