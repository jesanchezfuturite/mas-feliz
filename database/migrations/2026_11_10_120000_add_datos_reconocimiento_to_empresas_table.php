<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos que la empresa confirma para su reconocimiento (Angélica, 31/10/2026):
 * el nombre tal cual debe ir impreso, el logotipo en alta resolución y los
 * contactos del Director/Gerente y del enlace para las invitaciones de RP.
 *
 * El nombre del director, el del enlace y el cargo del enlace ya existían en
 * la tabla; aquí solo entra lo que faltaba.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('nombre_reconocimiento')->nullable()->after('nombre_empresa');
            $table->string('logo_reconocimiento')->nullable()->after('nombre_reconocimiento');
            $table->string('cargo_director')->nullable()->after('nombre_director');
            $table->string('celular_director', 20)->nullable()->after('cargo_director');
            $table->string('celular_enlace', 20)->nullable()->after('cargo_enlace');
            // Vacío = la empresa todavía no confirma; es lo que mantiene el
            // candado sobre su resultado.
            $table->timestamp('reconocimiento_confirmado_at')->nullable()->after('fecha_dictamen');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_reconocimiento',
                'logo_reconocimiento',
                'cargo_director',
                'celular_director',
                'celular_enlace',
                'reconocimiento_confirmado_at',
            ]);
        });
    }
};
