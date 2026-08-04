<?php

namespace Tests\Feature;

use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use App\Models\User;
use App\Support\MetricasAtencion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Punto 7 de Angélica: evaluadores y administración necesitan métricas y
 * estatus de los casos para dar seguimiento, sin ver datos de los trabajadores.
 */
class MetricasAtencionTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresaA;

    private Empresa $empresaB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresaA = $this->crearEmpresa('Empresa A', 'a@empresa.test');
        $this->empresaB = $this->crearEmpresa('Empresa B', 'b@empresa.test');

        // Empresa A: 3 tamizajes (uno declinó), 1 en riesgo urgente.
        $this->crearTamizaje($this->empresaA, 'Urgente');
        $this->crearTamizaje($this->empresaA, 'Leve');
        $this->crearTamizaje($this->empresaA, 'No participó');

        // Empresa B: 1 tamizaje moderado.
        $this->crearTamizaje($this->empresaB, 'Moderado');

        // Casos: A tiene uno en seguimiento y uno canalizado; B uno cerrado.
        CasoSeguimiento::create(['empresa_id' => $this->empresaA->id, 'identificador_empleado' => 'P1', 'nivel_riesgo_detectado' => 'Urgente', 'estatus_atencion' => 'En seguimiento']);
        $canalizado = CasoSeguimiento::create(['empresa_id' => $this->empresaA->id, 'identificador_empleado' => 'P2', 'nivel_riesgo_detectado' => 'Urgente', 'estatus_atencion' => 'Canalizado']);
        CasoSeguimiento::create(['empresa_id' => $this->empresaB->id, 'identificador_empleado' => 'P3', 'nivel_riesgo_detectado' => 'Moderado', 'estatus_atencion' => 'Cerrado satisfactorio']);

        // Una referencia enviada, todavía sin cita.
        SolicitudReferencia::create([
            'caso_seguimiento_id' => $canalizado->id,
            'empresa_id' => $this->empresaA->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'P2',
        ]);
    }

    private function crearEmpresa(string $nombre, string $correo): Empresa
    {
        return Empresa::create([
            'nombre_empresa' => $nombre,
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => $correo,
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
        ]);
    }

    private function crearTamizaje(Empresa $empresa, string $nivel): Tamizaje
    {
        return Tamizaje::create([
            'empresa_id' => $empresa->id,
            'nombre_completo' => 'Colaborador ' . uniqid(),
            'consentimiento_otorgado' => $nivel !== 'No participó',
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => $nivel,
        ]);
    }

    public function test_las_metricas_globales_suman_todas_las_empresas(): void
    {
        $m = MetricasAtencion::paraEmpresas();

        $this->assertSame(4, $m->tamizajesAplicados());
        $this->assertSame(3, $m->participaron());
        $this->assertSame(1, $m->noParticiparon());
        $this->assertSame(2, $m->detectadosEnRiesgo()); // Urgente + Moderado
        $this->assertSame(1, $m->casosAbiertos());
        $this->assertSame(1, $m->casosCanalizados());
        $this->assertSame(1, $m->casosCerrados());
    }

    public function test_las_metricas_se_acotan_a_las_empresas_indicadas(): void
    {
        $m = MetricasAtencion::paraEmpresas([$this->empresaB->id]);

        $this->assertSame(1, $m->tamizajesAplicados());
        $this->assertSame(1, $m->detectadosEnRiesgo());
        $this->assertSame(0, $m->casosCanalizados());
        $this->assertSame(1, $m->casosCerrados());
        $this->assertSame(0, $m->referenciasEnviadas());
    }

    public function test_quien_declina_cuenta_en_el_total_pero_no_en_la_participacion(): void
    {
        $m = MetricasAtencion::paraEmpresas([$this->empresaA->id]);

        $this->assertSame(3, $m->tamizajesAplicados());
        $this->assertSame(2, $m->participaron());
        $this->assertSame(67, $m->porcentajeParticipacion());
    }

    public function test_una_referencia_sin_cita_se_reporta_como_pendiente(): void
    {
        $m = MetricasAtencion::paraEmpresas();

        $this->assertSame(1, $m->referenciasEnviadas());
        $this->assertSame(0, $m->referenciasConCita());
        $this->assertSame(1, $m->referenciasSinCita());

        SolicitudReferencia::first()->update(['fecha_cita' => now()->addDay()]);

        $m = MetricasAtencion::paraEmpresas();
        $this->assertSame(1, $m->referenciasConCita());
        $this->assertSame(0, $m->referenciasSinCita());
    }

    public function test_sin_tamizajes_el_porcentaje_no_truena(): void
    {
        $vacia = $this->crearEmpresa('Empresa Vacía', 'vacia@empresa.test');

        $m = MetricasAtencion::paraEmpresas([$vacia->id]);

        $this->assertSame(0, $m->tamizajesAplicados());
        $this->assertSame(0, $m->porcentajeParticipacion());
    }

    public function test_el_evaluador_solo_ve_las_metricas_de_sus_empresas(): void
    {
        $evaluador = User::create([
            'name' => 'Evaluador',
            'apellidos' => 'Métricas',
            'email' => 'evaluador.metricas@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'evaluador',
        ]);
        $evaluador->empresas()->attach($this->empresaB->id);

        $this->actingAs($evaluador, 'web');

        $respuesta = $this->get('/evaluador');

        $respuesta->assertSuccessful()
            ->assertSee('Avance por organización')
            ->assertSee('Empresa B')
            ->assertDontSee('Empresa A');
    }

    public function test_el_tablero_de_avance_no_expone_nombres_de_colaboradores(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'apellidos' => 'Métricas',
            'email' => 'admin.metricas@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'web');

        $this->get('/admin')
            ->assertSuccessful()
            ->assertSee('Avance de la atención')
            ->assertSee('Empresa A')
            // Angélica fue explícita: métricas sí, datos de los trabajadores no.
            ->assertDontSee('P1')
            ->assertDontSee('P2');
    }
}
