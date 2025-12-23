<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
        // Use Bootstrap styling for pagination links (compatible with AdminLTE)
        Paginator::useBootstrap();

        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
