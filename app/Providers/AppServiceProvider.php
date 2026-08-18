<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        // Ova aplikacija je API-only (nema Blade stranicu za reset lozinke), pa ručno
        // gradimo link umesto oslanjanja na named route 'password.reset' koji ne postoji.
        // FRONTEND_URL treba da pokazuje na React reset-password ekran koji čita ?token=&email=.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $frontendUrl = config('app.frontend_url', config('app.url'));

            return "{$frontendUrl}/reset-password?token={$token}&email=".urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
