<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campo abierto que pidió Angélica el 10/09/2026 para el formato de
     * referencia: la descripción general de la situación o necesidad por la
     * que se refiere a la persona. Es texto libre porque el catálogo de
     * "Servicio que solicita" no alcanza a explicar el contexto del caso.
     */
    public function up(): void
    {
        Schema::table('solicitudes_referencia', function (Blueprint $table) {
            $table->text('motivo_referencia')->nullable()->after('servicio_solicitado');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_referencia', function (Blueprint $table) {
            $table->dropColumn('motivo_referencia');
        });
    }
};
