<?php

namespace App\Support;

use App\Livewire\ResponderTamizaje;
use App\Models\Tamizaje;

/**
 * Cómo se MUESTRA el resultado del ASQ de un tamizaje.
 *
 * Angélica pidió el 27/08/2026 que el resultado aparezca completo, con la
 * acción que corresponde en letra más chica, y que el positivo con agudeza
 * confirmada se distinga como "Positivo: Riesgo Agudo". Todo esto es texto de
 * pantalla: `nivel_suicidio` sigue guardando solo Negativo/Positivo —ya hubo
 * dos terceros valores en esa columna y ella reportó ambos—, y la agudeza
 * sale de la pregunta 5 del JSON `respuestas`, igual que en
 * {@see PrioridadAtencion} y el comando de reclasificación.
 */
class ResultadoAsq
{
    /** Título del positivo agudo. Despliegue, no valor de columna. */
    public const TITULO_AGUDO = 'Positivo: Riesgo Agudo';

    /**
     * Título visible del resultado: "Negativo", "Positivo" o
     * "Positivo: Riesgo Agudo" cuando la pregunta 5 se contestó "Sí".
     */
    public static function titulo(Tamizaje $tamizaje): string
    {
        if (self::esAgudo($tamizaje)) {
            return self::TITULO_AGUDO;
        }

        return (string) $tamizaje->nivel_suicidio;
    }

    /**
     * Acción que corresponde al resultado, con la redacción de Angélica.
     * `null` para valores históricos que no estén en la escala vigente.
     */
    public static function accion(Tamizaje $tamizaje): ?string
    {
        if (self::esAgudo($tamizaje)) {
            return ResponderTamizaje::ACCION_SUICIDIO_AGUDO;
        }

        return ResponderTamizaje::ACCIONES_SUICIDIO[$tamizaje->nivel_suicidio] ?? null;
    }

    /**
     * ASQ positivo con la pregunta 5 en "Sí". Los tamizajes anteriores a la
     * pregunta 5 no la tienen: su agudeza es desconocida, no aguda, y se
     * muestran como Positivo a secas (la espera ya la señala su prioridad
     * "Agudeza pendiente de confirmar").
     */
    private static function esAgudo(Tamizaje $tamizaje): bool
    {
        if ($tamizaje->nivel_suicidio !== ResponderTamizaje::SUICIDIO_POSITIVO) {
            return false;
        }

        $agudeza = data_get($tamizaje->respuestas, 'conducta_suicida.5');

        return $agudeza !== null && $agudeza !== '' && (int) $agudeza === 1;
    }
}
