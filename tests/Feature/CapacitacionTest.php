<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\MaterialApoyo;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Petición de Angélica del 04/08/2026 sobre la página de Capacitación:
 * el criterio 10 es indispensable (no "necesario"), se quitan los módulos
 * escritos a mano en la plantilla y en su lugar ella publica material y
 * avisos de fechas desde el panel de administración.
 */
class CapacitacionTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Capacitación',
            'municipio' => 'Piedras Negras',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'capacitacion@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 30,
        ]);

        $this->actingAs($this->empresa, 'empresa');
    }

    public function test_el_criterio_10_se_muestra_como_indispensable(): void
    {
        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertSee('Criterio 10 (Indispensable)', false)
            ->assertDontSee('Criterio 10 (Necesario)', false);
    }

    public function test_ya_no_aparecen_los_modulos_escritos_en_la_plantilla(): void
    {
        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertDontSee('Talleres y Webinars Programados')
            ->assertDontSee('Liderazgo Positivo y Sensibilización en Salud Mental')
            ->assertDontSee('Detección Oportuna de Señales de Alerta');
    }

    public function test_sin_contenido_publicado_se_avisa_a_la_empresa(): void
    {
        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertSee('Aún no hay fechas de capacitación publicadas', false);
    }

    public function test_los_avisos_de_fecha_se_publican_en_la_pagina(): void
    {
        MaterialApoyo::create([
            'titulo' => 'Taller de liderazgo positivo',
            'descripcion' => 'Duración 2 horas. Sede: Oficina Inspira.',
            'tipo' => 'aviso',
            'seccion' => 'capacitacion',
            'fecha_evento' => now()->addWeek()->setTime(10, 0),
            'activo' => true,
        ]);

        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertSee('Taller de liderazgo positivo')
            ->assertSee('Duración 2 horas. Sede: Oficina Inspira.')
            ->assertDontSee('Aún no hay fechas de capacitación publicadas', false);
    }

    public function test_el_material_de_apoyo_se_publica_por_separado_de_los_avisos(): void
    {
        MaterialApoyo::create([
            'titulo' => 'Guía de señales de alerta',
            'tipo' => 'pdf',
            'seccion' => 'capacitacion',
            'archivo_path' => 'material-apoyo/guia.pdf',
            'activo' => true,
        ]);

        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertSee('Material de apoyo')
            ->assertSee('Guía de señales de alerta')
            ->assertSee('Descargar PDF');
    }

    public function test_el_admin_ve_los_materiales_con_su_seccion_y_fecha(): void
    {
        MaterialApoyo::create([
            'titulo' => 'Taller de liderazgo positivo',
            'tipo' => 'aviso',
            'seccion' => 'capacitacion',
            'fecha_evento' => now()->addWeek()->setTime(10, 0),
            'activo' => true,
        ]);

        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'apellidos' => 'Capacitación',
            'email' => 'admin.capacitacion@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'web');

        $this->get('/admin/material-apoyos')
            ->assertSuccessful()
            ->assertSee('Taller de liderazgo positivo')
            ->assertSee('Capacitación');
    }

    public function test_el_contenido_de_otras_secciones_no_se_filtra_a_capacitacion(): void
    {
        MaterialApoyo::create([
            'titulo' => 'Material solo de prevención',
            'tipo' => 'pdf',
            'seccion' => 'prevencion_promocion',
            'archivo_path' => 'material-apoyo/prevencion.pdf',
            'activo' => true,
        ]);

        MaterialApoyo::create([
            'titulo' => 'Aviso desactivado',
            'tipo' => 'aviso',
            'seccion' => 'capacitacion',
            'fecha_evento' => now()->addWeek(),
            'activo' => false,
        ]);

        $this->get('/tablero/capacitacion')
            ->assertSuccessful()
            ->assertDontSee('Material solo de prevención')
            ->assertDontSee('Aviso desactivado');
    }
}
