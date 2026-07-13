<?php

namespace App\Filament\Evaluador\Widgets;

use App\Models\Autoevaluacion;
use App\Models\Empresa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class EvaluadorStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $evaluadorId = auth()->id();

        $totalEmpresas = Empresa::whereHas('evaluadores', function (Builder $query) use ($evaluadorId) {
            $query->where('user_id', $evaluadorId);
        })->count();

        $autoevaluacionesPendientes = Autoevaluacion::whereHas('empresa.evaluadores', function (Builder $query) use ($evaluadorId) {
            $query->where('user_id', $evaluadorId);
        })->where('estatus', 'En revisión')->count();

        $empresasDictaminadas = Empresa::whereHas('evaluadores', function (Builder $query) use ($evaluadorId) {
            $query->where('user_id', $evaluadorId);
        })->where('estatus_distintivo', 'Validado')->count();

        return [
            Stat::make('Empresas Asignadas', $totalEmpresas)
                ->description('Total de empresas bajo tu cargo')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
            Stat::make('Autoevaluaciones en Revisión', $autoevaluacionesPendientes)
                ->description('Requieren tu atención inmediata')
                ->descriptionIcon('heroicon-m-clock')
                ->color($autoevaluacionesPendientes > 0 ? 'warning' : 'success'),
            Stat::make('Empresas Dictaminadas', $empresasDictaminadas)
                ->description('Proceso de auditoría completado')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
