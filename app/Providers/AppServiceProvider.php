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
        // Force HTTPS when behind reverse proxy (ngrok, cloudflare)
        if (config('app.env') === 'local' && request()->header('X-Forwarded-Proto') === 'https') {
            \URL::forceScheme('https');
        }

        // Fix URL generation for reverse proxy
        if (request()->header('X-Forwarded-Host')) {
            \URL::forceRootUrl('https://' . request()->header('X-Forwarded-Host'));
        }
    }
}
