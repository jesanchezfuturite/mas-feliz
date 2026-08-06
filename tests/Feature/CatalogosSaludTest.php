<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\SolicitudReferenciaForm as Formato;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Catálogos que Angélica confirmó el 06/08/2026 para cerrar su punto 3.
 */
class CatalogosSaludTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Catálogos',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => 'catalogos@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 20,
        ]);
    }

    public function test_las_unidades_de_atencion_son_las_que_indico_salud(): void
    {
        $this->assertSame(
            ['Centro de Salud', 'Hospital General', 'Centro Integral', 'CECOSAMA', 'Otro'],
            array_keys(Formato::UNIDADES_ATENCION),
        );
    }

    public function test_hay_ocho_jurisdicciones_numeradas(): void
    {
        $this->assertCount(8, Formato::JURISDICCIONES);
        // PHP convierte las claves numéricas en enteros; el valor se guarda como texto.
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8], array_keys(Formato::JURISDICCIONES));
        $this->assertSame('Jurisdicción 8', Formato::JURISDICCIONES[8]);
    }

    public function test_el_estatus_de_la_cita_tiene_las_cinco_opciones_con_color(): void
    {
        $esperadas = [
            'Confirmo asistencia de cita',
            'Acudió a cita',
            'Reagendo cita',
            'Atendido por ruta alterna',
            'Notificación a empresa',
        ];

        $this->assertSame($esperadas, array_keys(Formato::ESTATUS_CITA));

        // Cada opción tiene color propio: es como Salud las distingue.
        foreach ($esperadas as $opcion) {
            $this->assertArrayHasKey($opcion, Formato::COLORES_ESTATUS_CITA);
        }
    }

    public function test_la_columna_ya_no_se_llama_estatus_somos(): void
    {
        $this->assertTrue(Schema::hasColumn('solicitudes_referencia', 'estatus_cita'));
        $this->assertFalse(Schema::hasColumn('solicitudes_referencia', 'estatus_somos'));
    }

    public function test_el_estatus_de_la_cita_se_guarda_y_se_lee(): void
    {
        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Referida',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'Canalizado',
        ]);

        $solicitud = SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'jurisdiccion' => '3',
            'nombre_usuario' => 'Persona Referida',
            'estatus_cita' => 'Acudió a cita',
            'unidad_atencion' => 'CECOSAMA',
            'fecha_cita' => now()->addDay(),
        ]);

        $this->assertSame('Acudió a cita', $solicitud->fresh()->estatus_cita);
        $this->assertSame('CECOSAMA', $solicitud->fresh()->unidad_atencion_completa);
    }

    public function test_el_gestor_ve_el_estatus_en_su_listado(): void
    {
        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Referida',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'Canalizado',
        ]);

        SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Referida',
            // Sin cita todavía: así entra en la bandeja de pendientes, que es
            // el filtro que el listado del Gestor aplica por omisión.
            'estatus_cita' => 'Notificación a empresa',
        ]);

        $gestor = User::create([
            'name' => 'Gestor',
            'apellidos' => 'Catálogos',
            'email' => 'gestor.catalogos@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'gestor',
        ]);

        $this->actingAs($gestor, 'web');

        $this->get('/gestor/referencias')
            ->assertSuccessful()
            ->assertSee('Notificación a empresa')
            ->assertDontSee('SOMOS+');
    }

    public function test_los_datos_de_identificacion_los_manda_el_tamizaje(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Colaborador Tamizado',
            'consentimiento_otorgado' => true,
            'genero' => 'Mujer',
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Administrativas',
            'tiempo_trabajando' => 'Más de 5 años',
            'riesgo_ansiedad' => 5,
            'riesgo_depresion' => 5,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => 'Moderado',
        ]);

        // El caso trae un valor viejo distinto al del cuestionario.
        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Colaborador Tamizado',
            'nivel_riesgo_detectado' => 'Moderado',
            'estatus_atencion' => 'En seguimiento',
            'genero' => 'Hombre',
            'edad' => '45 a 54 años',
        ]);

        // Manda la respuesta del trabajador, no la copia guardada en el caso.
        $this->assertSame('Mujer', $caso->datoIdentificacion('genero'));
        $this->assertSame('25 a 34 años', $caso->datoIdentificacion('edad'));
        $this->assertSame('Administrativas', $caso->datoIdentificacion('actividad_trabajo'));

        // Un caso capturado a mano sí conserva lo que escribió la empresa.
        $manual = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Sin Cuestionario',
            'nivel_riesgo_detectado' => 'Leve',
            'estatus_atencion' => 'En seguimiento',
            'genero' => 'Hombre',
        ]);

        $this->assertSame('Hombre', $manual->datoIdentificacion('genero'));
    }
}
