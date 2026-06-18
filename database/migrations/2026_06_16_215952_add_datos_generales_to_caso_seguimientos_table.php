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
        Schema::table('caso_seguimientos', function (Blueprint $table) {
            $table->string('genero')->nullable();
            $table->string('edad')->nullable();
            $table->string('actividad_trabajo')->nullable();
            $table->string('actividad_trabajo_otra')->nullable();
            $table->string('tiempo_trabajando')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caso_seguimientos', function (Blueprint $table) {
            $table->dropColumn([
                'genero',
                'edad',
                'actividad_trabajo',
                'actividad_trabajo_otra',
                'tiempo_trabajando'
            ]);
        });
    }
};
