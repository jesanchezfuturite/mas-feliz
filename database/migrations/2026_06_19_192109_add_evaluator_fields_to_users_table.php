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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->nullable()->after('name');
            $table->string('telefono')->nullable()->after('email');
            $table->boolean('estatus')->default(true)->after('password');
            $table->string('role')->default('admin')->after('estatus');
        });

        // Copy existing evaluadores to users if the table exists
        if (Schema::hasTable('evaluadores')) {
            $evaluadores = DB::table('evaluadores')->get();
            foreach ($evaluadores as $evaluador) {
                DB::table('users')->insert([
                    'name' => $evaluador->nombres,
                    'apellidos' => $evaluador->apellidos,
                    'email' => $evaluador->correo,
                    'telefono' => $evaluador->telefono,
                    'password' => $evaluador->password,
                    'estatus' => $evaluador->estatus ?? true,
                    'role' => 'evaluador',
                    'created_at' => $evaluador->created_at,
                    'updated_at' => $evaluador->updated_at,
                ]);
            }
            Schema::dropIfExists('evaluadores');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apellidos', 'telefono', 'estatus', 'role']);
        });
    }
};
