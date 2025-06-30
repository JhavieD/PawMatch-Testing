<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shared\Verification;

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
}
}
