<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::post('/workouts/sync', [SyncController::class, 'sync'])->name('api.workouts.sync');
Route::get('/exercises/quick-search', [SyncController::class, 'searchExercises'])->name('api.exercises.quick-search');
