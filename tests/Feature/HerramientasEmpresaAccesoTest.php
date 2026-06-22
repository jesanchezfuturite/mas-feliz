<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Filament\Facades\Filament;

class HerramientasEmpresaAccesoTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Acceso Test',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'acceso@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_when_tools_are_disabled_company_dashboard_shows_waiting_message_and_tools_return_403(): void
    {
        // Ensure tools are disabled (default is false)
        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => false]
        );

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        // Visit Dashboard
        $response = $this->get('/tablero');
        $response->assertStatus(200);
        $response->assertSee('Muchas gracias por tu registro al distintivo +Feliz.');

        // Try to visit Autoevaluaciones (should get 403)
        $responseAutoeval = $this->get('/tablero/autoevaluacions');
        $responseAutoeval->assertStatus(403);

        // Try to visit Tamizajes (should get 403)
        $responseTamizajes = $this->get('/tablero/tamizajes');
        $responseTamizajes->assertStatus(403);
    }

    public function test_when_tools_are_enabled_company_dashboard_does_not_show_waiting_message_and_tools_are_accessible(): void
    {
        // Enable tools
        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => true]
        );

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        // Visit Dashboard
        $response = $this->get('/tablero');
        $response->assertStatus(200);
        $response->assertDontSee('Muchas gracias por tu registro al distintivo +Feliz.');

        // Visit Autoevaluaciones (should be 200)
        $responseAutoeval = $this->get('/tablero/autoevaluacions');
        $responseAutoeval->assertStatus(200);

        // Visit Tamizajes (should be 200)
        $responseTamizajes = $this->get('/tablero/tamizajes');
        $responseTamizajes->assertStatus(200);
    }
}
