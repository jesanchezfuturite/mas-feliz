<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Angélica pidió el 04/08/2026 quitar la "Lista de Autoverificación para
 * Criterio 9" de Prevención/Promoción. Eran checkboxes decorativos, sin
 * wire:model ni persistencia. Esta prueba evita que se reintroduzcan.
 */
class PrevencionPromocionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_ya_no_muestra_la_lista_de_autoverificacion(): void
    {
        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Prevención',
            'municipio' => 'Monclova',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'prevencion@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 20,
        ]);

        $this->actingAs($empresa, 'empresa');

        $respuesta = $this->get('/tablero/prevencion-promocion');

        $respuesta->assertSuccessful()
            ->assertDontSee('Lista de Autoverificación')
            ->assertDontSee('¿El programa está documentado y firmado por el Comité de Salud Mental?', false)
            // El resto de la página se conserva.
            ->assertSee('Materiales de Apoyo')
            ->assertSee('Criterio 9', false);
    }
}
