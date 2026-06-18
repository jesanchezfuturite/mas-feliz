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
        Schema::table('autoevaluacions', function (Blueprint $table) {
            for ($i = 1; $i <= 25; $i++) {
                $table->dropColumn("criterio_{$i}");
            }
            $table->json('respuestas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autoevaluacions', function (Blueprint $table) {
            $table->dropColumn('respuestas');
            for ($i = 1; $i <= 25; $i++) {
                $table->string("criterio_{$i}")->nullable();
            }
        });
    }
};
