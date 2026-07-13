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
        Schema::create('material_apoyos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('tipo'); // pdf, imagen, video, enlace
            $table->string('archivo_path')->nullable();
            $table->string('enlace_url')->nullable();
            $table->string('seccion')->default('prevencion_promocion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_apoyos');
    }
};
