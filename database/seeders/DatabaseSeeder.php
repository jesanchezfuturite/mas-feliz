<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el usuario Administrador para el panel /admin
        User::updateOrCreate(
            ['email' => 'admin@inspira.gob.mx'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Crear la empresa por defecto para el panel /tablero
        Empresa::updateOrCreate(
            ['correo' => 'enrique.sanchez@futurite.com'],
            [
                'nombre_empresa' => 'Agencia de Marketing Digital Futurite',
                'password' => Hash::make('password'),
                'municipio' => 'Monterrey',
                'dias_horario_servicio' => 'Lunes a Viernes 9am - 6pm',
                'nombre_director' => 'Director',
                'nombre_responsable' => 'Responsable',
                'telefono' => '1234567890',
                'rubro' => 'Marketing',
                'numero_trabajadores' => 10,
                'token_tamizaje' => Str::random(32),
            ]
        );
    }
}
