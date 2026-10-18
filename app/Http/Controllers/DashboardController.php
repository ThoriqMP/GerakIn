<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $now = Carbon::now();

        // 1. Date Pill Carousel Selection (Defaults to Today)
        $selectedDateStr = $request->input('date', $now->toDateString());
        try {
            $selectedDate = Carbon::parse($selectedDateStr);
        } catch (\Exception $e) {
            $selectedDate = $now->copy();
            $selectedDateStr = $now->toDateString();
        }

        // Generate 14-day carousel: 7 days in past, today, 6 days ahead
        $datePills = [];
        for ($i = -7; $i <= 6; $i++) {
            $d = $now->copy()->addDays($i);
            $dStr = $d->toDateString();

            // Count workouts on that day
            $workoutCount = $user->workoutSessions()
                ->whereDate('started_at', $dStr)
                ->count();

            $dayIndo = match($d->dayOfWeek) {
                1 => 'Sen',
                2 => 'Sel',
                3 => 'Rab',
                4 => 'Kam',
                5 => 'Jum',
                6 => 'Sab',
                0 => 'Min',
            };

            $datePills[] = [
                'date' => $dStr,
                'day_name' => $dayIndo,
                'dayName' => $dayIndo,
                'day_number' => $d->format('j'),
                'dayNumber' => $d->format('j'),
                'month_name' => $d->isoFormat('MMM'),
                'monthName' => $d->isoFormat('MMM'),
                'is_today' => $d->isToday(),
                'isToday' => $d->isToday(),
                'is_selected' => ($dStr === $selectedDateStr),
                'isSelected' => ($dStr === $selectedDateStr),
                'has_workouts' => ($workoutCount > 0),
                'hasWorkout' => ($workoutCount > 0),
                'workout_count' => $workoutCount,
                'workoutCount' => $workoutCount,
            ];
        }

        // Workouts on the selected date
        $selectedDateWorkouts = $user->workoutSessions()
            ->whereDate('started_at', $selectedDateStr)
            ->with(['routine', 'sets.exercise'])
            ->latest('started_at')
            ->get();

        // Past workouts available for "Copy Latihan" planning feature
        $copyableWorkouts = $user->workoutSessions()
            ->where('is_completed', true)
            ->with(['routine', 'sets.exercise'])
            ->latest('started_at')
            ->take(8)
            ->get();

        // 2. Weekly Target & Streak
        $targetDays = $profile->weekly_target_days ?? 4;
        $weekStart = (clone $now)->startOfWeek();
        $weekEnd = (clone $now)->endOfWeek();

        $thisWeekSessions = $user->workoutSessions()
            ->where('is_completed', true)
            ->whereBetween('started_at', [$weekStart, $weekEnd])
            ->get();

        $completedThisWeek = $thisWeekSessions->count();
        $weeklyProgressPercent = min(100, round(($completedThisWeek / max(1, $targetDays)) * 100));
        $weeklyStreak = $user->calculateWeeklyStreak();

        // 3. Active in-progress workout session
        $activeSession = $user->workoutSessions()
            ->where('is_completed', false)
            ->latest('started_at')
            ->first();

        // 4. Muscle Heatmap calculation (Past 7 days)
        $sevenDaysAgo = (clone $now)->subDays(7);
        $recentSets = WorkoutSet::whereHas('workoutSession', function ($q) use ($user, $sevenDaysAgo) {
            $q->where('user_id', $user->id)
                ->where('is_completed', true)
                ->where('started_at', '>=', $sevenDaysAgo);
        })->with('exercise')->get();

        $muscleSetsCount = [
            'chest' => 0,
            'back' => 0,
            'shoulders' => 0,
            'arms' => 0,
            'legs' => 0,
            'core' => 0,
        ];

        foreach ($recentSets as $set) {
            $muscle = $set->exercise?->primary_muscle;
            if ($muscle && isset($muscleSetsCount[$muscle])) {
                $muscleSetsCount[$muscle]++;
            }
        }

        $muscleHeat = [];
        foreach ($muscleSetsCount as $muscle => $count) {
            if ($count === 0) {
                $muscleHeat[$muscle] = 'inactive';
            } elseif ($count <= 3) {
                $muscleHeat[$muscle] = 'low';
            } elseif ($count <= 8) {
                $muscleHeat[$muscle] = 'medium';
            } else {
                $muscleHeat[$muscle] = 'high';
            }
        }

        // 5. Lifetime & Weekly Stats
        $allCompleted = $user->workoutSessions()->where('is_completed', true)->get();
        $totalWorkoutsCount = $allCompleted->count();
        $totalVolumeKg = $allCompleted->sum('total_volume_kg');
        $thisWeekVolumeKg = $thisWeekSessions->sum('total_volume_kg');

        $totalPrCount = WorkoutSet::whereHas('workoutSession', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_completed', true);
        })->where('is_pr', true)->count();

        // Routines for quick start
        $routines = $user->routines()->with('exercises')->take(3)->get();

        // Badges
        $badges = $user->badges()->latest('unlocked_at')->take(4)->get();

        // Recent workouts
        $recentWorkouts = $user->workoutSessions()
            ->where('is_completed', true)
            ->with('sets.exercise')
            ->latest('started_at')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'profile',
            'selectedDateStr',
            'selectedDate',
            'datePills',
            'selectedDateWorkouts',
            'copyableWorkouts',
            'recentWorkouts',
            'targetDays',
            'completedThisWeek',
            'weeklyProgressPercent',
            'weeklyStreak',
            'activeSession',
            'muscleHeat',
            'muscleSetsCount',
            'totalWorkoutsCount',
            'totalVolumeKg',
            'thisWeekVolumeKg',
            'totalPrCount',
            'routines',
            'badges'
        ));
    }
}
