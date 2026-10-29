<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El estatus del distintivo con los nombres que pidió Angélica (28/10/2026).
 *
 * Su lista para el dictamen es "Aprobado, No aprobado, En revisión". La
 * plataforma guardaba `Rechazado`, que es la misma idea con otro nombre y el
 * único de los tres que no coincidía:
 *
 *   Rechazado ... → No aprobado
 *
 * Va como migración y no como un cambio de etiqueta en pantalla a propósito:
 * el valor guardado se muestra tal cual en las tablas del evaluador y del
 * tablero de avance, así que renombrar solo el desplegable dejaría media
 * plataforma diciendo "Rechazado". El despliegue en cPanel ya corre
 * `migrate --force`, de modo que es un paso manual menos.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('empresas')
            ->where('estatus_distintivo', 'Rechazado')
            ->update(['estatus_distintivo' => 'No aprobado']);
    }

    public function down(): void
    {
        DB::table('empresas')
            ->where('estatus_distintivo', 'No aprobado')
            ->update(['estatus_distintivo' => 'Rechazado']);
    }
};
