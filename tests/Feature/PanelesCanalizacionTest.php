<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\DetalleCasoForm;
use App\Filament\Gestor\Resources\SolicitudReferencias\Pages\ManageSolicitudReferencias;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Humo de las pantallas nuevas: que carguen de verdad, con datos, para cada
 * perfil, y que el aislamiento entre paneles se respete.
 *
 * Nota: el contenido de las modales no viaja en la respuesta de la prueba
 * (Filament v5 las renderiza aparte), así que lo que se revisa de ellas es su
 * esquema, armado con la página real del panel.
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
            'email' => $role.'.paneles@test.com',
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
        $this->assertFalse($gestor->fresh()->canAccessPanel(Filament::getPanel('gestor')));
    }

    /**
     * Texto visible del detalle del caso, armado con la página real del panel
     * del Gestor (el esquema necesita un componente Livewire vivo).
     */
    private function textoDelDetalle($record): string
    {
        $esquema = Schema::make(Livewire::test(ManageSolicitudReferencias::class)->instance())
            ->components(DetalleCasoForm::componentes(conEmpresa: true))
            ->record($record);

        return collect($esquema->getFlatComponents())
            ->map(function ($componente) {
                if ($componente instanceof Placeholder) {
                    $contenido = $componente->getContent();

                    return $contenido instanceof HtmlString ? $contenido->toHtml() : (string) $contenido;
                }

                return method_exists($componente, 'getLabel') ? (string) $componente->getLabel() : '';
            })
            ->implode("\n");
    }

    /**
     * Angélica, 11/09/2026: "que el perfil de gestor pueda ver todos los datos
     * de los usuarios que le mandan (los que salen en el apartado de
     * Atención)". El detalle se abre desde la referencia, que es donde el
     * Gestor trabaja.
     */
    public function test_el_gestor_ve_todos_los_datos_de_la_persona_referida(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Persona Canalizada',
            'consentimiento_otorgado' => true,
            'genero' => 'Mujer',
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Administrativas',
            'tiempo_trabajando' => 'Más de 5 años',
            'telefono' => '8441112233',
            'correo' => 'persona@empresa.test',
            'riesgo_ansiedad' => 16,
            'nivel_ansiedad' => 'Grave',
            'riesgo_depresion' => 12,
            'nivel_depresion' => 'Moderada',
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Positivo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $caso = CasoSeguimiento::where('identificador_empleado', 'Persona Canalizada')->first();
        $caso->update(['notas_clinicas' => 'Refiere crisis de ansiedad recurrentes.']);

        $this->actingAs($this->crearUsuario('gestor'), 'web');
        Filament::setCurrentPanel(Filament::getPanel('gestor'));

        // Entrando por la solicitud, que es como lo abre el Gestor.
        $texto = $this->textoDelDetalle($caso->solicitudReferencia);

        // De qué organización viene: las pantallas de gobierno cruzan empresas.
        $this->assertStringContainsString('Empresa Paneles', $texto);

        // Identificación y contacto.
        $this->assertStringContainsString('Persona Canalizada', $texto);
        $this->assertStringContainsString('Mujer', $texto);
        $this->assertStringContainsString('25 a 34 años', $texto);
        $this->assertStringContainsString('Administrativas', $texto);
        $this->assertStringContainsString('8441112233', $texto);
        $this->assertStringContainsString('persona@empresa.test', $texto);

        // Resultados del tamizaje.
        $this->assertStringContainsString('Síntomas de Ansiedad: Grave', $texto);
        $this->assertStringContainsString('Síntomas de Depresión: Moderada', $texto);
        $this->assertStringContainsString('Indicadores de Conducta suicida: Positivo', $texto);

        // Seguimiento que capturó la empresa en Atención.
        $this->assertStringContainsString('Psicología', $texto);
        $this->assertStringContainsString('Refiere crisis de ansiedad recurrentes.', $texto);
        $this->assertStringContainsString('Canalizado', $texto);

        // Y en qué va su referencia.
        $this->assertStringContainsString($caso->solicitudReferencia->folio, $texto);
    }

    /** El mismo detalle se abre desde el listado de casos canalizados. */
    public function test_el_detalle_tambien_funciona_desde_el_caso(): void
    {
        $caso = CasoSeguimiento::where('identificador_empleado', 'Persona Canalizada')->first();

        $this->actingAs($this->crearUsuario('gestor'), 'web');
        Filament::setCurrentPanel(Filament::getPanel('gestor'));

        $texto = $this->textoDelDetalle($caso);

        $this->assertStringContainsString('Persona Canalizada', $texto);
        $this->assertStringContainsString('Empresa Paneles', $texto);
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
