<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Interruptor para ocultar el listado de resultados del tamizaje por
     * individuo en el tablero de la empresa, dejando visible solo la vista
     * general. Angélica lo pidió mientras se aclara en reunión cómo debe
     * interpretarse el resultado del instrumento.
     *
     * Arranca en `true` para no cambiar el comportamiento de ningún ambiente al
     * desplegar: se apaga desde Configuración General donde haga falta.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('resultados_tamizaje_visibles')
                ->default(true)
                ->after('herramientas_empresa_activas');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('resultados_tamizaje_visibles');
        });
    }
};
