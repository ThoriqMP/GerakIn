<?php

namespace Tests\Feature;

use App\Models\Run;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RunTrackerTest extends TestCase
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
            'gender' => 'male',
            'height_cm' => 175,
            'current_weight_kg' => 70,
            'target_weight_kg' => 68,
            'fitness_goal' => 'hypertrophy',
            'weekly_target_days' => 4,
            'default_rest_seconds' => 90,
        ]);
    }

    public function test_user_can_view_running_index(): void
    {
        Run::create([
            'user_id' => $this->user->id,
            'title' => 'Lari Pagi Test',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'duration_seconds' => 1800,
            'distance_km' => 5.25,
            'avg_pace_seconds' => 342,
            'best_pace_seconds' => 310,
            'calories_burned' => 380,
            'route_coordinates' => [[-6.218571, 106.801849, 1729410000000]],
            'km_splits' => [['km' => 1, 'duration_seconds' => 340, 'pace_seconds' => 340]],
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('runs.index'));

        $response->assertStatus(200);
        $response->assertSee('Lari Pagi Test');
        $response->assertSee('5.25');
    }

    public function test_user_can_view_running_tracker_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('runs.track'));

        $response->assertStatus(200);
        $response->assertSee('Mulai Lari');
        $response->assertSee('Mode Simulasi Demo');
        $response->assertSee('Pace Saat Ini');
    }

    public function test_user_can_store_completed_running_session(): void
    {
        $payload = [
            'title' => 'Lari Sore Senayan',
            'started_at' => now()->subMinutes(30)->toIso8601String(),
            'ended_at' => now()->toIso8601String(),
            'duration_seconds' => 1800,
            'distance_km' => 4.50,
            'avg_pace_seconds' => 400,
            'best_pace_seconds' => 360,
            'calories_burned' => 326,
            'route_coordinates' => json_encode([
                [-6.218571, 106.801849, 1729410000000],
                [-6.218320, 106.802315, 1729410001000]
            ]),
            'km_splits' => json_encode([
                ['km' => 1, 'duration_seconds' => 400, 'pace_seconds' => 400]
            ]),
            'notes' => 'Pemanasan dan tempo stabil.',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('runs.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('runs', [
            'user_id' => $this->user->id,
            'title' => 'Lari Sore Senayan',
            'duration_seconds' => 1800,
            'is_completed' => true,
        ]);
    }

    public function test_user_can_view_run_details(): void
    {
        $run = Run::create([
            'user_id' => $this->user->id,
            'title' => 'Lari Malam GBK',
            'started_at' => now()->subMinutes(45),
            'ended_at' => now(),
            'duration_seconds' => 2700,
            'distance_km' => 6.00,
            'avg_pace_seconds' => 450,
            'best_pace_seconds' => 410,
            'calories_burned' => 435,
            'route_coordinates' => [
                [-6.218571, 106.801849, 1729410000000],
                [-6.217220, 106.803740, 1729410005000]
            ],
            'km_splits' => [
                ['km' => 1, 'duration_seconds' => 450, 'pace_seconds' => 450]
            ],
            'notes' => 'Lari santai sore hari.',
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('runs.show', $run));

        $response->assertStatus(200);
        $response->assertSee('Lari Malam GBK');
        $response->assertSee('6.00');
        $response->assertSee('Lari santai sore hari.');
    }

    public function test_user_cannot_view_or_delete_another_users_run(): void
    {
        $otherUser = User::factory()->create([
            'onboarding_completed_at' => now(),
        ]);

        $otherRun = Run::create([
            'user_id' => $otherUser->id,
            'title' => 'Aktivitas Orang Lain',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'duration_seconds' => 1200,
            'distance_km' => 2.5,
            'avg_pace_seconds' => 480,
            'calories_burned' => 180,
            'is_completed' => true,
        ]);

        // Attempting to view should abort 403
        $response = $this->actingAs($this->user)->get(route('runs.show', $otherRun));
        $response->assertStatus(403);

        // Attempting to delete should abort 403
        $deleteResponse = $this->actingAs($this->user)->delete(route('runs.destroy', $otherRun));
        $deleteResponse->assertStatus(403);

        $this->assertDatabaseHas('runs', ['id' => $otherRun->id]);
    }

    public function test_user_can_delete_their_own_run(): void
    {
        $run = Run::create([
            'user_id' => $this->user->id,
            'title' => 'Lari Untuk Dihapus',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'duration_seconds' => 900,
            'distance_km' => 2.0,
            'avg_pace_seconds' => 450,
            'calories_burned' => 140,
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('runs.destroy', $run));

        $response->assertRedirect(route('runs.index'));
        $this->assertDatabaseMissing('runs', ['id' => $run->id]);
    }
}
