<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutLoggerTest extends TestCase
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
            'current_weight_kg' => 75,
            'target_weight_kg' => 70,
            'fitness_goal' => 'hypertrophy',
            'weekly_target_days' => 4,
            'default_rest_seconds' => 90,
        ]);
    }

    public function test_can_start_empty_workout(): void
    {
        $response = $this->actingAs($this->user)->get('/workouts/start');
        $session = WorkoutSession::where('user_id', $this->user->id)->first();

        $this->assertNotNull($session);
        $this->assertFalse($session->is_completed);
        $response->assertRedirect("/workouts/{$session->id}/active");
    }

    public function test_can_start_workout_from_routine(): void
    {
        $exercise = Exercise::create([
            'name' => 'Barbell Bench Press',
            'slug' => 'barbell-bench-press',
            'primary_muscle' => 'chest',
            'equipment' => 'barbell',
            'category' => 'strength',
        ]);

        $routine = Routine::create([
            'user_id' => $this->user->id,
            'name' => 'Push Day',
            'color_tag' => '#0077B6',
        ]);

        RoutineExercise::create([
            'routine_id' => $routine->id,
            'exercise_id' => $exercise->id,
            'target_sets' => 3,
            'target_reps' => 10,
            'rest_seconds' => 90,
        ]);

        $response = $this->actingAs($this->user)->post('/workouts/start', [
            'routine_id' => $routine->id,
        ]);

        $session = WorkoutSession::where('user_id', $this->user->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals($routine->id, $session->routine_id);
        $this->assertCount(3, $session->sets);
    }

    public function test_can_add_exercise_and_set_to_active_workout(): void
    {
        $session = WorkoutSession::create([
            'user_id' => $this->user->id,
            'title' => 'Test Sesi',
            'started_at' => now(),
            'is_completed' => false,
        ]);

        $exercise = Exercise::create([
            'name' => 'Barbell Squat',
            'slug' => 'barbell-squat',
            'primary_muscle' => 'legs',
            'equipment' => 'barbell',
            'category' => 'strength',
        ]);

        // Add exercise
        $this->actingAs($this->user)->post("/workouts/{$session->id}/add-exercise", [
            'exercise_id' => $exercise->id,
        ]);

        $this->assertEquals(3, $session->sets()->count());

        // Add 1 more set
        $this->actingAs($this->user)->post("/workouts/{$session->id}/add-set", [
            'exercise_id' => $exercise->id,
            'weight_kg' => 80,
            'reps' => 8,
        ]);

        $this->assertEquals(4, $session->sets()->count());
    }

    public function test_can_complete_set_and_trigger_pr(): void
    {
        $session = WorkoutSession::create([
            'user_id' => $this->user->id,
            'title' => 'Test Sesi',
            'started_at' => now(),
            'is_completed' => false,
        ]);

        $exercise = Exercise::create([
            'name' => 'Deadlift',
            'slug' => 'deadlift',
            'primary_muscle' => 'back',
            'equipment' => 'barbell',
            'category' => 'strength',
        ]);

        $set = WorkoutSet::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'set_type' => 'normal',
            'weight_kg' => 100,
            'reps' => 5,
            'is_completed' => false,
        ]);

        // Complete the set via JSON update
        $response = $this->actingAs($this->user)->postJson("/workout-sets/{$set->id}", [
            'weight_kg' => 120,
            'reps' => 5,
            'is_completed' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_pr' => true]);

        $set->refresh();
        $this->assertTrue($set->is_completed);
        $this->assertTrue($set->is_pr);
        $this->assertEquals(120, $set->weight_kg);
    }

    public function test_can_finish_workout_and_view_summary_card(): void
    {
        $session = WorkoutSession::create([
            'user_id' => $this->user->id,
            'title' => 'Chest Day Selesai',
            'started_at' => now()->subMinutes(50),
            'is_completed' => false,
        ]);

        $exercise = Exercise::create([
            'name' => 'Incline Dumbbell Press',
            'slug' => 'incline-dumbbell-press',
            'primary_muscle' => 'chest',
            'equipment' => 'dumbbell',
            'category' => 'strength',
        ]);

        WorkoutSet::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight_kg' => 24,
            'reps' => 10,
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->post("/workouts/{$session->id}/finish", [
            'notes' => 'Pump sangat terasa!',
            'mood_rating' => 5,
        ]);

        $response->assertRedirect("/workouts/{$session->id}/summary");

        $session->refresh();
        $this->assertTrue($session->is_completed);
        $this->assertEquals(240, $session->total_volume_kg);

        // Check summary view renders
        $summaryResponse = $this->actingAs($this->user)->get("/workouts/{$session->id}/summary");
        $summaryResponse->assertStatus(200);
        $summaryResponse->assertSee('Kartu ringkasan');
        $summaryResponse->assertSee('shareableCard');
    }

    public function test_can_copy_workout_from_previous_date(): void
    {
        $pastDate = now()->subDays(3);
        $pastSession = WorkoutSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi Masa Lalu',
            'started_at' => $pastDate,
            'completed_at' => $pastDate->copy()->addMinutes(45),
            'duration_seconds' => 2700,
            'total_volume_kg' => 1500,
            'is_completed' => true,
        ]);

        $exercise = Exercise::create([
            'name' => 'Barbell Curl',
            'slug' => 'barbell-curl',
            'primary_muscle' => 'arms',
            'equipment' => 'barbell',
            'category' => 'strength',
        ]);

        WorkoutSet::create([
            'workout_session_id' => $pastSession->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight_kg' => 30,
            'reps' => 10,
            'is_completed' => true,
        ]);

        $targetDate = now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->user)->post('/workouts/copy', [
            'source_workout_id' => $pastSession->id,
            'target_date' => $targetDate,
        ]);

        $response->assertRedirect("/dashboard?date={$targetDate}");

        $copiedSession = WorkoutSession::where('user_id', $this->user->id)
            ->whereDate('started_at', $targetDate)
            ->first();

        $this->assertNotNull($copiedSession);
        $this->assertStringContainsString('Sesi Masa Lalu', $copiedSession->title);
        $this->assertCount(1, $copiedSession->sets);
        $this->assertEquals(30, $copiedSession->sets->first()->weight_kg);
        $this->assertFalse($copiedSession->sets->first()->is_completed);
    }

    public function test_workout_starts_in_planning_mode_and_timer_can_be_started(): void
    {
        $response = $this->actingAs($this->user)->get('/workouts/start');
        $session = WorkoutSession::where('user_id', $this->user->id)->first();

        $this->assertNotNull($session);
        $this->assertNull($session->timer_started_at);
        $this->assertFalse($session->isTimerActive());

        // Start timer via route
        $timerResponse = $this->actingAs($this->user)->postJson("/workouts/{$session->id}/start-timer");
        $timerResponse->assertStatus(200);
        $timerResponse->assertJson(['success' => true]);

        $session->refresh();
        $this->assertNotNull($session->timer_started_at);
        $this->assertTrue($session->isTimerActive());
    }

    public function test_home_workout_and_push_up_exercises_are_available(): void
    {
        $this->seed(\Database\Seeders\ExerciseSeeder::class);

        $this->assertGreaterThanOrEqual(8, Exercise::where('name', 'like', '%Push Up%')->count());
        $this->assertGreaterThanOrEqual(50, Exercise::where('equipment', 'bodyweight')->count());

        $this->assertNotNull(Exercise::where('name', 'like', '%Knee Push Up%')->first());
        $this->assertNotNull(Exercise::where('name', 'like', '%Diamond Push Up%')->first());
        $this->assertNotNull(Exercise::where('name', 'like', '%Bodyweight Air Squat%')->first());
        $this->assertNotNull(Exercise::where('name', 'like', '%Wall Sit%')->first());
    }

    public function test_bodyweight_exercise_defaults_to_zero_kg_and_tracks_rep_pr(): void
    {
        $pushUp = Exercise::create([
            'name' => 'Push Up',
            'slug' => 'push-up',
            'primary_muscle' => 'chest',
            'equipment' => 'bodyweight',
            'category' => 'strength',
        ]);

        $this->assertTrue($pushUp->isBodyweight());
        $this->assertEquals(0.0, $pushUp->defaultStartingWeight());

        $session = WorkoutSession::create([
            'user_id' => $this->user->id,
            'title' => 'Calisthenics Session',
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Add Push Up exercise to active session
        $this->actingAs($this->user)->post("/workouts/{$session->id}/add-exercise", [
            'exercise_id' => $pushUp->id,
        ]);

        $sets = WorkoutSet::where('workout_session_id', $session->id)->get();
        $this->assertCount(3, $sets);
        // All default sets should have 0 kg for bodyweight exercises!
        foreach ($sets as $set) {
            $this->assertEquals(0.0, $set->weight_kg);
        }

        // Complete set 1 with 15 reps, weight 0
        $firstSet = $sets->first();
        $updateResponse = $this->actingAs($this->user)->postJson("/workout-sets/{$firstSet->id}", [
            'weight_kg' => 0,
            'reps' => 15,
            'is_completed' => true,
        ]);

        $updateResponse->assertStatus(200);
        $firstSet->refresh();
        $this->assertTrue($firstSet->is_pr);
    }
}

