<?php

use App\Support\PrioridadAtencion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Escala de prioridad de atención del 21/08/2026.
 *
 * Angélica renombró "Riesgo General" a "Prioridad de atención" y con ello
 * cambió la escala: el nivel intermedio pasa a concordar en femenino
 * ("Moderada", porque califica a la prioridad, no al riesgo) y se agregan
 * "Alta" y "Agudeza pendiente de confirmar".
 *
 * Las tres columnas se migran juntas a propósito. `nivel_riesgo_detectado` del
 * caso se copia del tamizaje al abrirlo y `nivel_riesgo` de la solicitud se
 * copia del caso; si cada una hablara su propio idioma, la gráfica que suma
 * tamizajes con casos capturados a mano contaría dos veces lo mismo con
 * nombres distintos.
 *
 * Las columnas eran VARCHAR(30) y "Agudeza pendiente de confirmar" mide
 * exactamente 30 caracteres: se ensanchan a 60 para no dejar el valor al
 * borde del truncado.
 */
return new class extends Migration
{
    /** Columna de nivel de cada tabla involucrada. */
    private const COLUMNAS = [
        'tamizajes' => 'nivel_riesgo_general',
        'caso_seguimientos' => 'nivel_riesgo_detectado',
        'solicitudes_referencia' => 'nivel_riesgo',
    ];

    public function up(): void
    {
        foreach (self::COLUMNAS as $tabla => $columna) {
            $this->ensanchar($tabla, $columna);

            DB::table($tabla)
                ->where($columna, 'Moderado')
                ->update([$columna => PrioridadAtencion::MODERADA]);
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNAS as $tabla => $columna) {
            // "Alta" y "Agudeza pendiente de confirmar" no existían antes de
            // esta escala; lo más cercano hacia atrás es el nivel intermedio.
            DB::table($tabla)
                ->whereIn($columna, [PrioridadAtencion::MODERADA, PrioridadAtencion::ALTA, PrioridadAtencion::AGUDEZA_PENDIENTE])
                ->update([$columna => 'Moderado']);
        }
    }

    private function ensanchar(string $tabla, string $columna): void
    {
        $nullable = $tabla === 'solicitudes_referencia';

        Schema::table($tabla, function (Blueprint $table) use ($columna, $nullable) {
            $definicion = $table->string($columna, 60);

            if ($nullable) {
                $definicion->nullable();
            }

            $definicion->change();
        });
    }
};
