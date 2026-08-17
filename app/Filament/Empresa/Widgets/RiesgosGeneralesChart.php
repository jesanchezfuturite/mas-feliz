<?php

namespace App\Filament\Empresa\Widgets;

use App\Models\Setting;
use Filament\Widgets\ChartWidget;

class RiesgosGeneralesChart extends ChartWidget
{
    /**
     * El interruptor de resultados del tamizaje también apaga esta gráfica: si la
     * empresa no debe leer niveles de riesgo por persona mientras se aclara en
     * reunión cómo debe interpretarse el instrumento, tampoco debe leerlos
     * agregados. Lo que sigue visible en la página es el avance de participación
     * y la liga del diagnóstico (DashboardStatsOverview).
     */
    public static function canView(): bool
    {
        return Setting::resultadosTamizajeVisibles();
    }

    public function getView(): string
    {
        $empresa = auth()->user();
        $autoevaluacion = $empresa->autoevaluaciones()->first();
        $hasSubmitted = $autoevaluacion && in_array($autoevaluacion->estatus, ['En revisión', 'Validado']);

        if (! $hasSubmitted) {
            return 'filament.empresa.widgets.chart-locked';
        }

        return parent::getView();
    }

    protected ?string $heading = 'Distribución de Niveles de Riesgo';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $empresa = auth()->user();

        $tamizajeCounts = $empresa->tamizajes()
            ->selectRaw('nivel_riesgo_general, count(*) as total')
            ->groupBy('nivel_riesgo_general')
            ->pluck('total', 'nivel_riesgo_general')
            ->toArray();

        $manualCounts = $empresa->casosSeguimiento()
            ->whereNotIn('identificador_empleado', function ($query) use ($empresa) {
                $query->select('nombre_completo')
                    ->from('tamizajes')
                    ->where('empresa_id', $empresa->id);
            })
            ->selectRaw('nivel_riesgo_detectado, count(*) as total')
            ->groupBy('nivel_riesgo_detectado')
            ->pluck('total', 'nivel_riesgo_detectado')
            ->toArray();

        $leve = ($tamizajeCounts['Leve'] ?? 0) + ($manualCounts['Leve'] ?? 0);
        $moderado = ($tamizajeCounts['Moderado'] ?? 0) + ($manualCounts['Moderado'] ?? 0);
        $urgente = ($tamizajeCounts['Urgente'] ?? 0) + ($manualCounts['Urgente'] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Diagnósticos',
                    'data' => [$leve, $moderado, $urgente],
                    'backgroundColor' => [
                        '#10b981', // Verde / Leve
                        '#f59e0b', // Amarillo / Moderado
                        '#ef4444', // Rojo / Urgente
                    ],
                ],
            ],
            'labels' => ['Leve', 'Moderado', 'Urgente'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
