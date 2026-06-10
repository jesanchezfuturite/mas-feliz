<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Filament\Actions\ViewAction;

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
        ViewAction::configureUsing(function (ViewAction $action): void {
            $action->modalCancelAction(false);
        });
    }
}
