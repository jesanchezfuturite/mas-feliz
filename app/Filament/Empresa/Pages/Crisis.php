<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use App\Models\MaterialApoyo;
use Illuminate\Support\Carbon;

class Crisis extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Crisis (Apoyo al criterio indispensable 16)';

    protected static ?string $title = 'Atención a Crisis';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.empresa.pages.crisis';

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
            'materiales' => MaterialApoyo::where('activo', true)
                ->where('seccion', 'crisis')
                ->get(),
        ];
    }
}
