<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Page;
use App\Models\Setting;

class PrevencionPromocion extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Prevención/ Promoción: (Apoyo al criterio indispensable)';

    protected static ?string $title = 'Prevención y Promoción';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.empresa.pages.prevencion-promocion';

    public static function canAccess(): bool
    {
        return Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;
    }
}
