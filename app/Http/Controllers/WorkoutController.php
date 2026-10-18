<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Routine;
use App\Models\WorkoutSession;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    /**
     * Start a new workout session (or continue active)
     */
    public function start(Request $request)
    {
        $user = Auth::user();

        // Check if there's already an active session
        $existing = $user->workoutSessions()->where('is_completed', false)->latest('started_at')->first();
        if ($existing && !$request->has('force_new')) {
            return redirect()->route('workouts.active', $existing->id);
        }

        $routineId = $request->input('routine_id');
        $routine = $routineId ? Routine::with('routineExercises.exercise')->find($routineId) : null;

        $title = $routine ? $routine->name : 'Sesi Latihan Mandiri ' . Carbon::now()->isoFormat('D MMMM');

        $session = WorkoutSession::create([
            'user_id' => $user->id,
            'routine_id' => $routine?->id,
            'title' => $title,
            'started_at' => Carbon::now(),
            'timer_started_at' => null,
            'is_completed' => false,
            'client_session_id' => (string) Str::uuid(),
        ]);

        // Pre-populate sets if started from a routine
        if ($routine && $routine->routineExercises->isNotEmpty()) {
            foreach ($routine->routineExercises as $re) {
                // Get previous reference for this exercise
                $prevRecord = $re->exercise->getPersonalRecordForUser($user->id);
                $prevWeight = $prevRecord ? $prevRecord->weight_kg : $re->exercise->defaultStartingWeight();

                for ($s = 1; $s <= $re->target_sets; $s++) {
                    WorkoutSet::create([
                        'workout_session_id' => $session->id,
                        'exercise_id' => $re->exercise_id,
                        'set_number' => $s,
                        'set_type' => 'normal',
                        'weight_kg' => $prevWeight,
                        'reps' => $re->target_reps ?: 10,
                        'is_completed' => false,
                    ]);
                }
            }
        }

        return redirect()->route('workouts.active', $session->id);
    }

    /**
     * Active workout screen
     */
    public function active(WorkoutSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load(['sets.exercise', 'routine']);
        $user = Auth::user();
        $defaultRest = $user->profile?->default_rest_seconds ?? 90;

        // Group sets by exercise
        $groupedSets = $session->sets->groupBy('exercise_id');

        // Look up previous exercise reference data and PRs for each exercise
        $exerciseReferences = [];
        foreach ($groupedSets as $exerciseId => $sets) {
            $exercise = Exercise::find($exerciseId);
            if ($exercise) {
                $exerciseReferences[$exerciseId] = [
                    'exercise' => $exercise,
                    'pr' => $exercise->getPersonalRecordForUser($user->id),
                    'previous_sets' => $exercise->getPreviousSetsForUser($user->id),
                ];
            }
        }

        // Available exercises for adding more exercises
        $allExercises = Exercise::orderBy('primary_muscle')->orderBy('name')->get();

        return view('workouts.active', compact(
            'session',
            'user',
            'groupedSets',
            'exerciseReferences',
            'allExercises',
            'defaultRest'
        ));
    }

    /**
     * Start the workout timer (transitions from planning mode to active training)
     */
    public function startTimer(WorkoutSession $session, Request $request)
    {
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$session->timer_started_at) {
            $session->update([
                'timer_started_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'timer_started_at' => $session->timer_started_at->toIso8601String(),
            'message' => 'Latihan resmi dimulai!',
        ]);
    }

    /**
     * Add an exercise to active session
     */
    public function addExercise(WorkoutSession $session, Request $request)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $exerciseId = $request->validate([
            'exercise_id' => 'required|exists:exercises,id'
        ])['exercise_id'];

        $exercise = Exercise::findOrFail($exerciseId);
        $prevRecord = $exercise->getPersonalRecordForUser(Auth::id());
        $prevWeight = $prevRecord ? $prevRecord->weight_kg : $exercise->defaultStartingWeight();

        // Add 3 default sets
        for ($s = 1; $s <= 3; $s++) {
            WorkoutSet::create([
                'workout_session_id' => $session->id,
                'exercise_id' => $exerciseId,
                'set_number' => $s,
                'set_type' => 'normal',
                'weight_kg' => $prevWeight,
                'reps' => 10,
                'is_completed' => false,
            ]);
        }

        return redirect()->route('workouts.active', $session->id)->with('success', "Gerakan {$exercise->name} ditambahkan!");
    }

    /**
     * Add a single set to an exercise in active session
     */
    public function addSet(WorkoutSession $session, Request $request)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'weight_kg' => 'nullable|numeric|min:0',
            'reps' => 'nullable|integer|min:0',
        ]);

        $exercise = Exercise::findOrFail($validated['exercise_id']);

        $lastSet = WorkoutSet::where('workout_session_id', $session->id)
            ->where('exercise_id', $validated['exercise_id'])
            ->orderBy('set_number', 'desc')
            ->first();

        $setNumber = $lastSet ? $lastSet->set_number + 1 : 1;
        $weight = $validated['weight_kg'] ?? ($lastSet ? $lastSet->weight_kg : $exercise->defaultStartingWeight());
        $reps = $validated['reps'] ?? ($lastSet ? $lastSet->reps : 10);

        WorkoutSet::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $validated['exercise_id'],
            'set_number' => $setNumber,
            'set_type' => 'normal',
            'weight_kg' => $weight,
            'reps' => $reps,
            'is_completed' => false,
        ]);

        return redirect()->route('workouts.active', $session->id);
    }

    /**
     * Update/complete a specific set (AJAX / Form)
     */
    public function updateSet(WorkoutSet $set, Request $request)
    {
        if ($set->workoutSession->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'weight_kg' => 'nullable|numeric|min:0',
            'reps' => 'nullable|integer|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'rpe' => 'nullable|integer|min:1|max:10',
            'set_type' => 'nullable|in:warmup,normal,drop,failure',
            'is_completed' => 'nullable|boolean',
        ]);

        $set->update($validated);

        // Auto-start timer if a set was completed and timer hasn't officially started
        if ($set->is_completed && !$set->workoutSession->timer_started_at) {
            $set->workoutSession->update(['timer_started_at' => Carbon::now()]);
        }

        // Check for Personal Record (PR)
        $isPr = false;
        if ($set->is_completed) {
            if ($set->weight_kg > 0) {
                $prevBest = WorkoutSet::where('exercise_id', $set->exercise_id)
                    ->where('id', '!=', $set->id)
                    ->where('is_completed', true)
                    ->whereHas('workoutSession', function ($q) {
                        $q->where('user_id', Auth::id())->where('is_completed', true);
                    })
                    ->max('weight_kg');

                if (!$prevBest || $set->weight_kg > $prevBest) {
                    $isPr = true;
                    $set->update(['is_pr' => true]);
                }
            } else {
                // Bodyweight (0 kg): PR is achieved by breaking max repetitions
                $prevBestReps = WorkoutSet::where('exercise_id', $set->exercise_id)
                    ->where('id', '!=', $set->id)
                    ->where('is_completed', true)
                    ->where('weight_kg', '<=', 0)
                    ->whereHas('workoutSession', function ($q) {
                        $q->where('user_id', Auth::id())->where('is_completed', true);
                    })
                    ->max('reps');

                if ($set->reps > 0 && (!$prevBestReps || $set->reps > $prevBestReps)) {
                    $isPr = true;
                    $set->update(['is_pr' => true]);
                }
            }
        }

        // Recalculate session volume
        $set->workoutSession->recalculateVolume();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'set' => $set,
                'is_pr' => $isPr,
                'total_volume' => $set->workoutSession->total_volume_kg,
                'timer_started_at' => $set->workoutSession->timer_started_at?->toIso8601String()
            ]);
        }

        return redirect()->route('workouts.active', $set->workout_session_id);
    }

    /**
     * Delete a set
     */
    public function deleteSet(WorkoutSet $set)
    {
        $sessionId = $set->workout_session_id;
        if ($set->workoutSession->user_id !== Auth::id()) {
            abort(403);
        }

        $exerciseId = $set->exercise_id;
        $set->delete();

        // Renumber remaining sets for this exercise
        $remainingSets = WorkoutSet::where('workout_session_id', $sessionId)
            ->where('exercise_id', $exerciseId)
            ->orderBy('id')
            ->get();

        foreach ($remainingSets as $index => $s) {
            $s->update(['set_number' => $index + 1]);
        }

        return redirect()->route('workouts.active', $sessionId)->with('info', 'Set berhasil dihapus.');
    }

    /**
     * Finish and finalize active workout
     */
    public function finish(WorkoutSession $session, Request $request)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'mood_rating' => 'nullable|integer|min:1|max:5',
            'duration_seconds' => 'nullable|integer|min:0',
        ]);

        $now = Carbon::now();
        $durationSeconds = $validated['duration_seconds'] ?? null;
        if (!$durationSeconds || $durationSeconds <= 0) {
            $startTime = $session->timer_started_at ?? $session->started_at;
            $durationSeconds = max(60, $now->diffInSeconds($startTime));
        }

        $session->update([
            'ended_at' => $now,
            'duration_seconds' => $durationSeconds,
            'notes' => $validated['notes'] ?? null,
            'mood_rating' => $validated['mood_rating'] ?? 5,
            'is_completed' => true,
        ]);

        // Recalculate total tonnage volume
        $session->recalculateVolume();

        // Gamification: Award any newly achieved badges
        $awarded = Auth::user()->checkAndAwardBadges();

        $toastMsg = 'Latihan selesai! Kerja kerasmu terbayar.';
        if (!empty($awarded)) {
            $toastMsg .= ' Kamu membuka lencana baru: ' . implode(', ', $awarded) . '!';
        }

        return redirect()->route('workouts.summary', $session->id)->with('success', $toastMsg);
    }

    /**
     * Summary Card Screen (FR-DSH-04) - Shareable Workout Card
     */
    public function summary(WorkoutSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load(['sets.exercise', 'routine', 'user.profile']);
        $musclesTrained = $session->getMusclesTrained();
        $prSets = $session->sets()->where('is_pr', true)->with('exercise')->get();

        return view('workouts.summary', compact('session', 'musclesTrained', 'prSets'));
    }

    /**
     * Workout History List
     */
    public function history()
    {
        $user = Auth::user();
        $workouts = $user->workoutSessions()
            ->where('is_completed', true)
            ->with(['routine', 'sets.exercise'])
            ->latest('started_at')
            ->paginate(10);

        return view('workouts.history', compact('workouts'));
    }

    /**
     * Show single past workout details
     */
    public function show(WorkoutSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load(['sets.exercise', 'routine']);
        $groupedSets = $session->sets->groupBy('exercise_id');

        return view('workouts.show', compact('session', 'groupedSets'));
    }

    /**
     * Discard an uncompleted workout
     */
    public function discard(WorkoutSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->delete();
        return redirect()->route('dashboard')->with('info', 'Sesi latihan dibatalkan.');
    }

    /**
     * Copy a previous workout to a selected date for planning (Finding #005)
     */
    public function copyWorkout(Request $request)
    {
        $sourceId = $request->input('source_workout_id') ?? $request->input('source_session_id');
        $validated = $request->validate([
            'source_workout_id' => 'nullable|exists:workout_sessions,id',
            'source_session_id' => 'nullable|exists:workout_sessions,id',
            'target_date' => 'required|date',
        ]);

        if (!$sourceId) {
            return back()->with('error', 'Sesi latihan sumber tidak dipilih.');
        }

        $source = WorkoutSession::with('sets')->findOrFail($sourceId);
        if ($source->user_id !== Auth::id()) {
            abort(403);
        }

        $targetDate = Carbon::parse($validated['target_date'])->setTime(now()->hour, now()->minute);

        $newSession = WorkoutSession::create([
            'user_id' => Auth::id(),
            'routine_id' => $source->routine_id,
            'title' => $source->title,
            'started_at' => $targetDate,
            'is_completed' => false,
            'client_session_id' => (string) Str::uuid(),
            'notes' => 'Disalin dari latihan ' . $source->started_at->format('d/m/Y'),
        ]);

        foreach ($source->sets as $s) {
            WorkoutSet::create([
                'workout_session_id' => $newSession->id,
                'exercise_id' => $s->exercise_id,
                'set_number' => $s->set_number,
                'set_type' => $s->set_type,
                'weight_kg' => $s->weight_kg,
                'reps' => $s->reps,
                'distance_km' => $s->distance_km,
                'duration_seconds' => $s->duration_seconds,
                'rpe' => $s->rpe,
                'is_completed' => false,
            ]);
        }

        return redirect()->route('dashboard', ['date' => $validated['target_date']])
            ->with('success', "Latihan '{$source->title}' berhasil disalin ke tanggal " . $targetDate->format('d M Y') . '!');
    }
}
