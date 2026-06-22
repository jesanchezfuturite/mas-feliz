<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Setting;

class Dashboard extends BaseDashboard
{
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
