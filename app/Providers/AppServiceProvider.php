<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        $justRegistered = false;

        Event::listen(Registered::class, static function () use (&$justRegistered) {
            $justRegistered = true;
        });

        Event::listen(Login::class, static function (Login $event) use (&$justRegistered) {
            if ($justRegistered) {
                $justRegistered = false;
                return;
            }
            if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
                $event->user->sendEmailVerificationNotification();
            }
        });

        Inertia::share([
            'labels' => fn () => auth()->check()
                ? auth()->user()->labels()->orderBy('name')->get()
                : [],
            'flash' => [
                'success' => fn () => session('success'),
                'error' => fn () => session('error'),
            ],
        ]);
    }
}
