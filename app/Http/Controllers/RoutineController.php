<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutineController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $routines = $user->routines()->with('routineExercises.exercise')->get();
        return view('routines.index', compact('routines'));
    }

    public function create()
    {
        $exercises = Exercise::orderBy('primary_muscle')->orderBy('name')->get();
        return view('routines.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color_tag' => 'required|string|max:20',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.target_sets' => 'required|integer|min:1|max:15',
            'exercises.*.target_reps' => 'required|integer|min:1|max:100',
            'exercises.*.rest_seconds' => 'required|integer|min:15|max:600',
        ]);

        $routine = Routine::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color_tag' => $validated['color_tag'],
            'sort_order' => (Auth::user()->routines()->max('sort_order') ?? 0) + 1,
        ]);

        foreach ($validated['exercises'] as $index => $item) {
            RoutineExercise::create([
                'routine_id' => $routine->id,
                'exercise_id' => $item['exercise_id'],
                'target_sets' => $item['target_sets'],
                'target_reps' => $item['target_reps'],
                'rest_seconds' => $item['rest_seconds'],
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('routines.index')->with('success', "Routine '{$routine->name}' berhasil dibuat!");
    }

    public function show(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $routine->load('routineExercises.exercise');
        return view('routines.show', compact('routine'));
    }

    public function edit(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $routine->load('routineExercises.exercise');
        $exercises = Exercise::orderBy('primary_muscle')->orderBy('name')->get();

        return view('routines.edit', compact('routine', 'exercises'));
    }

    public function update(Routine $routine, Request $request)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color_tag' => 'required|string|max:20',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.target_sets' => 'required|integer|min:1|max:15',
            'exercises.*.target_reps' => 'required|integer|min:1|max:100',
            'exercises.*.rest_seconds' => 'required|integer|min:15|max:600',
        ]);

        $routine->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color_tag' => $validated['color_tag'],
        ]);

        // Recreate exercises
        $routine->routineExercises()->delete();
        foreach ($validated['exercises'] as $index => $item) {
            RoutineExercise::create([
                'routine_id' => $routine->id,
                'exercise_id' => $item['exercise_id'],
                'target_sets' => $item['target_sets'],
                'target_reps' => $item['target_reps'],
                'rest_seconds' => $item['rest_seconds'],
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('routines.show', $routine->id)->with('success', "Routine '{$routine->name}' berhasil diperbarui!");
    }

    public function destroy(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $name = $routine->name;
        $routine->delete();

        return redirect()->route('routines.index')->with('info', "Routine '{$name}' telah dihapus.");
    }
}
