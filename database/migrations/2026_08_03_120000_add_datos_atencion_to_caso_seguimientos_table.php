<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columnas solicitadas por Salud para el listado de personas en riesgo
     * (documento "ATENCIÓN EMPRESAS +FELIZ"): datos de contacto, consentimiento
     * y el tipo de servicio que requiere la persona.
     */
    public function up(): void
    {
        Schema::table('caso_seguimientos', function (Blueprint $table) {
            $table->string('correo')->nullable()->after('tiempo_trabajando');
            $table->string('celular', 20)->nullable()->after('correo');

            // El consentimiento del tamizaje es anónimo; este es el consentimiento
            // explícito de la persona para ser contactada y atendida.
            $table->boolean('consentimiento')->nullable()->after('celular');

            // Medicina / Psicología / Psiquiatría / Otro. Se guarda como arreglo
            // porque una misma persona puede requerir más de un servicio.
            $table->json('servicios')->nullable()->after('consentimiento');
            $table->string('servicio_otro')->nullable()->after('servicios');

            $table->boolean('referencia_secretaria_salud')->default(false)->after('servicio_otro');
        });
    }

    public function down(): void
    {
        Schema::table('caso_seguimientos', function (Blueprint $table) {
            $table->dropColumn([
                'correo',
                'celular',
                'consentimiento',
                'servicios',
                'servicio_otro',
                'referencia_secretaria_salud',
            ]);
        });
    }
};
