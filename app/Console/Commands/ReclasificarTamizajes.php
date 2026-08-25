<?php

namespace App\Console\Commands;

use App\Livewire\ResponderTamizaje;
use App\Models\Tamizaje;
use App\Support\PrioridadAtencion;
use Illuminate\Console\Command;

/**
 * Reclasifica los tamizajes ya aplicados con la escala vigente.
 *
 * Va en dos pasadas, cada una con su marca en `respuestas`:
 *
 * - `reclasificacion_18_08_2026`: hasta esa fecha un "Sí" en la pregunta 4
 *   —"¿Alguna vez has intentado quitarte la vida?", que abarca toda la vida—
 *   marcaba por sí solo riesgo agudo, y la ansiedad o depresión graves
 *   escalaban a urgente. De las 1,771 alertas urgentes acumuladas, 933 eran de
 *   personas sin ningún indicador reciente.
 *
 * - `prioridad_atencion_21_08_2026`: Angélica cambió "Riesgo General" por
 *   "Prioridad de atención" con la escala de cuatro niveles de
 *   {@see PrioridadAtencion}. Los tamizajes aplicados antes de que existiera la
 *   pregunta de agudeza no pueden clasificarse como "Alta" (que supone ASQ
 *   positivo no agudo) ni como "Urgente": quedan en "Agudeza pendiente de
 *   confirmar", que es el estado que ella pidió agregar para ellos.
 *
 * Ninguna pasada eleva un registro histórico a riesgo agudo: esa determinación
 * corresponde a la valoración clínica, no a un recálculo.
 *
 * No toca los casos de seguimiento: sobre esos ya hay trabajo de las empresas
 * y de los gestores. La migración de la escala sí renombra su nivel.
 */
class ReclasificarTamizajes extends Command
{
    protected $signature = 'tamizajes:reclasificar
                            {--aplicar : Escribe los cambios. Sin esta bandera solo muestra lo que haría}';

    protected $description = 'Recalcula la prioridad de atención de los tamizajes ya aplicados';

    /** Marca de la pasada vigente. Deja constancia y hace el comando idempotente. */
    private const MARCA = 'prioridad_atencion_21_08_2026';

    public function handle(): int
    {
        $aplicar = (bool) $this->option('aplicar');

        if (! $aplicar) {
            $this->warn('Simulación: no se escribirá nada. Usa --aplicar para guardar los cambios.');
        }

        $resumen = [];
        $tocados = 0;
        $omitidos = 0;

        Tamizaje::query()
            ->where('nivel_riesgo_general', '!=', PrioridadAtencion::NO_PARTICIPO)
            ->chunkById(500, function ($tamizajes) use ($aplicar, &$resumen, &$tocados, &$omitidos) {
                foreach ($tamizajes as $tamizaje) {
                    $respuestas = $tamizaje->respuestas ?? [];

                    if (isset($respuestas[self::MARCA])) {
                        $omitidos++;

                        continue;
                    }

                    $agudeza = $this->agudeza($tamizaje);
                    $suicidioNuevo = $this->nivelSuicidio($tamizaje);
                    $riesgoNuevo = PrioridadAtencion::calcular(
                        $tamizaje->nivel_ansiedad,
                        $tamizaje->nivel_depresion,
                        (int) $tamizaje->riesgo_conducta_suicida,
                        $agudeza
                    );

                    if ($suicidioNuevo === $tamizaje->nivel_suicidio && $riesgoNuevo === $tamizaje->nivel_riesgo_general) {
                        continue;
                    }

                    $clave = sprintf(
                        '%s → %s  |  %s → %s',
                        $tamizaje->nivel_suicidio ?: '(sin nivel)',
                        $suicidioNuevo,
                        $tamizaje->nivel_riesgo_general,
                        $riesgoNuevo
                    );
                    $resumen[$clave] = ($resumen[$clave] ?? 0) + 1;
                    $tocados++;

                    if ($aplicar) {
                        $respuestas[self::MARCA] = [
                            'nivel_suicidio_anterior' => $tamizaje->nivel_suicidio,
                            'nivel_riesgo_general_anterior' => $tamizaje->nivel_riesgo_general,
                        ];

                        $tamizaje->update([
                            'nivel_suicidio' => $suicidioNuevo,
                            'nivel_riesgo_general' => $riesgoNuevo,
                            'respuestas' => $respuestas,
                        ]);
                    }
                }
            });

        $this->newLine();
        $filas = [];
        foreach ($resumen as $cambio => $total) {
            $filas[] = [$cambio, number_format($total)];
        }
        $this->table(['Cambio', 'Registros'], $filas);

        $this->line(sprintf(
            '%s %s registros. %s ya estaban reclasificados.',
            $aplicar ? 'Actualizados:' : 'Se actualizarían:',
            number_format($tocados),
            number_format($omitidos)
        ));

        return self::SUCCESS;
    }

    /**
     * Respuesta a la pregunta de agudeza, o `null` si nunca se formuló.
     *
     * Los tamizajes aplicados antes del 18/08/2026 no la tienen, y esa
     * ausencia es justo lo que los deja en "Agudeza pendiente de confirmar".
     */
    private function agudeza(Tamizaje $tamizaje): ?int
    {
        $valor = data_get($tamizaje->respuestas, 'conducta_suicida.5');

        return $valor === null || $valor === '' ? null : (int) $valor;
    }

    /**
     * Resultado del ASQ. Lo deciden las preguntas 1 a 4; la agudeza no entra
     * —solo afecta la prioridad—, así que este método no la recibe.
     */
    private function nivelSuicidio(Tamizaje $tamizaje): string
    {
        return (int) $tamizaje->riesgo_conducta_suicida === 0
            ? ResponderTamizaje::SUICIDIO_NEGATIVO
            : ResponderTamizaje::SUICIDIO_POSITIVO;
    }
}
