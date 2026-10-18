<?php

namespace App\Http\Controllers;

use App\Models\BodyLog;
use App\Models\Exercise;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Body Weight Trend (last 12 logs)
        $bodyLogs = $user->bodyLogs()->orderBy('logged_at', 'asc')->take(12)->get();
        $weightChartLabels = $bodyLogs->pluck('logged_at')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $weightChartData = $bodyLogs->pluck('weight_kg')->toArray();

        // 2. Weekly Total Volume Tonnage (last 8 weeks)
        $weeklyVolumeLabels = [];
        $weeklyVolumeData = [];

        for ($i = 7; $i >= 0; $i--) {
            $wStart = (clone $now)->subWeeks($i)->startOfWeek();
            $wEnd = (clone $now)->subWeeks($i)->endOfWeek();

            $volume = $user->workoutSessions()
                ->where('is_completed', true)
                ->whereBetween('started_at', [$wStart, $wEnd])
                ->sum('total_volume_kg');

            $weeklyVolumeLabels[] = 'M' . (8 - $i) . ' (' . $wStart->format('d/m') . ')';
            $weeklyVolumeData[] = round($volume);
        }

        // 3. Estimated 1RM Progression for Big 3 / Big 4 lifts
        $keyExercises = ['Barbell Bench Press', 'Barbell Back Squat', 'Barbell Deadlift', 'Overhead Barbell Press (OHP)'];
        $oneRmProgression = [];

        foreach ($keyExercises as $liftName) {
            $ex = Exercise::where('name', $liftName)->first();
            if ($ex) {
                // Find all sets for this exercise
                $sets = WorkoutSet::where('exercise_id', $ex->id)
                    ->where('is_completed', true)
                    ->where('weight_kg', '>', 0)
                    ->whereHas('workoutSession', function ($q) use ($user) {
                        $q->where('user_id', $user->id)->where('is_completed', true);
                    })
                    ->with('workoutSession')
                    ->get();

                $best1rm = 0.0;
                $bestSet = null;

                foreach ($sets as $s) {
                    $e1rm = $s->estimated_one_rep_max;
                    if ($e1rm > $best1rm) {
                        $best1rm = $e1rm;
                        $bestSet = $s;
                    }
                }

                $oneRmProgression[] = [
                    'name' => $liftName,
                    'muscle' => $ex->primary_muscle,
                    'best_weight' => $bestSet ? $bestSet->weight_kg : 0,
                    'best_reps' => $bestSet ? $bestSet->reps : 0,
                    'estimated_1rm' => $best1rm,
                ];
            }
        }

        // 4. Badges list & unlock status
        $allBadgeDefinitions = [
            [
                'code' => 'first_sweat',
                'title' => 'First Sweat',
                'description' => 'Menyelesaikan sesi latihan pertamamu di GerakIn.',
                'icon' => 'zap',
            ],
            [
                'code' => 'pr_smasher',
                'title' => 'PR Smasher',
                'description' => 'Memecahkan Rekor Pribadi (Personal Record) baru.',
                'icon' => 'trending-up',
            ],
            [
                'code' => '10k_club',
                'title' => '10,000 Kg Club',
                'description' => 'Total angkatan akumulatif melampaui 10.000 kg.',
                'icon' => 'award',
            ],
            [
                'code' => 'streak_master',
                'title' => 'Streak Master',
                'description' => 'Konsisten mencapai target latihan 3 minggu beruntun.',
                'icon' => 'flame',
            ],
            [
                'code' => '50k_club',
                'title' => 'Titan Lifter (50,000 Kg)',
                'description' => 'Mencapai total volume angkatan 50.000 kg.',
                'icon' => 'shield',
            ],
        ];

        $userBadges = $user->badges()->pluck('unlocked_at', 'badge_code')->toArray();

        $badges = array_map(function ($b) use ($userBadges) {
            $isUnlocked = isset($userBadges[$b['code']]);
            return [
                'code' => $b['code'],
                'title' => $b['title'],
                'description' => $b['description'],
                'icon' => $b['icon'],
                'is_unlocked' => $isUnlocked,
                'unlocked_at' => $isUnlocked ? Carbon::parse($userBadges[$b['code']])->format('d M Y') : null,
            ];
        }, $allBadgeDefinitions);

        // 5. Total all time stats
        $allSessions = $user->workoutSessions()->where('is_completed', true)->get();
        $totalSessionsCount = $allSessions->count();
        $totalVolumeKg = $allSessions->sum('total_volume_kg');
        $totalHours = round($allSessions->sum('duration_seconds') / 3600, 1);
        $totalSetsCount = WorkoutSet::whereHas('workoutSession', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_completed', true);
        })->where('is_completed', true)->count();

        return view('analytics.index', compact(
            'user',
            'bodyLogs',
            'weightChartLabels',
            'weightChartData',
            'weeklyVolumeLabels',
            'weeklyVolumeData',
            'oneRmProgression',
            'badges',
            'totalSessionsCount',
            'totalVolumeKg',
            'totalHours',
            'totalSetsCount'
        ));
    }

    /**
     * Store new Body Weight / Body Metric log entry
     */
    public function storeBodyLog(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:20|max:300',
            'body_fat_percentage' => 'nullable|numeric|min:2|max:60',
            'logged_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        BodyLog::create([
            'user_id' => $user->id,
            'logged_at' => $validated['logged_at'],
            'weight_kg' => $validated['weight_kg'],
            'body_fat_percentage' => $validated['body_fat_percentage'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update profile current weight if date is today or newer
        if (Carbon::parse($validated['logged_at'])->isToday() || Carbon::parse($validated['logged_at'])->isFuture()) {
            $user->profile?->update(['current_weight_kg' => $validated['weight_kg']]);
        }

        return redirect()->route('analytics.index')->with('success', 'Catatan berat badan berhasil disimpan!');
    }
}
