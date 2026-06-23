<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Setting;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Escritorio';
    protected static ?int $navigationSort = 1;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Escritorio';

    public function getView(): string
    {
        $activas = Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;

        if (!$activas) {
            return 'filament.empresa.pages.dashboard-disabled';
        }

        return parent::getView();
    }

    public function getWidgets(): array
    {
        $activas = Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;

        if (!$activas) {
            return [];
        }

        return parent::getWidgets();
    }
}
