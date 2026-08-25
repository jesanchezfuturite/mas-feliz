<?php

use App\Livewire\ResponderTamizaje;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Resultado del ASQ con los nombres del documento de Angélica (22/08/2026).
 *
 * Su recuadro "PARA LA REVISIÓN" define dos resultados —"Negativo" y
 * "Positivo"— y el texto largo que va después de los dos puntos es la acción
 * que corresponde, no parte del nombre. La plataforma guardaba tres valores y
 * ninguno se llamaba así:
 *
 *   Riesgo Agudo ............................. → Positivo
 *   Positivo: requiere valoración posterior .. → Positivo
 *   Evaluación Adicional ..................... → Positivo
 *
 * `Riesgo Agudo` era invención nuestra para que la pregunta 5 se notara en esa
 * columna. La agudeza no se pierde: sigue elevando la prioridad de atención a
 * "Urgente", que es donde su escala la pide, y esa columna no se toca aquí.
 *
 * Va como migración y no como pasada del comando de reclasificación a
 * propósito: el despliegue en cPanel ya corre `migrate --force`, y un paso
 * manual menos es un paso menos que se puede olvidar.
 */
return new class extends Migration
{
    /** Todo lo que alguna vez significó "positivo" en esta columna. */
    private const POSITIVOS_ANTERIORES = [
        'Riesgo Agudo',
        'Positivo: requiere valoración posterior',
        'Evaluación Adicional',
    ];

    public function up(): void
    {
        DB::table('tamizajes')
            ->whereIn('nivel_suicidio', self::POSITIVOS_ANTERIORES)
            ->update(['nivel_suicidio' => ResponderTamizaje::SUICIDIO_POSITIVO]);
    }

    /**
     * La vuelta atrás no puede ser fiel: los tres valores anteriores quedaron
     * fundidos en uno y el que le tocaba a cada registro ya no se puede
     * distinguir por esta columna. Se devuelve al positivo no agudo, que era
     * el más común y el único que no afirma agudeza.
     */
    public function down(): void
    {
        DB::table('tamizajes')
            ->where('nivel_suicidio', ResponderTamizaje::SUICIDIO_POSITIVO)
            ->update(['nivel_suicidio' => 'Positivo: requiere valoración posterior']);
    }
};
