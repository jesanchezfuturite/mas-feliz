<?php

namespace Tests\Feature;

use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\SolicitudReferencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Humo de las pantallas nuevas: que carguen de verdad, con datos, para cada
 * perfil, y que el aislamiento entre paneles se respete.
 *
 * Nota: las interacciones (abrir modales, ejecutar acciones) no se pueden
 * probar en este proyecto porque los helpers de Filament dependen de
 * Livewire\Testable::instance(), que Livewire 4 devuelve como null. Es la
 * misma causa de los 13 tests que ya venían fallando.
 */
class PanelesCanalizacionTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Paneles',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'paneles@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 40,
        ]);

        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Canalizada',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'Canalizado',
            'servicios' => ['Psicología'],
            'consentimiento' => true,
            'referencia_secretaria_salud' => true,
        ]);

        SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Canalizada',
            'nivel_riesgo' => 'Urgente',
        ]);
    }

    private function crearUsuario(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'apellidos' => 'De Prueba',
            'email' => $role . '.paneles@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => $role,
        ]);
    }

    public function test_el_gestor_entra_a_su_panel_y_ve_las_referencias(): void
    {
        $this->actingAs($this->crearUsuario('gestor'), 'web');

        $this->get('/gestor/referencias')
            ->assertSuccessful()
            ->assertSee('Persona Canalizada');

        $this->get('/gestor/casos-canalizados')
            ->assertSuccessful()
            ->assertSee('Empresa Paneles');
    }

    public function test_el_admin_tambien_ve_referencias_y_canalizados(): void
    {
        $this->actingAs($this->crearUsuario('admin'), 'web');

        $this->get('/admin/referencias')->assertSuccessful()->assertSee('Persona Canalizada');
        $this->get('/admin/casos-canalizados')->assertSuccessful()->assertSee('Persona Canalizada');
        $this->get('/admin/gestores')->assertSuccessful();
    }

    public function test_el_evaluador_no_entra_al_panel_del_gestor(): void
    {
        $this->actingAs($this->crearUsuario('evaluador'), 'web');

        $this->get('/gestor/referencias')->assertForbidden();
    }

    public function test_un_gestor_desactivado_pierde_el_acceso(): void
    {
        $gestor = $this->crearUsuario('gestor');
        $gestor->update(['estatus' => false]);

        $this->actingAs($gestor, 'web');

        // canAccessPanel() exige rol y estatus activo.
        $this->assertFalse($gestor->fresh()->canAccessPanel(\Filament\Facades\Filament::getPanel('gestor')));
    }

    public function test_la_empresa_ve_su_caso_con_las_columnas_nuevas(): void
    {
        // El módulo de herramientas está detrás del interruptor global.
        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->actingAs($this->empresa, 'empresa');

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('Persona Canalizada')
            ->assertSee('Psicología')
            ->assertSee('Consentimiento');
    }
}
