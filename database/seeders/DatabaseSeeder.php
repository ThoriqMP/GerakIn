<?php

namespace Database\Seeders;

use App\Models\BodyLog;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed all 100+ exercises
        $this->call(ExerciseSeeder::class);

        // 2. Create Demo User: Budi (Persona from PRD)
        $user = User::firstOrCreate(
            ['email' => 'budi@gerakin.app'],
            [
                'name' => 'Budi Prakoso',
                'username' => 'budiprakoso',
                'password' => Hash::make('password123'),
                'onboarding_completed_at' => Carbon::now()->subMonths(1),
            ]
        );

        // 3. User Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'gender' => 'male',
                'birth_date' => '1999-05-14',
                'age' => 25,
                'height_cm' => 175,
                'current_weight_kg' => 74.5,
                'target_weight_kg' => 72.0,
                'fitness_goal' => 'hypertrophy',
                'weekly_target_days' => 4,
                'preferred_sports' => ['gym', 'running'],
                'preferred_unit' => 'kg',
                'default_rest_seconds' => 90,
                'theme' => 'dark',
                'accent_badge' => 'lightning',
            ]
        );

        // 4. Create Standard Workout Routines for Budi
        $bench = Exercise::where('name', 'Barbell Bench Press')->first();
        $inclineDb = Exercise::where('name', 'Incline Dumbbell Press')->first();
        $ohp = Exercise::where('name', 'Overhead Barbell Press (OHP)')->first();
        $latRaise = Exercise::where('name', 'Dumbbell Lateral Raise')->first();
        $ropePush = Exercise::where('name', 'Tricep Rope Pushdown')->first();

        $deadlift = Exercise::where('name', 'Barbell Deadlift')->first();
        $barRow = Exercise::where('name', 'Conventional Barbell Row')->first();
        $latPull = Exercise::where('name', 'Lat Pulldown (Wide Grip)')->first();
        $dbCurl = Exercise::where('name', 'Dumbbell Alternating Curl')->first();

        $squat = Exercise::where('name', 'Barbell Back Squat')->first();
        $rdl = Exercise::where('name', 'Romanian Deadlift (RDL)')->first();
        $legPress = Exercise::where('name', 'Leg Press')->first();
        $calf = Exercise::where('name', 'Standing Calf Raise')->first();
        $plank = Exercise::where('name', 'Plank')->first();
        $running = Exercise::where('name', 'Outdoor Running')->first();

        // Push Day Routine
        $pushRoutine = Routine::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Push Day (Dada, Bahu, Trisep)'],
            [
                'description' => 'Fokus kekuatan dan hipertrofi dada, bahu depan-samping, dan trisep.',
                'color_tag' => '#0077B6',
                'sort_order' => 1,
            ]
        );

        if ($pushRoutine->routineExercises()->count() === 0) {
            $pushEx = [$bench, $inclineDb, $ohp, $latRaise, $ropePush];
            foreach ($pushEx as $idx => $ex) {
                if ($ex) {
                    RoutineExercise::create([
                        'routine_id' => $pushRoutine->id,
                        'exercise_id' => $ex->id,
                        'target_sets' => 3,
                        'target_reps' => 10,
                        'rest_seconds' => 90,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }

        // Pull Day Routine
        $pullRoutine = Routine::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Pull Day (Punggung & Bisep)'],
            [
                'description' => 'Membangun punggung tebal V-taper dan kekuatan lengan tarikan.',
                'color_tag' => '#00B4D8',
                'sort_order' => 2,
            ]
        );

        if ($pullRoutine->routineExercises()->count() === 0) {
            $pullEx = [$deadlift, $barRow, $latPull, $dbCurl];
            foreach ($pullEx as $idx => $ex) {
                if ($ex) {
                    RoutineExercise::create([
                        'routine_id' => $pullRoutine->id,
                        'exercise_id' => $ex->id,
                        'target_sets' => 3,
                        'target_reps' => 8,
                        'rest_seconds' => 120,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }

        // Leg Day Routine
        $legRoutine = Routine::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Leg Day (Paha & Betis)'],
            [
                'description' => 'Latihan kaki intensif melatih quadriceps, hamstring, dan betis.',
                'color_tag' => '#FB8500',
                'sort_order' => 3,
            ]
        );

        if ($legRoutine->routineExercises()->count() === 0) {
            $legEx = [$squat, $rdl, $legPress, $calf];
            foreach ($legEx as $idx => $ex) {
                if ($ex) {
                    RoutineExercise::create([
                        'routine_id' => $legRoutine->id,
                        'exercise_id' => $ex->id,
                        'target_sets' => 4,
                        'target_reps' => 10,
                        'rest_seconds' => 90,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }

        // 5. Seed historical completed sessions in past 2 weeks
        // Session 1: 5 days ago (Push Day)
        $session1 = WorkoutSession::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Push Day Blast', 'started_at' => Carbon::now()->subDays(5)->setTime(17, 30)],
            [
                'routine_id' => $pushRoutine->id,
                'ended_at' => Carbon::now()->subDays(5)->setTime(18, 35),
                'duration_seconds' => 3900,
                'total_volume_kg' => 4820,
                'notes' => 'Sesi bench press sangat bertenaga, PR naik!',
                'mood_rating' => 5,
                'is_completed' => true,
            ]
        );

        if ($bench && $session1->sets()->count() === 0) {
            WorkoutSet::create(['workout_session_id' => $session1->id, 'exercise_id' => $bench->id, 'set_number' => 1, 'set_type' => 'warmup', 'weight_kg' => 50, 'reps' => 12, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session1->id, 'exercise_id' => $bench->id, 'set_number' => 2, 'set_type' => 'normal', 'weight_kg' => 70, 'reps' => 10, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session1->id, 'exercise_id' => $bench->id, 'set_number' => 3, 'set_type' => 'normal', 'weight_kg' => 80, 'reps' => 8, 'is_pr' => true, 'is_completed' => true]);
        }
        if ($inclineDb && $session1->sets()->where('exercise_id', $inclineDb->id)->count() === 0) {
            WorkoutSet::create(['workout_session_id' => $session1->id, 'exercise_id' => $inclineDb->id, 'set_number' => 1, 'set_type' => 'normal', 'weight_kg' => 24, 'reps' => 10, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session1->id, 'exercise_id' => $inclineDb->id, 'set_number' => 2, 'set_type' => 'normal', 'weight_kg' => 26, 'reps' => 8, 'is_completed' => true]);
        }

        // Session 2: 3 days ago (Pull Day)
        $session2 = WorkoutSession::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Heavy Pull Day', 'started_at' => Carbon::now()->subDays(3)->setTime(18, 00)],
            [
                'routine_id' => $pullRoutine->id,
                'ended_at' => Carbon::now()->subDays(3)->setTime(19, 10),
                'duration_seconds' => 4200,
                'total_volume_kg' => 5450,
                'notes' => 'Deadlift terkontrol, lat pump luar biasa.',
                'mood_rating' => 4,
                'is_completed' => true,
            ]
        );

        if ($deadlift && $session2->sets()->count() === 0) {
            WorkoutSet::create(['workout_session_id' => $session2->id, 'exercise_id' => $deadlift->id, 'set_number' => 1, 'set_type' => 'warmup', 'weight_kg' => 60, 'reps' => 10, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session2->id, 'exercise_id' => $deadlift->id, 'set_number' => 2, 'set_type' => 'normal', 'weight_kg' => 100, 'reps' => 6, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session2->id, 'exercise_id' => $deadlift->id, 'set_number' => 3, 'set_type' => 'normal', 'weight_kg' => 120, 'reps' => 5, 'is_pr' => true, 'is_completed' => true]);
        }

        // Session 3: 1 day ago (Leg Day)
        $session3 = WorkoutSession::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Leg Day Quads Crusher', 'started_at' => Carbon::now()->subDays(1)->setTime(17, 00)],
            [
                'routine_id' => $legRoutine->id,
                'ended_at' => Carbon::now()->subDays(1)->setTime(18, 15),
                'duration_seconds' => 4500,
                'total_volume_kg' => 6200,
                'notes' => 'Squat tembus 100 kg dengan teknik bersih.',
                'mood_rating' => 5,
                'is_completed' => true,
            ]
        );

        if ($squat && $session3->sets()->count() === 0) {
            WorkoutSet::create(['workout_session_id' => $session3->id, 'exercise_id' => $squat->id, 'set_number' => 1, 'set_type' => 'warmup', 'weight_kg' => 60, 'reps' => 10, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session3->id, 'exercise_id' => $squat->id, 'set_number' => 2, 'set_type' => 'normal', 'weight_kg' => 85, 'reps' => 8, 'is_completed' => true]);
            WorkoutSet::create(['workout_session_id' => $session3->id, 'exercise_id' => $squat->id, 'set_number' => 3, 'set_type' => 'normal', 'weight_kg' => 100, 'reps' => 6, 'is_pr' => true, 'is_completed' => true]);
        }

        // 6. Seed Body Logs for Budi (last 4 weeks)
        $bodyLogWeights = [76.2, 75.6, 75.0, 74.5];
        foreach ($bodyLogWeights as $i => $weight) {
            BodyLog::firstOrCreate(
                ['user_id' => $user->id, 'logged_at' => Carbon::now()->subWeeks(3 - $i)->format('Y-m-d')],
                [
                    'weight_kg' => $weight,
                    'body_fat_percentage' => 18.0 - ($i * 0.4),
                    'notes' => 'Penimbangan pagi hari sebelum sarapan.',
                ]
            );
        }

        // 7. Check & Award Badges
        $user->checkAndAwardBadges();
    }
}
