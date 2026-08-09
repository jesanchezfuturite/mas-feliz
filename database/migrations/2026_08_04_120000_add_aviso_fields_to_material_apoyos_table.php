<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Angélica pidió poder publicar en Capacitación tanto material como avisos
     * de fechas, en lugar de los módulos fijos que estaban escritos en la
     * plantilla. Un aviso no tiene archivo ni enlace: solo título, descripción
     * y la fecha del evento.
     */
    public function up(): void
    {
        Schema::table('material_apoyos', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('titulo');
            $table->timestamp('fecha_evento')->nullable()->after('enlace_url');
        });
    }

    public function down(): void
    {
        Schema::table('material_apoyos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'fecha_evento']);
        });
    }
};
