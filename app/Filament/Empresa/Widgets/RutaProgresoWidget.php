<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\Widget;
use App\Models\Autoevaluacion;
use Illuminate\Support\Carbon;

class RutaProgresoWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.ruta-progreso-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $empresa = auth()->user();
        $autoevaluacion = $empresa->autoevaluaciones()->first();
        
        // Determinar estado de la Autoevaluación
        if (!$autoevaluacion) {
            $estadoAutoevaluacion = 'pendiente'; // No iniciada
        } elseif ($autoevaluacion->estatus === 'Borrador') {
            $estadoAutoevaluacion = 'borrador'; // En borrador
        } elseif ($autoevaluacion->estatus === 'En revisión') {
            $estadoAutoevaluacion = 'revision'; // En revisión por admin
        } else {
            $estadoAutoevaluacion = 'validado'; // Validado por admin
        }

        // Determinar si las siguientes fases están desbloqueadas
        $autoevaluacionCompletada = in_array($estadoAutoevaluacion, ['revision', 'validado']);

        // Determinar si la fase de Crisis y Capacitación está activa (10 de julio)
        $isFaseTemporalActiva = Carbon::now()->greaterThanOrEqualTo(Carbon::parse('2026-07-10 00:00:00'));

        return [
            'estadoAutoevaluacion' => $estadoAutoevaluacion,
            'autoevaluacionCompletada' => $autoevaluacionCompletada,
            'isFaseTemporalActiva' => $isFaseTemporalActiva,
        ];
    }
}
