<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Interruptor de resultados del tamizaje, ahora también por empresa:
     * algunas instituciones de gobierno pidieron no tener visibilidad de sus
     * resultados, así que el admin puede encender el interruptor global y
     * apagarlo solo a empresas específicas desde el listado de Empresas.
     *
     * Arranca en `true` para no cambiar el comportamiento de ninguna empresa al
     * desplegar: el global sigue mandando y esto solo restringe.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('resultados_tamizaje_visibles')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('resultados_tamizaje_visibles');
        });
    }
};
