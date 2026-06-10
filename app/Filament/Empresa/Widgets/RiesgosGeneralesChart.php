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

        $counts = $empresa->tamizajes()
            ->selectRaw('nivel_riesgo_general, count(*) as total')
            ->groupBy('nivel_riesgo_general')
            ->pluck('total', 'nivel_riesgo_general')
            ->toArray();

        $leve = $counts['Leve'] ?? 0;
        $moderado = $counts['Moderado'] ?? 0;
        $urgente = $counts['Urgente'] ?? 0;

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
