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
        // Auto-run migrations on production if database not initialized
        if (env('APP_ENV') === 'production') {
            $this->ensureMigrationsRun();
        }
    }

    /**
     * Ensure migrations have been run
     */
    private function ensureMigrationsRun(): void
    {
        try {
            // Check if users table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--force' => true,
                    '--no-interaction' => true,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Auto-migration failed: ' . $e->getMessage());
        }
    }
}
