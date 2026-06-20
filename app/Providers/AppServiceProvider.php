<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\ViewAction;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Notifications\ResetPassword::class,
            \App\Notifications\ResetPasswordNotificationEs::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Force Spanish locale globally
        app()->setLocale('es');

        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory())->create(
                Dsn::fromString(config('services.brevo.dsn'))
            );
        });

        ViewAction::configureUsing(function (ViewAction $action): void {
            $action->modalCancelAction(false);
        });
    }
}
