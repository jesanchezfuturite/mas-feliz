<?php

namespace App\Filament\Empresa\Pages;

use Filament\Pages\Page;
use App\Filament\Empresa\Widgets\DashboardStatsOverview;
use App\Filament\Empresa\Widgets\RiesgosGeneralesChart;

class DiagnosticoTamizaje extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.empresa.pages.diagnostico-tamizaje';

    protected static ?string $title = 'Diagnóstico, tamizaje, apoyo al criterio indispensable 4';

    protected static ?string $navigationLabel = 'Diagnóstico y Tamizaje';

    protected static ?string $slug = 'diagnostico-tamizaje-criterio-4';

    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            RiesgosGeneralesChart::class,
        ];
    }
}
