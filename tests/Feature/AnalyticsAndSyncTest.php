<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnalyticsAndSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'onboarding_completed_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $this->user->id,
            'current_weight_kg' => 74.0,
            'target_weight_kg' => 70.0,
            'weekly_target_days' => 4,
            'default_rest_seconds' => 90,
            'theme' => 'dark',
            'accent_badge' => 'lightning',
        ]);
    }

    public function test_analytics_page_renders_with_charts_and_stats(): void
    {
        $response = $this->actingAs($this->user)->get('/analytics');
        $response->assertStatus(200);
        $response->assertSee('Volume Latihan Mingguan');
        $response->assertSee('Tren Berat Badan');
        $response->assertSee('Estimasi One-Rep Max');
    }

    public function test_user_can_log_body_weight(): void
    {
        $response = $this->actingAs($this->user)->post('/analytics/body-log', [
            'weight_kg' => 73.8,
            'body_fat_percentage' => 16.5,
            'logged_at' => now()->toDateString(),
            'notes' => 'Timbang badan pagi hari',
        ]);

        $response->assertRedirect('/analytics');
        $this->assertDatabaseHas('body_logs', [
            'user_id' => $this->user->id,
            'weight_kg' => 73.8,
        ]);

        $this->user->profile->refresh();
        $this->assertEquals(73.8, $this->user->profile->current_weight_kg);
    }

    public function test_user_can_update_profile_and_export_data(): void
    {
        // Update profile
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Baru',
            'username' => 'budibaru',
            'age' => 26,
            'gender' => 'male',
            'height_cm' => 176,
            'current_weight_kg' => 73.5,
            'target_weight_kg' => 70.0,
            'fitness_goal' => 'hypertrophy',
            'weekly_target_days' => 5,
            'preferred_unit' => 'kg',
            'default_rest_seconds' => 120,
            'theme' => 'dark',
            'accent_badge' => 'titan',
        ]);

        $response->assertRedirect('/profile');
        $this->user->refresh();
        $this->assertEquals('Budi Baru', $this->user->name);
        $this->assertEquals('budibaru', $this->user->username);
        $this->assertEquals(120, $this->user->profile->default_rest_seconds);

        // Export data
        $exportResponse = $this->actingAs($this->user)->get('/profile/export/json');
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'application/json');
        $this->assertStringContainsString('GerakIn - Workout & Fitness Tracker', $exportResponse->getContent());
    }

    public function test_offline_workout_batch_sync_via_api(): void
    {
        $exercise = Exercise::create([
            'name' => 'Bench Press',
            'slug' => 'bench-press',
            'primary_muscle' => 'chest',
            'equipment' => 'barbell',
            'category' => 'strength',
        ]);

        $clientUuid = (string) Str::uuid();

        $payload = [
            'workouts' => [
                [
                    'client_session_id' => $clientUuid,
                    'title' => 'Sesi Offline Basement Gym',
                    'started_at' => now()->subHour()->toIso8601String(),
                    'ended_at' => now()->toIso8601String(),
                    'duration_seconds' => 3600,
                    'notes' => 'Disinkronkan otomatis setelah online',
                    'sets' => [
                        [
                            'exercise_id' => $exercise->id,
                            'set_number' => 1,
                            'weight_kg' => 75,
                            'reps' => 10,
                            'is_completed' => true,
                        ],
                        [
                            'exercise_id' => $exercise->id,
                            'set_number' => 2,
                            'weight_kg' => 80,
                            'reps' => 8,
                            'is_completed' => true,
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->postJson('/api/workouts/sync', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'synced_count' => 1,
        ]);

        $this->assertDatabaseHas('workout_sessions', [
            'client_session_id' => $clientUuid,
            'user_id' => $this->user->id,
            'total_volume_kg' => 1390, // (75*10) + (80*8) = 750 + 640 = 1390
        ]);
    }
}
