<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Angélica pidió el 06/08/2026 quitar "SOMOS+" del nombre del campo. Las
     * opciones que definió describen el avance de la cita (confirmó, acudió,
     * reagendó, ruta alterna, notificación a la empresa), así que la columna
     * pasa a llamarse estatus_cita.
     */
    public function up(): void
    {
        Schema::table('solicitudes_referencia', function (Blueprint $table) {
            $table->renameColumn('estatus_somos', 'estatus_cita');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_referencia', function (Blueprint $table) {
            $table->renameColumn('estatus_cita', 'estatus_somos');
        });
    }
};
