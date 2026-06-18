<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\ChartWidget;

class RiesgosGeneralesChart extends ChartWidget
{
    protected ?string $heading = 'Distribución de Niveles de Riesgo';

    protected int | string | array $columnSpan = 'full';

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
