<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Formato de referencia complementaria a Secretaría de Salud. La empresa lo
     * llena desde el caso de seguimiento; el Gestor (trabajador social) y el
     * admin son quienes asignan la cita y la unidad de atención.
     */
    public function up(): void
    {
        Schema::create('solicitudes_referencia', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();

            $table->foreignId('caso_seguimiento_id')->constrained('caso_seguimientos')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->string('municipio');
            $table->string('jurisdiccion')->nullable();
            $table->string('nivel_riesgo', 30)->nullable();

            // Datos de la persona referida
            $table->string('nombre_usuario');
            $table->string('sexo', 20)->nullable();
            $table->string('edad', 30)->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('telefono_contacto', 20)->nullable();
            $table->string('ine_path')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('derechohabiencia')->nullable();
            $table->string('servicio_solicitado')->nullable();
            $table->string('informe_valoracion_path')->nullable();

            // Lo llena Salud / Gestor y lo ve la empresa
            $table->string('estatus_somos')->nullable();
            $table->timestamp('fecha_cita')->nullable();
            $table->string('unidad_atencion')->nullable();
            $table->string('unidad_atencion_otra')->nullable();
            $table->foreignId('asignada_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_referencia');
    }
};
