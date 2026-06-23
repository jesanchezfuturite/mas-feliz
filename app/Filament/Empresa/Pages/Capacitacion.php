<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use Illuminate\Support\Carbon;

class Capacitacion extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Capacitación (Apoyo al criterio indispensable 10)';

    protected static ?string $title = 'Capacitación';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.empresa.pages.capacitacion';

    public static function canAccess(): bool
    {
        return Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;
    }

    public function getViewData(): array
    {
        // Habilitar a partir del 10 de julio de 2026. force_active=true para testing
        $isHabilitado = request()->query('force_active') === 'true' || Carbon::now()->greaterThanOrEqualTo(Carbon::parse('2026-07-10 00:00:00'));

        return [
            'isHabilitado' => $isHabilitado,
        ];
    }
}
