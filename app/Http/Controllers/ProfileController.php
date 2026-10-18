<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('profile.index', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'age' => 'required|integer|min:12|max:100',
            'gender' => 'required|in:male,female,other',
            'height_cm' => 'required|numeric|min:100|max:250',
            'current_weight_kg' => 'required|numeric|min:30|max:300',
            'target_weight_kg' => 'required|numeric|min:30|max:300',
            'fitness_goal' => 'required|in:lose_fat,hypertrophy,endurance,stay_active',
            'weekly_target_days' => 'required|integer|min:1|max:7',
            'preferred_sports' => 'nullable|array',
            'preferred_unit' => 'required|in:kg,lbs',
            'default_rest_seconds' => 'required|integer|in:45,60,90,120,180',
            'theme' => 'required|in:dark,light',
            'accent_badge' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => Str::lower($validated['username']),
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'height_cm' => $validated['height_cm'],
                'current_weight_kg' => $validated['current_weight_kg'],
                'target_weight_kg' => $validated['target_weight_kg'],
                'fitness_goal' => $validated['fitness_goal'],
                'weekly_target_days' => $validated['weekly_target_days'],
                'preferred_sports' => $validated['preferred_sports'] ?? [],
                'preferred_unit' => $validated['preferred_unit'],
                'default_rest_seconds' => $validated['default_rest_seconds'],
                'theme' => $validated['theme'],
                'accent_badge' => $validated['accent_badge'],
            ]
        );

        return redirect()->route('profile.index')->with('success', 'Pengaturan profil berhasil diperbarui!');
    }

    /**
     * Export all user data as JSON
     */
    public function exportJson()
    {
        $user = Auth::user();
        $user->load([
            'profile',
            'routines.routineExercises.exercise',
            'workoutSessions.sets.exercise',
            'bodyLogs',
            'badges'
        ]);

        $data = [
            'app' => 'GerakIn - Workout & Fitness Tracker',
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'profile' => $user->profile,
            ],
            'routines' => $user->routines,
            'workout_sessions' => $user->workoutSessions,
            'body_logs' => $user->bodyLogs,
            'badges' => $user->badges,
        ];

        $filename = 'gerakin_backup_' . Str::slug($user->name) . '_' . date('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
