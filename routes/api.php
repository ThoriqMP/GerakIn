<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::post('/workouts/sync', [SyncController::class, 'sync'])->name('api.workouts.sync');
Route::get('/exercises/quick-search', [SyncController::class, 'searchExercises'])->name('api.exercises.quick-search');

Route::get('/app-version', function () {
    return response()->json([
        'app_name' => 'GerakIn',
        'package_name' => 'com.gerakin.app',
        'latest_version' => '1.0.0',
        'version_code' => 1,
        'min_supported_version' => '1.0.0',
        'force_update' => false,
        'direct_apk_url' => 'https://gerak-in-seven.vercel.app/downloads/gerakin-app.apk',
        'github_apk_url' => 'https://github.com/ThoriqMP/GerakIn-Mobile/releases/download/v1.0.0/app-release.apk',
        'github_releases_url' => 'https://github.com/ThoriqMP/GerakIn-Mobile/releases',
        'release_notes' => "Rilis perdana GerakIn Mobile untuk Android.\n- Pelacak lari GPS ala Strava\n- Pencatat latihan gym & beban\n- Muscle heatmap anatomi tubuh interaktif\n- Offline mode & timer istirahat otomatis",
        'published_at' => '2026-10-21',
    ]);
})->name('api.app.version');

