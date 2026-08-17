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

        if (! $hasSubmitted) {
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

        // La participación se mide contra el universo que la empresa declaró al
        // registrarse y cuenta todos los cuestionarios respondidos, otorguen o no
        // el consentimiento. Se calcula sobre los tamizajes —no sobre $evaluados—
        // para que dé exactamente el mismo número que ve el evaluador en "Avance
        // por organización"; los casos capturados a mano no son participación en
        // el tamizaje.
        //
        // Antes el denominador caía a 1 cuando la empresa no había declarado su
        // total de colaboradores, y el widget anunciaba cosas como "500%".
        $trabajadores = (int) $empresa->numero_trabajadores;
        $porcentaje = $trabajadores > 0
            ? round(($tamizajes / $trabajadores) * 100, 1)
            : null;
        $liga = route('tamizaje.publico', ['token' => $empresa->token_tamizaje]);

        return [
            Stat::make('Liga de Diagnóstico', $liga)
                ->description('Haz clic aquí, copia y comparte esta liga con tus colaboradores')
                ->descriptionIcon('heroicon-m-link')
                ->color('info')
                ->view('filament.widgets.custom-stat'),
            Stat::make('Progreso de Participación', $porcentaje === null ? 'Sin dato' : "{$porcentaje}%")
                ->description($trabajadores > 0
                    ? "{$tamizajes} de {$trabajadores} colaboradores · meta indispensable: 90%"
                    : 'Falta registrar tu total de colaboradores')
                ->descriptionIcon($porcentaje !== null && $porcentaje >= 90 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($porcentaje !== null && $porcentaje >= 90 ? 'success' : 'warning')
                ->view('filament.widgets.custom-stat'),
            Stat::make('Total Evaluados', $evaluados)
                ->description('Con tamizaje respondido o caso registrado')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->view('filament.widgets.custom-stat'),
        ];
    }
}
