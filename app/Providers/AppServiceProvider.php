<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        if (app()->runningInConsole()) {
            // Skip DB checks during build/composer install
            return;
        }
        View::composer('*', function ($view) {
        $pendingVerifications = Verification::with('shelter')
                                            ->where('status', 'pending')
                                            ->latest()
                                            ->take(5)
                                            ->get();

        $view->with([
            'pendingCount' => $pendingVerifications->count(),
            'pendingVerifications' => $pendingVerifications,
        ]);
    });

    // Share site_name and contact_email with all views, only if settings table exists
    if (\Schema::hasTable('settings')) {
        $siteName = Setting::where('key', 'site_name')->value('value') ?? 'PawMatch';
        $contactEmail = Setting::where('key', 'contact_email')->value('value') ?? 'support@pawmatch.com';
        view()->share('site_name', $siteName);
        view()->share('contact_email', $contactEmail);

        // Global helper for settings
        if (!function_exists('get_setting')) {
            function get_setting($key, $default = null) {
                return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
            }
        }
    }
}
}
