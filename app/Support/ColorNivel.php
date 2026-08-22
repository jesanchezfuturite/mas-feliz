<?php

namespace App\Support;

/**
 * Color con el que se pinta cualquier nivel del tamizaje.
 *
 * La misma tabla de colores estaba copiada en seis lugares —el detalle del
 * tamizaje, el de casos del panel empresa, el del panel admin, la tabla de
 * casos, la de canalizados y la de referencias— y ya se había desincronizado:
 * varias copias seguían pintando "Moderado" y ninguna conocía "Alta".
 *
 * Cubre las dos escalas que se muestran juntas: la prioridad de atención
 * ({@see PrioridadAtencion}) y los niveles de cada instrumento (GAD-7, PHQ-9 y
 * ASQ), porque en los listados aparecen en columnas vecinas.
 */
class ColorNivel
{
    /** Niveles de los instrumentos, del más grave al más leve. */
    private const GRAVE = ['Grave', 'Moderadamente grave', 'Riesgo Agudo'];

    /**
     * "Evaluación Adicional" es el nombre que tuvo el positivo del ASQ antes y
     * ya no se asigna. Se conserva solo aquí, como color: si quedara algún
     * registro histórico con ese valor, se pinta bien en vez de salir en gris.
     * Como categoría del desglose ya no se dibuja —ver
     * ResponderTamizaje::NIVELES_SUICIDIO—.
     */
    private const INTERMEDIO = ['Moderada', 'Evaluación Adicional', 'Positivo: requiere valoración posterior'];

    private const LEVE = ['Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo'];

    /** Hexadecimal, para los badges dibujados a mano y las gráficas. */
    public static function hex(?string $nivel): string
    {
        if ($nivel !== null && isset(PrioridadAtencion::HEX[$nivel])) {
            return PrioridadAtencion::HEX[$nivel];
        }

        return match (true) {
            in_array($nivel, self::GRAVE, true) => '#ef4444',
            in_array($nivel, self::INTERMEDIO, true) => '#f59e0b',
            in_array($nivel, self::LEVE, true) => '#22c55e',
            default => '#6b7280',
        };
    }

    /** Nombre de color de Filament, para `->badge()->color()`. */
    public static function badge(?string $nivel): string
    {
        if ($nivel !== null && isset(PrioridadAtencion::COLORES[$nivel])) {
            return PrioridadAtencion::COLORES[$nivel];
        }

        return match (true) {
            in_array($nivel, self::GRAVE, true) => 'danger',
            in_array($nivel, self::INTERMEDIO, true) => 'warning',
            in_array($nivel, self::LEVE, true) => 'success',
            default => 'gray',
        };
    }
}
