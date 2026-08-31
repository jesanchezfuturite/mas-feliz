<?php

namespace App\Filament\Empresa\Widgets;

use App\Livewire\ResponderTamizaje;
use App\Models\Setting;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
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
        // `respuestas` viene por la pregunta 5 del ASQ: la tarjeta del
        // instrumento separa el positivo agudo, y la agudeza vive en ese JSON.
        $rows = $empresa->tamizajes()
            ->whereIn('nivel_riesgo_general', PrioridadAtencion::ESCALA)
            ->get(['genero', 'edad', 'tiempo_trabajando', 'actividad_trabajo', 'actividad_trabajo_otra', 'nivel_riesgo_general', 'nivel_ansiedad', 'nivel_depresion', 'nivel_suicidio', 'respuestas']);

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

        // La tarjeta del ASQ va aparte: Angélica pidió el 27/08/2026 que la
        // gráfica también muestre el resultado completo, con el positivo agudo
        // como renglón propio y la acción de cada resultado en letra chica.
        // Es despliegue: la columna sigue guardando Negativo/Positivo, y la
        // agudeza sale de la pregunta 5 vía ResultadoAsq, igual que el badge
        // del detalle.
        $asq = function ($rows): array {
            $acciones = ResponderTamizaje::ACCIONES_SUICIDIO
                + [ResultadoAsq::TITULO_AGUDO => ResponderTamizaje::ACCION_SUICIDIO_AGUDO];
            // El agudo en rojo: es la misma condición que pone la prioridad en
            // Urgente. Los otros dos conservan el color de su resultado.
            $colores = [
                ResponderTamizaje::SUICIDIO_NEGATIVO => ColorNivel::hex(ResponderTamizaje::SUICIDIO_NEGATIVO),
                ResponderTamizaje::SUICIDIO_POSITIVO => ColorNivel::hex(ResponderTamizaje::SUICIDIO_POSITIVO),
                ResultadoAsq::TITULO_AGUDO => PrioridadAtencion::HEX[PrioridadAtencion::URGENTE],
            ];

            $conteos = array_fill_keys(array_keys($acciones), 0);
            $total = 0;
            foreach ($rows as $r) {
                if (! in_array($r->nivel_suicidio, ResponderTamizaje::NIVELES_SUICIDIO, true)) {
                    continue;
                }
                $conteos[ResultadoAsq::titulo($r)]++;
                $total++;
            }

            $niveles = [];
            foreach ($conteos as $titulo => $n) {
                $niveles[] = [
                    'label' => $titulo,
                    'count' => $n,
                    'color' => $colores[$titulo],
                    'accion' => $acciones[$titulo],
                ];
            }

            return ['total' => $total, 'niveles' => $niveles];
        };

        // Conteo simple por categoría, sin cruzar con ningún resultado. Es la
        // sección "¿Quiénes fueron evaluados?" que Angélica pidió el
        // 27/08/2026: el universo que participó, tal cual.
        $conteo = function ($rows, callable $keyFn): array {
            $out = [];
            foreach ($rows as $r) {
                $key = $keyFn($r);
                $key = ($key === null || $key === '') ? 'Sin especificar' : $key;
                $out[$key] = ($out[$key] ?? 0) + 1;
            }

            return $out;
        };

        // Variantes para conteos simples de las de abajo, que operan sobre
        // arreglos de niveles: mismo criterio (fusionar mayúsculas/espacios,
        // top 8 + "Otras") para que el universo y el cruce cuenten igual.
        $fusionarConteo = function (array $datos): array {
            $porClave = [];
            $visible = [];
            foreach ($datos as $categoria => $n) {
                $clave = mb_strtolower(trim((string) $categoria));
                if (! isset($visible[$clave]) || $n > $visible[$clave]['n']) {
                    $visible[$clave] = ['texto' => trim((string) $categoria), 'n' => $n];
                }
                $porClave[$clave] = ($porClave[$clave] ?? 0) + $n;
            }

            return array_combine(
                array_map(fn ($clave) => $visible[$clave]['texto'], array_keys($porClave)),
                array_values($porClave)
            );
        };

        $compactarConteo = function (array $datos, int $tope = 8): array {
            arsort($datos);
            if (count($datos) <= $tope) {
                return $datos;
            }
            $visibles = array_slice($datos, 0, $tope, true);
            $resto = array_slice($datos, $tope, null, true);
            $visibles['Otras ('.count($resto).' categorías)'] = array_sum($resto);

            return $visibles;
        };

        // Une categorías que solo difieren en mayúsculas o espacios. Aplica al
        // texto libre de "¿Cuál función?": en la UAdeC, "Docente", "DOCENTE" y
        // "docente " se dibujaban como tres barras distintas.
        $fusionar = function (array $datos): array {
            $porClave = [];
            $visible = [];
            foreach ($datos as $categoria => $conteos) {
                $clave = mb_strtolower(trim((string) $categoria));
                // Como nombre visible gana la variante con más personas.
                if (! isset($visible[$clave]) || $conteos['total'] > $visible[$clave]['n']) {
                    $visible[$clave] = ['texto' => trim((string) $categoria), 'n' => $conteos['total']];
                }
                if (! isset($porClave[$clave])) {
                    $porClave[$clave] = $conteos;

                    continue;
                }
                foreach ($conteos as $nivel => $n) {
                    $porClave[$clave][$nivel] += $n;
                }
            }

            return array_combine(
                array_map(fn ($clave) => $visible[$clave]['texto'], array_keys($porClave)),
                array_values($porClave)
            );
        };

        // Ordena por volumen y deja visibles solo las categorías más
        // frecuentes; el resto se suma en una sola barra "Otras". Sin esto,
        // una empresa grande convierte el cruce en cientos de barras de una
        // persona, ilegible (y reidentificable).
        $compactar = function (array $datos, int $tope = 8) use ($seleccion): array {
            uasort($datos, fn (array $a, array $b) => $b['total'] <=> $a['total']);
            if (count($datos) <= $tope) {
                return $datos;
            }
            $visibles = array_slice($datos, 0, $tope, true);
            $resto = array_slice($datos, $tope, null, true);
            $otras = array_fill_keys($seleccion['niveles'], 0) + ['total' => 0];
            foreach ($resto as $conteos) {
                foreach ($conteos as $nivel => $n) {
                    $otras[$nivel] += $n;
                }
            }
            $visibles['Otras ('.count($resto).' categorías)'] = $otras;

            return $visibles;
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
                ['titulo' => 'Indicadores de Conducta suicida'] + $asq($rows),
            ],
            // El universo que participó, sin cruce con resultados. Los
            // decliners no entran: quien no consiente no llega a la sección
            // de datos demográficos, así que no hay qué contar de ellos.
            'universo' => [
                [
                    'titulo' => 'Por sexo',
                    'datos' => $conteo($rows, fn ($r) => $r->genero),
                ],
                [
                    'titulo' => 'Por rango de edad',
                    'datos' => $ordenar($conteo($rows, fn ($r) => $r->edad), $ordenEdad),
                ],
                [
                    'titulo' => 'Por tiempo en la empresa',
                    'datos' => $ordenar($conteo($rows, fn ($r) => $r->tiempo_trabajando), $ordenTiempo),
                ],
                [
                    'titulo' => 'Por tipo de funciones',
                    'datos' => $compactarConteo($fusionarConteo(
                        $conteo($rows, fn ($r) => $r->actividad_trabajo === 'Otra' ? ($r->actividad_trabajo_otra ?: 'Otra') : $r->actividad_trabajo)
                    )),
                ],
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
                    'datos' => $compactar($fusionar(
                        $agrupar($rows, fn ($r) => $r->actividad_trabajo === 'Otra' ? ($r->actividad_trabajo_otra ?: 'Otra') : $r->actividad_trabajo)
                    )),
                ],
            ],
        ];
    }
}
