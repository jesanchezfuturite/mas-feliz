<?php

namespace App\Filament\Empresa\Widgets;

use App\Livewire\ResponderTamizaje;
use App\Models\Setting;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use Filament\Widgets\Widget;

class EstadisticaTamizajeWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.estadistica-tamizaje';

    protected int|string|array $columnSpan = 'full';

    /**
     * Instrumento por el que se cruzan los perfiles demográficos. Es la
     * petición de fondo de Angélica (21/08/2026): la empresa debe ver
     * tendencias de sintomatología —niveles de ansiedad, depresión y ASQ por
     * sexo, edad, antigüedad y función—, no solo la prioridad de atención.
     * La prioridad se conserva como una opción más del selector.
     */
    public string $instrumento = 'ansiedad';

    /**
     * Las escalas seleccionables. `campo` es la columna del tamizaje y
     * `niveles` su orden de severidad; los colores salen de ColorNivel para
     * cualquiera de ellas.
     */
    public const INSTRUMENTOS = [
        'ansiedad' => [
            'titulo' => 'Síntomas de Ansiedad (GAD-7)',
            'campo' => 'nivel_ansiedad',
            'niveles' => ['Mínima o sin ansiedad', 'Leve', 'Moderada', 'Grave'],
        ],
        'depresion' => [
            'titulo' => 'Síntomas de Depresión (PHQ-9)',
            'campo' => 'nivel_depresion',
            'niveles' => ['Mínima o ausente', 'Leve', 'Moderada', 'Moderadamente grave', 'Grave'],
        ],
        'suicidio' => [
            'titulo' => 'Indicadores de Conducta suicida',
            'campo' => 'nivel_suicidio',
            'niveles' => ResponderTamizaje::NIVELES_SUICIDIO,
        ],
        'prioridad' => [
            'titulo' => PrioridadAtencion::ETIQUETA,
            'campo' => 'nivel_riesgo_general',
            'niveles' => PrioridadAtencion::ESCALA,
        ],
    ];

    /**
     * Mismo interruptor que el listado por individuo: este bloque desglosa los
     * niveles de cada instrumento (GAD-7, PHQ-9, conducta suicida) y es justo lo
     * que no debe leerse hasta que se aclare cómo interpretarlos.
     */
    public static function canView(): bool
    {
        return Setting::resultadosTamizajeVisibles();
    }

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

        // La propiedad es pública de Livewire: se sanea por si llega alterada.
        if (! isset(self::INSTRUMENTOS[$this->instrumento])) {
            $this->instrumento = 'ansiedad';
        }
        $seleccion = self::INSTRUMENTOS[$this->instrumento];

        // Solo tamizajes con resultado de riesgo real (excluye "No participó" y nulos).
        $rows = $empresa->tamizajes()
            ->whereIn('nivel_riesgo_general', PrioridadAtencion::ESCALA)
            ->get(['genero', 'edad', 'tiempo_trabajando', 'actividad_trabajo', 'actividad_trabajo_otra', 'nivel_riesgo_general', 'nivel_ansiedad', 'nivel_depresion', 'nivel_suicidio']);

        // Color por severidad del nivel (consistente con el resto del sistema).
        $color = fn ($nivel) => ColorNivel::hex($nivel);

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

        // Agrupa filas por una dimensión y cuenta por nivel del instrumento
        // seleccionado en el selector (sintomatología o prioridad).
        $agrupar = function ($rows, callable $keyFn) use ($seleccion): array {
            $out = [];
            foreach ($rows as $r) {
                $key = $keyFn($r);
                $key = ($key === null || $key === '') ? 'Sin especificar' : $key;
                if (! isset($out[$key])) {
                    // Un contador por nivel de la escala seleccionada, más el total.
                    $out[$key] = array_fill_keys($seleccion['niveles'], 0) + ['total' => 0];
                }
                $nivel = $r->{$seleccion['campo']};
                if ($nivel !== null && isset($out[$key][$nivel])) {
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
            // La escala del instrumento seleccionado se pasa a la vista en vez
            // de repetirla ahí: los niveles cambian con el selector (y ya
            // cambiaron de fondo una vez, el 21/08/2026).
            'escala' => array_map(
                fn (string $nivel) => ['label' => $nivel, 'color' => ColorNivel::hex($nivel)],
                $seleccion['niveles']
            ),
            'tituloPerfil' => $seleccion['titulo'],
            'opcionesInstrumento' => array_map(fn (array $i) => $i['titulo'], self::INSTRUMENTOS),
            // La nota legal acompaña solo a la prioridad de atención: es una
            // aclaración sobre esa escala, no sobre los instrumentos clínicos.
            'nota' => $this->instrumento === 'prioridad' ? PrioridadAtencion::NOTA : null,
            'instrumentos' => [
                ['titulo' => 'Síntomas de Ansiedad (GAD-7)'] + $instrumento($rows, 'nivel_ansiedad', ['Mínima o sin ansiedad', 'Leve', 'Moderada', 'Grave']),
                ['titulo' => 'Síntomas de Depresión (PHQ-9)'] + $instrumento($rows, 'nivel_depresion', ['Mínima o ausente', 'Leve', 'Moderada', 'Moderadamente grave', 'Grave']),
                ['titulo' => 'Indicadores de Conducta suicida'] + $instrumento($rows, 'nivel_suicidio', ResponderTamizaje::NIVELES_SUICIDIO),
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
