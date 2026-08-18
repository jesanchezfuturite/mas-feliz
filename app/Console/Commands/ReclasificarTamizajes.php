<?php

namespace App\Console\Commands;

use App\Livewire\ResponderTamizaje;
use App\Models\Tamizaje;
use Illuminate\Console\Command;

/**
 * Reclasifica los tamizajes aplicados antes de la corrección del 18/08/2026.
 *
 * Hasta esa fecha un "Sí" en la pregunta 4 —"¿Alguna vez has intentado
 * quitarte la vida?", que abarca toda la vida— marcaba por sí solo riesgo
 * agudo, y la ansiedad o depresión graves escalaban el riesgo general a
 * urgente. De las 1,771 alertas urgentes acumuladas, 933 correspondían a
 * personas sin ningún indicador reciente de conducta suicida.
 *
 * La pregunta de agudeza no existía cuando se aplicaron estos tamizajes, así
 * que NINGÚN registro histórico puede elevarse a riesgo agudo: esa
 * determinación corresponde a la valoración clínica. El comando solo corrige
 * a la baja lo que estaba sobreclasificado.
 *
 * No toca los casos de seguimiento: sobre esos ya hay trabajo de las empresas
 * y de los gestores.
 */
class ReclasificarTamizajes extends Command
{
    protected $signature = 'tamizajes:reclasificar
                            {--aplicar : Escribe los cambios. Sin esta bandera solo muestra lo que haría}';

    protected $description = 'Recalcula el nivel de los tamizajes aplicados antes de la corrección del 18/08/2026';

    /** Marca en `respuestas` que deja constancia y hace el comando idempotente. */
    private const MARCA = 'reclasificacion_18_08_2026';

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
            ->where('nivel_riesgo_general', '!=', 'No participó')
            ->chunkById(500, function ($tamizajes) use ($aplicar, &$resumen, &$tocados, &$omitidos) {
                foreach ($tamizajes as $tamizaje) {
                    $respuestas = $tamizaje->respuestas ?? [];

                    if (isset($respuestas[self::MARCA])) {
                        $omitidos++;

                        continue;
                    }

                    $suicidioNuevo = $this->nivelSuicidio($tamizaje);
                    $riesgoNuevo = $this->nivelRiesgoGeneral($tamizaje, $suicidioNuevo);

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
     * Sin pregunta de agudeza, cualquier "Sí" en las cuatro preguntas queda
     * como positivo que requiere valoración posterior.
     */
    private function nivelSuicidio(Tamizaje $tamizaje): string
    {
        return $tamizaje->riesgo_conducta_suicida > 0
            ? ResponderTamizaje::SUICIDIO_POSITIVO
            : ResponderTamizaje::SUICIDIO_NEGATIVO;
    }

    private function nivelRiesgoGeneral(Tamizaje $tamizaje, string $nivelSuicidio): string
    {
        if ($nivelSuicidio === ResponderTamizaje::SUICIDIO_AGUDO) {
            return 'Urgente';
        }

        $requiereAtencion = $tamizaje->riesgo_conducta_suicida > 0
            || in_array($tamizaje->nivel_depresion, ['Moderada', 'Moderadamente grave', 'Grave'], true)
            || in_array($tamizaje->nivel_ansiedad, ['Moderada', 'Grave'], true);

        return $requiereAtencion ? 'Moderado' : 'Leve';
    }
}
