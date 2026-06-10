<?php

namespace App\Filament\Resources\Empresas\Widgets;

use App\Models\Empresa;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmpresaStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Empresas Registradas', Empresa::count())
                ->description('Empresas incorporadas en el sistema')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success')
                ->view('filament.widgets.custom-stat'),
            Stat::make('Total de Trabajadores Impactados', Empresa::sum('numero_trabajadores'))
                ->description('Colaboradores beneficiados directamente')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->view('filament.widgets.custom-stat'),
        ];
    }
}
