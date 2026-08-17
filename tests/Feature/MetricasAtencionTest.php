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
            'nombre_completo' => 'Colaborador '.uniqid(),
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

    /**
     * Angélica (17/08/2026): la participación se mide contra el total de
     * colaboradores que la organización registró al inicio, y quien declina
     * cuenta igual, porque también fue convocado.
     */
    public function test_la_participacion_se_mide_sobre_el_universo_registrado(): void
    {
        $m = MetricasAtencion::paraEmpresas([$this->empresaA->id]);

        $this->assertSame(10, $m->colaboradoresRegistrados());
        $this->assertSame(3, $m->tamizajesAplicados());

        // 3 de 10 convocados respondieron, incluida la persona que declinó.
        $this->assertSame(30, $m->porcentajeParticipacion());
    }

    /** La tasa de consentimiento sigue disponible, pero como lectura aparte. */
    public function test_el_consentimiento_se_reporta_por_separado(): void
    {
        $m = MetricasAtencion::paraEmpresas([$this->empresaA->id]);

        $this->assertSame(2, $m->participaron());
        $this->assertSame(1, $m->noParticiparon());

        // 2 de los 3 que respondieron otorgaron su consentimiento.
        $this->assertSame(67, $m->porcentajeConsentimiento());
    }

    public function test_el_universo_se_suma_de_todas_las_empresas_del_alcance(): void
    {
        $m = MetricasAtencion::paraEmpresas();

        // Dos empresas de 10 colaboradores cada una, 4 tamizajes en total.
        $this->assertSame(20, $m->colaboradoresRegistrados());
        $this->assertSame(20, $m->porcentajeParticipacion());
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

    public function test_los_casos_cerrados_sin_atender_se_cuentan_aparte(): void
    {
        CasoSeguimiento::create([
            'empresa_id' => $this->empresaA->id,
            'identificador_empleado' => 'P4',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'Cerrado no atendido',
        ]);

        $m = MetricasAtencion::paraEmpresas();

        // No se mezclan con los cerrados satisfactoriamente.
        $this->assertSame(1, $m->casosCerrados());
        $this->assertSame(1, $m->casosCerradosSinAtender());
    }

    public function test_el_estatus_cerrado_no_atendido_esta_disponible(): void
    {
        $this->assertArrayHasKey('Cerrado no atendido', CasoSeguimiento::ESTATUS_ATENCION);
        $this->assertCount(5, CasoSeguimiento::ESTATUS_ATENCION);

        // Cada estatus tiene color propio en los listados.
        foreach (array_keys(CasoSeguimiento::ESTATUS_ATENCION) as $estatus) {
            $this->assertArrayHasKey($estatus, CasoSeguimiento::COLORES_ESTATUS);
        }
    }

    public function test_sin_tamizajes_el_porcentaje_no_truena(): void
    {
        $vacia = $this->crearEmpresa('Empresa Vacía', 'vacia@empresa.test');

        $m = MetricasAtencion::paraEmpresas([$vacia->id]);

        $this->assertSame(0, $m->tamizajesAplicados());
        $this->assertSame(0, $m->porcentajeParticipacion());
        $this->assertNull($m->porcentajeConsentimiento());
    }

    /**
     * Sin universo declarado no hay porcentaje que dar: se reporta null para que
     * el tablero diga "Sin dato" en vez de inventar un 0% o, como pasaba antes,
     * dividir entre 1 y anunciar 300%.
     */
    public function test_sin_universo_declarado_no_hay_porcentaje(): void
    {
        $sinUniverso = $this->crearEmpresa('Empresa Sin Universo', 'sinuniverso@empresa.test');
        $sinUniverso->update(['numero_trabajadores' => 0]);
        $this->crearTamizaje($sinUniverso, 'Leve');

        $m = MetricasAtencion::paraEmpresas([$sinUniverso->id]);

        $this->assertSame(0, $m->colaboradoresRegistrados());
        $this->assertSame(1, $m->tamizajesAplicados());
        $this->assertNull($m->porcentajeParticipacion());
    }

    /** Punto 7: el desglose incluye todos los estatus, también los que van en cero. */
    public function test_el_desglose_de_casos_cubre_todos_los_estatus(): void
    {
        CasoSeguimiento::create([
            'empresa_id' => $this->empresaA->id,
            'identificador_empleado' => 'P5',
            'nivel_riesgo_detectado' => 'Moderado',
            'estatus_atencion' => 'Abandonó',
        ]);

        $m = MetricasAtencion::paraEmpresas();
        $desglose = $m->casosPorCadaEstatus();

        $this->assertSame(array_keys(CasoSeguimiento::ESTATUS_ATENCION), array_keys($desglose));
        $this->assertSame(1, $desglose['Abandonó']);
        $this->assertSame(1, $m->casosAbandonados());
        $this->assertSame(0, $desglose['Cerrado no atendido']);
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
