<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\Shared\Verification;
use App\Models\Setting;

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
        // Skip all database operations during console commands (build, composer install, etc.)
        if (app()->runningInConsole()) {
            return;
        }

        // Only proceed if we can connect to the database
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            // Database not available, skip all database operations
            return;
        }

        View::composer('*', function ($view) {
            try {
                $pendingVerifications = Verification::with('shelter')
                                                    ->where('status', 'pending')
                                                    ->latest()
                                                    ->take(5)
                                                    ->get();

                $view->with([
                    'pendingCount' => $pendingVerifications->count(),
                    'pendingVerifications' => $pendingVerifications,
                ]);
            } catch (\Exception $e) {
                // If database query fails, provide default values
                $view->with([
                    'pendingCount' => 0,
                    'pendingVerifications' => collect(),
                ]);
            }
        });

        // Share site_name and contact_email with all views, only if settings table exists
        try {
            if (\Schema::hasTable('settings')) {
                $siteName = Setting::where('key', 'site_name')->value('value') ?? 'PawMatch';
                $contactEmail = Setting::where('key', 'contact_email')->value('value') ?? 'support@pawmatch.com';
                view()->share('site_name', $siteName);
                view()->share('contact_email', $contactEmail);

                // Global helper for settings
                if (!function_exists('get_setting')) {
                    function get_setting($key, $default = null) {
                        try {
                            return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
                        } catch (\Exception $e) {
                            return $default;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // If settings table doesn't exist or query fails, use defaults
            view()->share('site_name', 'PawMatch');
            view()->share('contact_email', 'support@pawmatch.com');
        }
    }
}
