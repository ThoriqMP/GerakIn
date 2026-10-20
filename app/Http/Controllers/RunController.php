<?php

namespace App\Http\Controllers;

use App\Models\Run;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RunController extends Controller
{
    /**
     * Display running history, stats, and records
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $completedRunsQuery = $user->runs()->where('is_completed', true);

        // Calculate running statistics
        $totalDistance = (float) $completedRunsQuery->sum('distance_km');
        $totalRuns = $completedRunsQuery->count();
        $totalDurationSeconds = (int) $completedRunsQuery->sum('duration_seconds');
        $totalCalories = (int) $completedRunsQuery->sum('calories_burned');

        // Weekly distance
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $thisWeekDistance = (float) $user->runs()
            ->where('is_completed', true)
            ->whereBetween('started_at', [$startOfWeek, $endOfWeek])
            ->sum('distance_km');

        // Average pace calculation
        $overallAvgPaceSeconds = 0;
        if ($totalDistance > 0 && $totalDurationSeconds > 0) {
            $overallAvgPaceSeconds = (int) round($totalDurationSeconds / $totalDistance);
        }

        // Records
        $longestRun = (float) ($completedRunsQuery->max('distance_km') ?? 0);
        $fastestPaceSeconds = $user->runs()
            ->where('is_completed', true)
            ->where('distance_km', '>=', 1.0)
            ->where('avg_pace_seconds', '>', 0)
            ->min('avg_pace_seconds');

        // Paginated list
        $runs = $user->runs()
            ->where('is_completed', true)
            ->paginate(10);

        return view('runs.index', compact(
            'runs',
            'totalDistance',
            'totalRuns',
            'totalDurationSeconds',
            'totalCalories',
            'thisWeekDistance',
            'overallAvgPaceSeconds',
            'longestRun',
            'fastestPaceSeconds'
        ));
    }

    /**
     * Show live Strava-like GPS running tracker
     */
    public function tracker()
    {
        $user = Auth::user();
        $userWeightKg = $user->profile?->current_weight_kg ?? 70.0;

        return view('runs.tracker', compact('user', 'userWeightKg'));
    }

    /**
     * Store completed running session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date',
            'duration_seconds' => 'required|integer|min:1',
            'distance_km' => 'required|numeric|min:0',
            'avg_pace_seconds' => 'nullable|integer',
            'best_pace_seconds' => 'nullable|integer',
            'calories_burned' => 'nullable|integer',
            'route_coordinates' => 'nullable',
            'km_splits' => 'nullable',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Process coordinates array
        $routeCoords = $validated['route_coordinates'] ?? [];
        if (is_string($routeCoords)) {
            $routeCoords = json_decode($routeCoords, true) ?: [];
        }

        // Process km splits array
        $kmSplits = $validated['km_splits'] ?? [];
        if (is_string($kmSplits)) {
            $kmSplits = json_decode($kmSplits, true) ?: [];
        }

        // Generate friendly default title if none provided
        $startedAt = Carbon::parse($validated['started_at']);
        $title = trim($validated['title'] ?? '');
        if (empty($title)) {
            $hour = $startedAt->hour;
            if ($hour >= 4 && $hour < 11) {
                $timeOfDay = 'Lari Pagi';
            } elseif ($hour >= 11 && $hour < 15) {
                $timeOfDay = 'Lari Siang';
            } elseif ($hour >= 15 && $hour < 18) {
                $timeOfDay = 'Lari Sore';
            } else {
                $timeOfDay = 'Lari Malam';
            }
            $title = $timeOfDay . ' ' . $startedAt->translatedFormat('d M');
        }

        // Estimate calories if zero based on weight and distance
        $calories = (int) ($validated['calories_burned'] ?? 0);
        if ($calories <= 0 && $validated['distance_km'] > 0) {
            $weight = $user->profile?->current_weight_kg ?? 70.0;
            // Standard running calorie estimation: ~ 1.036 kcal per kg per km
            $calories = (int) round($weight * $validated['distance_km'] * 1.036);
        }

        $run = $user->runs()->create([
            'title' => $title,
            'started_at' => $startedAt,
            'ended_at' => $validated['ended_at'] ? Carbon::parse($validated['ended_at']) : Carbon::now(),
            'duration_seconds' => (int) $validated['duration_seconds'],
            'distance_km' => (float) $validated['distance_km'],
            'avg_pace_seconds' => (int) ($validated['avg_pace_seconds'] ?? 0),
            'best_pace_seconds' => !empty($validated['best_pace_seconds']) ? (int) $validated['best_pace_seconds'] : null,
            'calories_burned' => $calories,
            'route_coordinates' => $routeCoords,
            'km_splits' => $kmSplits,
            'notes' => $validated['notes'] ?? null,
            'is_completed' => true,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'run_id' => $run->id,
                'redirect' => route('runs.show', $run),
                'message' => 'Sesi lari berhasil disimpan!',
            ]);
        }

        return redirect()->route('runs.show', $run)->with('success', 'Sesi lari berhasil disimpan!');
    }

    /**
     * Display a specific run activity summary and replay map
     */
    public function show(Run $run)
    {
        if ($run->user_id !== Auth::id()) {
            abort(403);
        }

        return view('runs.show', compact('run'));
    }

    /**
     * Delete a run record
     */
    public function destroy(Run $run)
    {
        if ($run->user_id !== Auth::id()) {
            abort(403);
        }

        $run->delete();

        return redirect()->route('runs.index')->with('success', 'Catatan sesi lari telah dihapus.');
    }
}
