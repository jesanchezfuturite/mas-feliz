<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\Widget;

class EstadisticaTamizajeWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.estadistica-tamizaje';

    protected int | string | array $columnSpan = 'full';

    /**
     * Se bloquea igual que el resto de widgets del diagnóstico:
     * disponible cuando la empresa ya envió su autoevaluación.
     */
    public function getView(): string
    {
        $empresa = auth()->user();
        $autoevaluacion = $empresa->autoevaluaciones()->first();
        $hasSubmitted = $autoevaluacion && in_array($autoevaluacion->estatus, ['En revisión', 'Validado']);

        if (! $hasSubmitted) {
            return 'filament.empresa.widgets.chart-locked';
        }

        return 'filament.empresa.widgets.estadistica-tamizaje';
    }

    protected function getViewData(): array
    {
        $empresa = auth()->user();

        // Solo tamizajes con resultado de riesgo real (excluye "No participó" y nulos).
        $rows = $empresa->tamizajes()
            ->whereIn('nivel_riesgo_general', ['Leve', 'Moderado', 'Urgente'])
            ->get(['genero', 'edad', 'tiempo_trabajando', 'actividad_trabajo', 'actividad_trabajo_otra', 'nivel_riesgo_general', 'nivel_ansiedad', 'nivel_depresion', 'nivel_suicidio']);

        // Color por severidad del nivel (consistente con el resto del sistema).
        $color = fn ($nivel) => match ($nivel) {
            'Grave', 'Moderadamente grave', 'Riesgo Agudo' => '#ef4444',
            'Moderada', 'Evaluación Adicional' => '#f59e0b',
            'Leve' => '#84cc16',
            'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo' => '#22c55e',
            default => '#94a3b8',
        };

        // Distribución de niveles de un instrumento, en orden de severidad.
        $instrumento = function ($rows, string $campo, array $orden) use ($color): array {
            $counts = [];
            $total = 0;
            foreach ($rows as $r) {
                $v = $r->{$campo};
                if ($v === null || $v === '') {
                    continue;
                }
                $counts[$v] = ($counts[$v] ?? 0) + 1;
                $total++;
            }
            $niveles = [];
            foreach ($orden as $lvl) {
                $niveles[] = ['label' => $lvl, 'count' => $counts[$lvl] ?? 0, 'color' => $color($lvl)];
                unset($counts[$lvl]);
            }
            foreach ($counts as $lvl => $n) {
                $niveles[] = ['label' => $lvl, 'count' => $n, 'color' => $color($lvl)];
            }
            return ['total' => $total, 'niveles' => $niveles];
        };

        // Agrupa filas por una dimensión y cuenta por nivel de riesgo.
        $agrupar = function ($rows, callable $keyFn): array {
            $out = [];
            foreach ($rows as $r) {
                $key = $keyFn($r);
                $key = ($key === null || $key === '') ? 'Sin especificar' : $key;
                if (! isset($out[$key])) {
                    $out[$key] = ['Leve' => 0, 'Moderado' => 0, 'Urgente' => 0, 'total' => 0];
                }
                $nivel = $r->nivel_riesgo_general;
                if (isset($out[$key][$nivel])) {
                    $out[$key][$nivel]++;
                    $out[$key]['total']++;
                }
            }
            return $out;
        };

        // Reordena una agrupación según un orden predefinido; extras van al final.
        $ordenar = function (array $data, array $orden): array {
            $out = [];
            foreach ($orden as $k) {
                if (isset($data[$k])) {
                    $out[$k] = $data[$k];
                }
            }
            foreach ($data as $k => $v) {
                if (! isset($out[$k])) {
                    $out[$k] = $v;
                }
            }
            return $out;
        };

        $ordenEdad = ['Menor de 18 años', '18 a 24 años', '25 a 34 años', '35 a 44 años', '45 a 54 años', '55 años o más'];
        $ordenTiempo = ['Menos de 6 meses', 'De 6 meses a 1 año', 'Más de 1 año a 3 años', 'Más de 3 años a 5 años', 'Más de 5 años'];

        return [
            'total' => $rows->count(),
            'instrumentos' => [
                ['titulo' => 'Ansiedad (GAD-7)'] + $instrumento($rows, 'nivel_ansiedad', ['Mínima o sin ansiedad', 'Leve', 'Moderada', 'Grave']),
                ['titulo' => 'Depresión (PHQ-9)'] + $instrumento($rows, 'nivel_depresion', ['Mínima o ausente', 'Leve', 'Moderada', 'Moderadamente grave', 'Grave']),
                ['titulo' => 'Riesgo suicida'] + $instrumento($rows, 'nivel_suicidio', ['Negativo', 'Evaluación Adicional', 'Riesgo Agudo']),
            ],
            'dimensiones' => [
                [
                    'titulo' => 'Por sexo',
                    'datos' => $agrupar($rows, fn ($r) => $r->genero),
                ],
                [
                    'titulo' => 'Por rango de edad',
                    'datos' => $ordenar($agrupar($rows, fn ($r) => $r->edad), $ordenEdad),
                ],
                [
                    'titulo' => 'Por tiempo en la empresa',
                    'datos' => $ordenar($agrupar($rows, fn ($r) => $r->tiempo_trabajando), $ordenTiempo),
                ],
                [
                    'titulo' => 'Por tipo de funciones',
                    'datos' => $agrupar($rows, fn ($r) => $r->actividad_trabajo === 'Otra' ? ($r->actividad_trabajo_otra ?: 'Otra') : $r->actividad_trabajo),
                ],
            ],
        ];
    }
}
