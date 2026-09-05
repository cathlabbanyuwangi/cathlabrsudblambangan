<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
        // Paksa skema HTTPS otomatis saat berada di environment production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Creator otomatis diizinkan mengakses semua permission / gate tanpa batasan
        Gate::before(function ($user, $ability) {
            return $user->hasRole('creator') ? true : null;
        });
    }
}