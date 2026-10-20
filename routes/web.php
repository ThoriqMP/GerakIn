<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    try {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Root redirect check: ' . $e->getMessage());
        return redirect()->route('login');
    }
    return redirect()->route('login');
});

// PWA Offline Fallback Page
Route::view('/offline', 'offline')->name('offline');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Quick demo helpers for review & testing
    Route::get('/demo-login', [AuthController::class, 'quickDemoLogin'])->name('demo.login');
    Route::get('/demo-new-user', [AuthController::class, 'freshDemoUser'])->name('demo.new-user');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get')->middleware('auth');

// Onboarding routes (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// Authenticated & Onboarded Application Routes
Route::middleware(['auth', 'onboarded'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Workouts
    Route::prefix('workouts')->name('workouts.')->group(function () {
        Route::match(['get', 'post'], '/start', [WorkoutController::class, 'start'])->name('start');
        Route::post('/copy', [WorkoutController::class, 'copyWorkout'])->name('copy');
        Route::get('/history', [WorkoutController::class, 'history'])->name('history');
        Route::get('/{session}/active', [WorkoutController::class, 'active'])->name('active');
        Route::post('/{session}/start-timer', [WorkoutController::class, 'startTimer'])->name('start-timer');
        Route::post('/{session}/add-exercise', [WorkoutController::class, 'addExercise'])->name('add-exercise');
        Route::post('/{session}/add-set', [WorkoutController::class, 'addSet'])->name('add-set');
        Route::post('/{session}/finish', [WorkoutController::class, 'finish'])->name('finish');
        Route::get('/{session}/summary', [WorkoutController::class, 'summary'])->name('summary');
        Route::post('/{session}/discard', [WorkoutController::class, 'discard'])->name('discard');
        Route::get('/{session}', [WorkoutController::class, 'show'])->name('show');
    });

    // Workout Sets inline operations
    Route::match(['post', 'patch'], '/workout-sets/{set}', [WorkoutController::class, 'updateSet'])->name('workout-sets.update');
    Route::delete('/workout-sets/{set}', [WorkoutController::class, 'deleteSet'])->name('workout-sets.delete');
    Route::get('/workout-sets/{set}/delete', [WorkoutController::class, 'deleteSet'])->name('workout-sets.delete.get');

    // Routines CRUD
    Route::resource('routines', RoutineController::class);

    // Exercise Library
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::get('/exercises/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');

    // Analytics & Progress
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/analytics/body-log', [AnalyticsController::class, 'storeBodyLog'])->name('analytics.body-log.store');

    // Strava-like GPS Running Tracker & History
    Route::prefix('runs')->name('runs.')->group(function () {
        Route::get('/', [RunController::class, 'index'])->name('index');
        Route::get('/track', [RunController::class, 'tracker'])->name('track');
        Route::post('/', [RunController::class, 'store'])->name('store');
        Route::get('/{run}', [RunController::class, 'show'])->name('show');
        Route::delete('/{run}', [RunController::class, 'destroy'])->name('destroy');
    });

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/export/json', [ProfileController::class, 'exportJson'])->name('profile.export.json');
});
