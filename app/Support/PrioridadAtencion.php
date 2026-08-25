<?php

namespace App\Support;

/**
 * Prioridad de atención de un tamizaje.
 *
 * Angélica cambió el nombre y la escala el 21/08/2026: lo que se llamaba
 * "Riesgo General" ahora es "Prioridad de atención", y ya no son tres niveles
 * sino cuatro más un estado de espera. El nombre importa porque lo que la
 * plataforma muestra es una prioridad operativa del Distintivo, no un
 * diagnóstico: la nota de `NOTA` va junto a la escala en todos los listados.
 *
 * La tabla que definió es esta:
 *
 *   Ansiedad mínima + depresión mínima + ASQ negativo ............... Leve
 *   Ansiedad/depresión leve o moderada + ASQ negativo .............. Moderada
 *   Ansiedad grave y/o depresión mod. grave/grave + ASQ negativo ... Alta
 *   ASQ positivo no agudo + ansiedad/depresión en cualquier nivel .. Alta
 *   ASQ positivo con pregunta 5 = "Sí" ............................ Urgente
 *
 * `AGUDEZA_PENDIENTE` no sale de esa tabla: es para los tamizajes aplicados
 * antes de que existiera la pregunta 5. Ahí el ASQ salió positivo y nunca se
 * exploró la agudeza, así que no se puede afirmar que sean "no agudos" (Alta)
 * ni tratarlos como emergencia (Urgente). Quedan aparte, a la espera de la
 * valoración que lo confirme.
 *
 * Esta clase es la única fuente de la escala: la consultan el cuestionario
 * público, el comando de reclasificación, los listados y las gráficas.
 */
class PrioridadAtencion
{
    /** Cómo se titula la columna en todos los listados. */
    public const ETIQUETA = 'Prioridad de atención';

    /** Aclaración que Angélica pidió agregar junto a la escala. */
    public const NOTA = 'La prioridad de atención que se muestra es una clasificación operativa del Distintivo +Feliz, no una clasificación clínica de riesgo suicida.';

    public const LEVE = 'Leve';

    public const MODERADA = 'Moderada';

    public const ALTA = 'Alta';

    public const URGENTE = 'Urgente';

    public const AGUDEZA_PENDIENTE = 'Agudeza pendiente de confirmar';

    /** Quien declinó participar. No es una prioridad, pero ocupa la columna. */
    public const NO_PARTICIPO = 'No participó';

    /**
     * La escala, de menor a mayor. Alimenta las opciones de los selectores,
     * los filtros y el orden de las series en las gráficas.
     */
    public const ESCALA = [
        self::LEVE,
        self::MODERADA,
        self::ALTA,
        self::URGENTE,
        self::AGUDEZA_PENDIENTE,
    ];

    /**
     * Niveles que cuentan como "requiere atención" para las métricas de
     * avance. La agudeza pendiente cuenta: es justamente lo que hay que ir a
     * confirmar.
     */
    public const REQUIEREN_ATENCION = [
        self::MODERADA,
        self::ALTA,
        self::URGENTE,
        self::AGUDEZA_PENDIENTE,
    ];

    /** Color de badge de Filament. `naranja` se registra en AppServiceProvider. */
    public const COLORES = [
        self::LEVE => 'success',
        self::MODERADA => 'warning',
        self::ALTA => 'naranja',
        self::URGENTE => 'danger',
        self::AGUDEZA_PENDIENTE => 'info',
        self::NO_PARTICIPO => 'gray',
    ];

    /** El mismo código en hexadecimal, para las gráficas de Chart.js. */
    public const HEX = [
        self::LEVE => '#10b981',
        self::MODERADA => '#f59e0b',
        self::ALTA => '#f97316',
        self::URGENTE => '#ef4444',
        self::AGUDEZA_PENDIENTE => '#6366f1',
    ];

    /** Opciones para un `Select`, con el valor como clave. */
    public static function opciones(): array
    {
        return array_combine(self::ESCALA, self::ESCALA);
    }

    /**
     * Prioridad que corresponde a un tamizaje.
     *
     * @param  string|null  $nivelAnsiedad  Nivel del GAD-7 ya calculado.
     * @param  string|null  $nivelDepresion  Nivel del PHQ-9 ya calculado.
     * @param  int  $puntajeSuicidio  Suma de las cuatro preguntas del ASQ.
     * @param  int|null  $agudeza  Respuesta a la pregunta 5; `null` si nunca se formuló.
     */
    public static function calcular(
        ?string $nivelAnsiedad,
        ?string $nivelDepresion,
        int $puntajeSuicidio,
        ?int $agudeza
    ): string {
        if ($puntajeSuicidio > 0) {
            return match ($agudeza) {
                1 => self::URGENTE,
                0 => self::ALTA,
                default => self::AGUDEZA_PENDIENTE,
            };
        }

        if ($nivelAnsiedad === 'Grave' || in_array($nivelDepresion, ['Moderadamente grave', 'Grave'], true)) {
            return self::ALTA;
        }

        if (in_array($nivelAnsiedad, ['Leve', 'Moderada'], true) || in_array($nivelDepresion, ['Leve', 'Moderada'], true)) {
            return self::MODERADA;
        }

        return self::LEVE;
    }
}
