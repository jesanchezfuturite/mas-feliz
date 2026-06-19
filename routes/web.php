<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/diagnostico/{token}', \App\Livewire\ResponderTamizaje::class)->name('tamizaje.publico');

Route::get('/preview-email', function () {
    $empresa = \App\Models\Empresa::first() ?? new \App\Models\Empresa([
        'folio' => 'MF-2026-0001',
        'nombre_empresa' => 'Mi Organización de Prueba S.A.',
        'nombre_responsable' => 'Juan Pérez',
        'correo' => 'juan.perez@ejemplo.com',
    ]);
    return new \App\Mail\AccesosTableroEmpresa($empresa, 'PasswordTemporal123!');
});
