<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production / Vercel to prevent Mixed Content
        if (app()->environment('production') || env('VERCEL') || request()->header('x-forwarded-proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Auto-run migrations & seeds on Vercel / serverless if database is fresh
        if (app()->environment('production') || env('VERCEL')) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Vercel boot auto-migration notice: ' . $e->getMessage());
            }
        }
    }
}
