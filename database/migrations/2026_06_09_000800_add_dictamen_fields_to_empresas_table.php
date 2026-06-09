<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('estatus_distintivo', 30)->default('En revisión')->after('numero_trabajadores');
            $table->string('nivel_madurez_asignado', 30)->nullable()->after('estatus_distintivo');
            $table->text('retroalimentacion_gobierno')->nullable()->after('nivel_madurez_asignado');
            $table->timestamp('fecha_dictamen')->nullable()->after('retroalimentacion_gobierno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['estatus_distintivo', 'nivel_madurez_asignado', 'retroalimentacion_gobierno', 'fecha_dictamen']);
        });
    }
};
