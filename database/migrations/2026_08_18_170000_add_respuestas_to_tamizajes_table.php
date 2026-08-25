<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hasta ahora solo se guardaba la ponderación (los puntajes sumados), así
     * que revisar un caso concreto obligaba a pedirle capturas de pantalla al
     * colaborador. Pasó el 18/08/2026 al revisar por qué el tamizaje marcaba
     * urgentes a personas sin riesgo actual. Con las respuestas ítem por ítem
     * cualquier resultado se puede auditar contra lo que la persona contestó.
     */
    public function up(): void
    {
        Schema::table('tamizajes', function (Blueprint $table) {
            $table->json('respuestas')->nullable()->after('nivel_riesgo_general');
        });
    }

    public function down(): void
    {
        Schema::table('tamizajes', function (Blueprint $table) {
            $table->dropColumn('respuestas');
        });
    }
};
