<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
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

        // La escala de prioridad de atención necesita distinguir "Alta" de
        // "Urgente"; con los colores que trae Filament ambas caerían en el
        // mismo rojo. Se registra aquí, no en cada panel, porque la escala se
        // muestra en los cuatro.
        FilamentColor::register([
            'naranja' => Color::Orange,
        ]);

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
