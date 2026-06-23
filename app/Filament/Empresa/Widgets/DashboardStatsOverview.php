<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends StatsOverviewWidget
{
    public function getView(): string
    {
        $empresa = auth()->user();
        $autoevaluacion = $empresa->autoevaluaciones()->first();
        $hasSubmitted = $autoevaluacion && in_array($autoevaluacion->estatus, ['En revisión', 'Validado']);

        if (!$hasSubmitted) {
            return 'filament.empresa.widgets.stats-locked';
        }

        return parent::getView();
    }

    protected function getStats(): array
    {
        $empresa = auth()->user();
        $tamizajes = $empresa->tamizajes()->count();
        $casosManuales = $empresa->casosSeguimiento()
            ->whereNotIn('identificador_empleado', function ($query) use ($empresa) {
                $query->select('nombre_completo')
                    ->from('tamizajes')
                    ->where('empresa_id', $empresa->id);
            })->count();

        $evaluados = $tamizajes + $casosManuales;
        $trabajadores = $empresa->numero_trabajadores ?: 1;
        $porcentaje = round(($evaluados / $trabajadores) * 100, 1);
        $liga = route('tamizaje.publico', ['token' => $empresa->token_tamizaje]);

        return [
            Stat::make('Liga de Diagnóstico', $liga)
                ->description('Haz clic aquí, copia y comparte esta liga con tus colaboradores')
                ->descriptionIcon('heroicon-m-link')
                ->color('info')
                ->view('filament.widgets.custom-stat'),
            Stat::make('Progreso de Participación', "{$porcentaje}%")
                ->description('Meta indispensable: 90%')
                ->descriptionIcon($porcentaje >= 90 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($porcentaje >= 90 ? 'success' : 'warning')
                ->view('filament.widgets.custom-stat'),
            Stat::make('Total Evaluados', $evaluados)
                ->description('Colaboradores que han respondido')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->view('filament.widgets.custom-stat'),
        ];
    }
}
