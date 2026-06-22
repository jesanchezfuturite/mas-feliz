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
            $table->string('rfc', 15)->nullable()->after('nombre_empresa');
            $table->string('ambito', 50)->nullable()->after('rfc');
            $table->string('domicilio', 255)->nullable()->after('ambito');
            $table->string('cargo_enlace', 255)->nullable()->after('nombre_responsable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['rfc', 'ambito', 'domicilio', 'cargo_enlace']);
        });
    }
};
