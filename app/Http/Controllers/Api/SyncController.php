<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    /**
     * Batch sync offline workouts
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $workouts = $request->input('workouts', []);
        $syncedSessions = [];

        foreach ($workouts as $w) {
            $clientSessionId = $w['client_session_id'] ?? null;
            if (!$clientSessionId) continue;

            $startedAt = isset($w['started_at']) ? Carbon::parse($w['started_at']) : Carbon::now();
            $endedAt = isset($w['ended_at']) ? Carbon::parse($w['ended_at']) : Carbon::now();
            $durationSeconds = $w['duration_seconds'] ?? max(60, $endedAt->diffInSeconds($startedAt));

            $session = WorkoutSession::firstOrCreate(
                ['client_session_id' => $clientSessionId],
                [
                    'user_id' => $user->id,
                    'routine_id' => $w['routine_id'] ?? null,
                    'title' => $w['title'] ?? 'Latihan Offline Tersinkronisasi',
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt,
                    'duration_seconds' => $durationSeconds,
                    'notes' => $w['notes'] ?? 'Dicatat saat mode offline',
                    'mood_rating' => $w['mood_rating'] ?? 5,
                    'is_completed' => true,
                ]
            );

            // Sync sets
            if (!empty($w['sets']) && is_array($w['sets'])) {
                foreach ($w['sets'] as $s) {
                    WorkoutSet::firstOrCreate(
                        [
                            'workout_session_id' => $session->id,
                            'exercise_id' => $s['exercise_id'],
                            'set_number' => $s['set_number'],
                        ],
                        [
                            'set_type' => $s['set_type'] ?? 'normal',
                            'weight_kg' => $s['weight_kg'] ?? 0,
                            'reps' => $s['reps'] ?? 0,
                            'distance_km' => $s['distance_km'] ?? null,
                            'duration_seconds' => $s['duration_seconds'] ?? null,
                            'rpe' => $s['rpe'] ?? null,
                            'is_completed' => true,
                        ]
                    );
                }
            }

            $session->recalculateVolume();
            $syncedSessions[] = $session->id;
        }

        // Award badges
        $user->checkAndAwardBadges();

        return response()->json([
            'success' => true,
            'message' => 'Batch sinkronisasi data offline selesai!',
            'synced_count' => count($syncedSessions),
            'synced_ids' => $syncedSessions,
        ]);
    }

    /**
     * Quick search exercises
     */
    public function searchExercises(Request $request)
    {
        $q = $request->input('q', '');
        $exercises = Exercise::where('name', 'like', "%{$q}%")
            ->orWhere('primary_muscle', 'like', "%{$q}%")
            ->take(15)
            ->get(['id', 'name', 'primary_muscle', 'equipment', 'category']);

        return response()->json($exercises);
    }
}
