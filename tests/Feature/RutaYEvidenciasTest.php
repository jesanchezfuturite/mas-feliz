<?php

namespace Tests\Feature;

use App\Models\Autoevaluacion;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Puntos 8 y 9 de Angélica (04/08/2026): el acompañante debe poder mover la
 * Ruta Crítica, y las evidencias deben verse tanto para la organización que
 * las cargó como para el evaluador.
 */
class RutaYEvidenciasTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    private User $evaluador;

    protected function setUp(): void
    {
        parent::setUp();

        // El módulo de herramientas de la empresa está detrás del interruptor global.
        \App\Models\Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Ruta',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => 'ruta@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 15,
            'paso_certificacion' => 2,
        ]);

        $this->evaluador = User::create([
            'name' => 'Acompañante',
            'apellidos' => 'De Prueba',
            'email' => 'acompanante@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'evaluador',
        ]);

        $this->evaluador->empresas()->attach($this->empresa->id);
    }

    public function test_las_fases_de_la_ruta_critica_son_las_mismas_en_todos_lados(): void
    {
        $this->assertCount(6, Empresa::PASOS_CERTIFICACION);
        $this->assertSame('Registro', Empresa::PASOS_CERTIFICACION[1]);
        $this->assertSame('Reconocimiento acorde al nivel de Madurez', Empresa::PASOS_CERTIFICACION[6]);

        // El desplegable numera las mismas fases, sin poder desincronizarse.
        $this->assertSame('3. Retroalimentación y Acompañamiento', Empresa::opcionesPasoCertificacion()[3]);
        $this->assertSame(
            array_keys(Empresa::PASOS_CERTIFICACION),
            array_keys(Empresa::opcionesPasoCertificacion()),
        );
    }

    public function test_el_evaluador_tiene_disponible_la_accion_de_actualizar_fase(): void
    {
        $this->actingAs($this->evaluador, 'web');

        $this->get('/evaluador/empresas')
            ->assertSuccessful()
            ->assertSee('Empresa Ruta')
            ->assertSee('Actualizar Fase');
    }

    public function test_la_fase_que_guarda_el_evaluador_es_la_que_ve_la_empresa(): void
    {
        $this->empresa->update(['paso_certificacion' => 4]);

        $this->actingAs($this->empresa, 'empresa');

        $this->get('/tablero')
            ->assertSuccessful()
            // La línea de tiempo del escritorio toma las etiquetas del modelo.
            ->assertSee('Plan de acción/Implementación');
    }

    public function test_el_enlace_de_la_evidencia_no_depende_de_app_url(): void
    {
        // Si APP_URL apunta a otro dominio, los enlaces absolutos se rompen.
        config(['app.url' => 'http://dominio-equivocado.test']);

        Storage::fake('public');
        Storage::disk('public')->put('autoevaluacion-evidencias/politica.pdf', 'contenido');

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $this->empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => [
                'criterio_1' => ['elemento_1' => ['archivo' => 'autoevaluacion-evidencias/politica.pdf', 'score' => '10']],
            ],
        ]);

        $this->actingAs($this->evaluador, 'web');

        $respuesta = $this->get('/evaluador/autoevaluacions/' . $autoevaluacion->id);

        $respuesta->assertSuccessful()
            ->assertSee('/storage/autoevaluacion-evidencias/politica.pdf', false)
            ->assertDontSee('dominio-equivocado.test', false);
    }

    public function test_la_empresa_ve_su_propia_evidencia_en_borrador(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('autoevaluacion-evidencias/mi-evidencia.pdf', 'contenido');

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $this->empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [
                'criterio_1' => ['elemento_1' => ['archivo' => 'autoevaluacion-evidencias/mi-evidencia.pdf']],
            ],
        ]);

        $this->actingAs($this->empresa, 'empresa');

        // El distintivo de evidencia acompaña al criterio en la propia pantalla.
        $this->get('/tablero/autoevaluacions/' . $autoevaluacion->id . '/edit')
            ->assertSuccessful()
            ->assertSee('/storage/autoevaluacion-evidencias/mi-evidencia.pdf', false);
    }

    public function test_el_diagnostico_distingue_evidencias_sanas_rotas_y_huerfanas(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('autoevaluacion-evidencias/sana.pdf', 'ok');
        Storage::disk('public')->put('autoevaluacion-evidencias/huerfana.pdf', 'nadie me referencia');

        Autoevaluacion::create([
            'empresa_id' => $this->empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [
                'criterio_1' => ['elemento_1' => ['archivo' => 'autoevaluacion-evidencias/sana.pdf']],
                'criterio_2' => ['elemento_1' => ['archivo' => 'autoevaluacion-evidencias/no-existe.pdf']],
            ],
        ]);

        $this->artisan('evidencias:diagnosticar')
            ->expectsOutputToContain('Archivos en disco')
            ->expectsOutputToContain('huerfana.pdf')
            ->expectsOutputToContain('no-existe.pdf')
            ->assertSuccessful();
    }
}
