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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->string('nombre_empresa');
            $table->string('municipio');
            $table->string('dias_horario_servicio');
            $table->string('nombre_director');
            $table->string('nombre_responsable');
            $table->string('correo')->unique();
            $table->string('telefono');
            $table->string('rubro');
            $table->integer('numero_trabajadores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
