<?php

namespace App\Filament\Empresa\Widgets;

use App\Models\Setting;
use App\Support\PrioridadAtencion;
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

    protected ?string $heading = 'Distribución por prioridad de atención';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    /** La aclaración que Angélica pidió que acompañe a la escala. */
    public function getDescription(): ?string
    {
        return PrioridadAtencion::NOTA;
    }

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

        // La escala pasó de tres niveles a cinco el 21/08/2026. Los niveles sin
        // un solo registro se omiten para que la dona no muestre rebanadas
        // vacías en la leyenda —lo habitual es que ninguna empresa tenga los
        // cinco a la vez.
        $niveles = [];
        foreach (PrioridadAtencion::ESCALA as $nivel) {
            $total = ($tamizajeCounts[$nivel] ?? 0) + ($manualCounts[$nivel] ?? 0);

            if ($total > 0) {
                $niveles[$nivel] = $total;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Diagnósticos',
                    'data' => array_values($niveles),
                    'backgroundColor' => array_map(
                        fn (string $nivel) => PrioridadAtencion::HEX[$nivel],
                        array_keys($niveles)
                    ),
                ],
            ],
            'labels' => array_keys($niveles),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
