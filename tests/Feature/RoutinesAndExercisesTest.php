<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Routine;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\ExerciseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutinesAndExercisesTest extends TestCase
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
            'fitness_goal' => 'hypertrophy',
        ]);
    }

    public function test_exercise_seeder_populates_over_100_exercises(): void
    {
        $this->seed(ExerciseSeeder::class);
        $this->assertGreaterThanOrEqual(100, Exercise::count());
    }

    public function test_can_search_and_filter_exercises(): void
    {
        $this->seed(ExerciseSeeder::class);

        // Search bench
        $response = $this->actingAs($this->user)->get('/exercises?search=Bench');
        $response->assertStatus(200);
        $response->assertSee('Barbell Bench Press');

        // Filter muscle back
        $responseBack = $this->actingAs($this->user)->get('/exercises?muscle=back');
        $responseBack->assertStatus(200);
        $responseBack->assertSee('Barbell Deadlift');
    }

    public function test_user_can_create_custom_exercise(): void
    {
        $response = $this->actingAs($this->user)->post('/exercises', [
            'name' => 'Pendlay Seal Row',
            'primary_muscle' => 'back',
            'equipment' => 'barbell',
            'category' => 'strength',
            'instructions' => 'Tengkurap di bangku tinggi, tarik barbel dari bawah tanpa momentum tubuh.',
        ]);

        $response->assertRedirect('/exercises');
        $this->assertDatabaseHas('exercises', [
            'user_id' => $this->user->id,
            'name' => 'Pendlay Seal Row',
        ]);
    }

    public function test_user_can_create_and_manage_routines(): void
    {
        $ex1 = Exercise::create(['name' => 'Bench', 'slug' => 'bench', 'primary_muscle' => 'chest', 'equipment' => 'barbell', 'category' => 'strength']);
        $ex2 = Exercise::create(['name' => 'Incline', 'slug' => 'incline', 'primary_muscle' => 'chest', 'equipment' => 'dumbbell', 'category' => 'strength']);

        // Create
        $response = $this->actingAs($this->user)->post('/routines', [
            'name' => 'Chest Hypertrophy Blast',
            'description' => 'Fokus penebalan dada.',
            'color_tag' => '#0077B6',
            'exercises' => [
                ['exercise_id' => $ex1->id, 'target_sets' => 4, 'target_reps' => 8, 'rest_seconds' => 90],
                ['exercise_id' => $ex2->id, 'target_sets' => 3, 'target_reps' => 10, 'rest_seconds' => 60],
            ]
        ]);

        $response->assertRedirect('/routines');
        $routine = Routine::where('user_id', $this->user->id)->first();
        $this->assertNotNull($routine);
        $this->assertCount(2, $routine->routineExercises);

        // Delete
        $delResponse = $this->actingAs($this->user)->delete("/routines/{$routine->id}");
        $delResponse->assertRedirect('/routines');
        $this->assertDatabaseMissing('routines', ['id' => $routine->id]);
    }
}
