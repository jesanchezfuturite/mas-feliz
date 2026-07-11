<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Usuarios de prueba para cada tenant/panel.
     * Password compartido: kike9800
     */
    public function run(): void
    {
        $password = 'kike9800';

        // 1. Panel /admin  -> User (role admin)
        User::updateOrCreate(
            ['email' => 'admin.test@masfeliz.test'],
            [
                'name' => 'Admin Prueba',
                'password' => Hash::make($password),
                'estatus' => true,
                'role' => 'admin',
            ]
        );

        // 2. Panel /evaluador  -> User (role evaluador)
        User::updateOrCreate(
            ['email' => 'evaluador.test@masfeliz.test'],
            [
                'name' => 'Evaluador Prueba',
                'password' => Hash::make($password),
                'estatus' => true,
                'role' => 'evaluador',
            ]
        );

        // 3. Panel /tablero  -> Empresa (login por 'correo', password NO se castea a hashed)
        Empresa::updateOrCreate(
            ['correo' => 'empresa.test@masfeliz.test'],
            [
                'nombre_empresa' => 'Empresa de Prueba +Feliz',
                'password' => Hash::make($password),
                'municipio' => 'Monterrey',
                'dias_horario_servicio' => 'Lunes a Viernes 9am - 6pm',
                'nombre_director' => 'Director Prueba',
                'nombre_responsable' => 'Responsable Prueba',
                'telefono' => '8100000000',
                'rubro' => 'Pruebas',
                'numero_trabajadores' => 25,
            ]
        );
    }
}
