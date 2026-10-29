<?php

namespace Tests\Feature;

use App\Filament\Empresa\Widgets\DictamenWidget;
use App\Models\Empresa;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La empresa ve el resultado de su dictaminación en su escritorio.
 *
 * Angélica (29/10/2026): el correo de resultados las invita a «ingresar a la
 * plataforma y consultar el nivel de madurez alcanzado», pero no veían ni el
 * estatus, ni el nivel, ni el comentario que les puso el Comité.
 */
class DictamenEmpresaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Organización Prueba Dictamen',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'dictamen.empresa@test.com',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 25,
        ]);

        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => true]
        );

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));
    }

    private function dictaminar(array $datos = []): void
    {
        $this->empresa->update(array_merge([
            'estatus_distintivo' => 'Aprobado',
            'nivel_madurez_asignado' => 'Intermedio',
            'fecha_dictamen' => '2026-10-28 10:00:00',
            'retroalimentacion_gobierno' => 'Excelente trabajo en prevención.',
        ], $datos));
    }

    public function test_la_empresa_ve_su_dictamen_en_el_escritorio(): void
    {
        $this->dictaminar();

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Resultado de la Dictaminación')
            ->assertSee('Aprobado')
            ->assertSee('Intermedio')
            ->assertSee('Comentario')
            ->assertSee('Excelente trabajo en prevención.')
            ->assertSee('28/10/2026');
    }

    public function test_sin_dictamen_el_escritorio_no_muestra_la_tarjeta(): void
    {
        $this->assertFalse($this->empresa->tieneDictamen());
        $this->assertFalse(DictamenWidget::canView());

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertDontSee('Resultado de la Dictaminación');
    }

    public function test_una_empresa_no_aprobada_ve_su_estatus_y_su_comentario(): void
    {
        $this->dictaminar([
            'estatus_distintivo' => 'No aprobado',
            'nivel_madurez_asignado' => null,
            'retroalimentacion_gobierno' => 'Falta participación en el tamizaje.',
        ]);

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('No aprobado')
            ->assertSee('Falta participación en el tamizaje.');
    }

    public function test_el_estatus_viejo_validado_se_le_muestra_como_aprobado(): void
    {
        // 'Validado' lo pone la validación de la autoevaluación.
        $this->dictaminar(['estatus_distintivo' => 'Validado']);

        $this->assertSame('Aprobado', Empresa::etiquetaEstatusDistintivo('Validado'));

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Aprobado')
            ->assertDontSee('Validado');
    }

    public function test_el_dictamen_se_ve_aunque_las_herramientas_esten_apagadas(): void
    {
        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => false]
        );

        $this->dictaminar();

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Muchas gracias por tu registro al distintivo +Feliz.')
            ->assertSee('Resultado de la Dictaminación')
            ->assertSee('Intermedio');
    }

    public function test_un_dictamen_a_medias_tambien_cuenta(): void
    {
        $this->empresa->update(['retroalimentacion_gobierno' => 'Pendiente de visita.']);

        $this->assertTrue($this->empresa->fresh()->tieneDictamen());
    }
}
