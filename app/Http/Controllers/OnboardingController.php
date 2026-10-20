<?php

namespace App\Http\Controllers;

use App\Models\BodyLog;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?? $user->profile()->create([
            'gender' => 'male',
            'age' => 25,
            'height_cm' => 170,
            'current_weight_kg' => 70,
            'target_weight_kg' => 68,
            'fitness_goal' => 'hypertrophy',
            'weekly_target_days' => 4,
            'preferred_sports' => ['gym'],
            'preferred_unit' => 'kg',
            'default_rest_seconds' => 90,
            'theme' => 'dark',
            'accent_badge' => 'lightning',
        ]);

        return view('onboarding.wizard', compact('user', 'profile'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:12|max:100',
            'height_cm' => 'required|numeric|min:100|max:250',
            'current_weight_kg' => 'required|numeric|min:30|max:300',
            'target_weight_kg' => 'required|numeric|min:30|max:300',
            'fitness_goal' => 'required|in:lose_fat,hypertrophy,endurance,stay_active',
            'weekly_target_days' => 'required|integer|min:1|max:7',
            'preferred_sports' => 'nullable|array',
            'preferred_unit' => 'required|in:kg,lbs',
            'default_rest_seconds' => 'required|integer|in:45,60,90,120,180',
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($user, $validated) {
            // Update profile
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'gender' => $validated['gender'],
                    'age' => $validated['age'],
                    'height_cm' => $validated['height_cm'],
                    'current_weight_kg' => $validated['current_weight_kg'],
                    'target_weight_kg' => $validated['target_weight_kg'],
                    'fitness_goal' => $validated['fitness_goal'],
                    'weekly_target_days' => $validated['weekly_target_days'],
                    'preferred_sports' => $validated['preferred_sports'] ?? ['gym'],
                    'preferred_unit' => $validated['preferred_unit'],
                    'default_rest_seconds' => $validated['default_rest_seconds'],
                ]
            );

            // Mark onboarding complete
            $user->update(['onboarding_completed_at' => now()]);

            // Create initial body log
            BodyLog::firstOrCreate(
                ['user_id' => $user->id, 'logged_at' => now()->toDateString()],
                [
                    'weight_kg' => $validated['current_weight_kg'],
                    'notes' => 'Catatan berat badan awal saat onboarding GerakIn',
                ]
            );

            // If user has no routines yet, generate starter template routines based on their fitness goal
            if ($user->routines()->count() === 0) {
                $this->generateStarterRoutines($user, $validated['fitness_goal']);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Selamat! Profil olahraga GerakIn kamu telah siap. Mari mulai bergerak!');
    }

    private function generateStarterRoutines($user, string $goal): void
    {
        $now = now();

        if ($goal === 'endurance') {
            $routine = Routine::create([
                'user_id' => $user->id,
                'name' => 'Kardio & Ketahanan Stamina',
                'description' => 'Meningkatkan kapasitas VO2 Max dan stamina pembakaran lemak.',
                'color_tag' => '#00B4D8',
                'sort_order' => 1,
            ]);

            $exercises = Exercise::whereIn('name', ['Outdoor Running', 'Jump Rope (Skipping)', 'Plank', 'Mountain Climbers'])->get();
            $batch = [];
            foreach ($exercises as $i => $ex) {
                $batch[] = [
                    'routine_id' => $routine->id,
                    'exercise_id' => $ex->id,
                    'target_sets' => 3,
                    'target_reps' => 15,
                    'rest_seconds' => 60,
                    'sort_order' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($batch)) {
                RoutineExercise::insert($batch);
            }
        } else {
            // Default Gym / Hypertrophy / Fat Loss / Stay Active
            $routineA = Routine::create([
                'user_id' => $user->id,
                'name' => 'Upper Body Power (Dada & Punggung)',
                'description' => 'Membangun kekuatan dan otot tubuh bagian atas.',
                'color_tag' => '#0077B6',
                'sort_order' => 1,
            ]);

            $routineB = Routine::create([
                'user_id' => $user->id,
                'name' => 'Lower Body & Core (Kaki & Perut)',
                'description' => 'Pondasi otot kaki yang kokoh dan stabilitas core.',
                'color_tag' => '#FB8500',
                'sort_order' => 2,
            ]);

            $namesA = ['Barbell Bench Press', 'Conventional Barbell Row', 'Incline Dumbbell Press', 'Lat Pulldown (Wide Grip)'];
            $namesB = ['Barbell Back Squat', 'Romanian Deadlift (RDL)', 'Standing Calf Raise', 'Hanging Leg Raise'];

            $allExercises = Exercise::whereIn('name', array_merge($namesA, $namesB))->get()->keyBy('name');

            $allBatch = [];
            foreach ($namesA as $i => $name) {
                if (isset($allExercises[$name])) {
                    $allBatch[] = [
                        'routine_id' => $routineA->id,
                        'exercise_id' => $allExercises[$name]->id,
                        'target_sets' => 3,
                        'target_reps' => 10,
                        'rest_seconds' => 90,
                        'sort_order' => $i + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach ($namesB as $i => $name) {
                if (isset($allExercises[$name])) {
                    $allBatch[] = [
                        'routine_id' => $routineB->id,
                        'exercise_id' => $allExercises[$name]->id,
                        'target_sets' => 3,
                        'target_reps' => 10,
                        'rest_seconds' => 90,
                        'sort_order' => $i + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($allBatch)) {
                RoutineExercise::insert($allBatch);
            }
        }
    }
}
