<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\WorkoutSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $muscle = $request->input('muscle');
        $equipment = $request->input('equipment');

        $query = Exercise::where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', Auth::id());
        });

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($muscle && $muscle !== 'all') {
            $query->where('primary_muscle', $muscle);
        }

        if ($equipment && $equipment !== 'all') {
            $query->where('equipment', $equipment);
        }

        $exercises = $query->orderBy('primary_muscle')->orderBy('name')->paginate(20)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($exercises);
        }

        $muscles = ['all', 'chest', 'back', 'legs', 'shoulders', 'arms', 'core', 'cardio'];
        $equipments = ['all', 'barbell', 'dumbbell', 'machine', 'cable', 'bodyweight', 'cardio'];

        return view('exercises.index', compact('exercises', 'muscles', 'equipments', 'search', 'muscle', 'equipment'));
    }

    public function show(Exercise $exercise)
    {
        $user = Auth::user();

        // Exercise PR for this user
        $pr = $exercise->getPersonalRecordForUser($user->id);

        // History of sets done by this user for this exercise
        $historySets = WorkoutSet::where('exercise_id', $exercise->id)
            ->where('is_completed', true)
            ->whereHas('workoutSession', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_completed', true);
            })
            ->with('workoutSession')
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        return view('exercises.show', compact('exercise', 'pr', 'historySets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'primary_muscle' => 'required|in:chest,back,legs,shoulders,arms,core,cardio',
            'secondary_muscle' => 'nullable|string|max:50',
            'equipment' => 'required|in:barbell,dumbbell,machine,cable,bodyweight,cardio',
            'category' => 'required|in:strength,cardio',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $exercise = Exercise::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'primary_muscle' => $validated['primary_muscle'],
            'secondary_muscle' => $validated['secondary_muscle'] ?? null,
            'equipment' => $validated['equipment'],
            'category' => $validated['category'],
            'instructions' => $validated['instructions'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'exercise' => $exercise]);
        }

        return redirect()->route('exercises.index')->with('success', "Gerakan kustom '{$exercise->name}' berhasil ditambahkan!");
    }
}
