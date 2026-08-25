<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\ListCasoSeguimientos;
use App\Filament\Empresa\Resources\CasoSeguimientos\Tables\Columnas;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Puntos 3, 4 y 5 de Angélica (04/08/2026) sobre el listado de personas en
 * riesgo: jerarquía del documento de Salud, captura en línea en vez de
 * ventanas modales, y aviso de la cita directo en el nombre del colaborador.
 */
class ListadoRiesgoTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Listado',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'listado@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 60,
        ]);

        $this->actingAs($this->empresa, 'empresa');
    }

    private function crearCaso(array $extra = []): CasoSeguimiento
    {
        return CasoSeguimiento::create(array_merge([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Juan Pérez',
            'nivel_riesgo_detectado' => 'Moderada',
            'estatus_atencion' => 'En seguimiento',
        ], $extra));
    }

    public function test_las_columnas_siguen_la_jerarquia_del_documento_de_salud(): void
    {
        $etiquetas = array_map(
            fn ($columna) => $columna->getLabel(),
            Columnas::definicion(),
        );

        // Orden exacto del documento "ATENCIÓN EMPRESAS +FELIZ": identificación,
        // resultados, consentimiento, estatus, servicio y solicitud de referencia.
        $esperado = [
            'Nombre', 'Rango de edad', 'Sexo', 'Funciones', '¿Cuál función?',
            'Tiempo trabajando en la empresa', 'Correo', 'Celular',
            'Ansiedad', 'Depresión', 'Ideación y riesgo suicida', 'Prioridad de atención',
            'Consentimiento', 'Estatus de atención',
            'Medicina', 'Psicología', 'Psiquiatría', 'Otro', '¿Cuál servicio?',
            'Secretaría de Salud', 'Institución de canalización',
        ];

        $this->assertSame($esperado, $etiquetas);
    }

    public function test_el_listado_se_muestra_con_las_columnas_nuevas(): void
    {
        $this->crearCaso();

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('Rango de edad')
            ->assertSee('Ideación y riesgo suicida')
            ->assertSee('Secretaría de Salud');
    }

    public function test_la_columna_del_nombre_queda_inmovilizada(): void
    {
        $this->crearCaso();

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('col-nombre-fija');
    }

    public function test_las_casillas_de_servicio_escriben_sobre_el_arreglo_de_servicios(): void
    {
        $caso = $this->crearCaso();

        $caso->servicio_psicologia = true;
        $caso->servicio_medicina = true;
        $caso->save();

        $this->assertEqualsCanonicalizing(['Psicología', 'Medicina'], $caso->fresh()->servicios);
        $this->assertTrue($caso->fresh()->servicio_psicologia);
        $this->assertFalse($caso->fresh()->servicio_psiquiatria);

        // Desmarcar solo quita ese servicio, no el resto.
        $caso->servicio_medicina = false;
        $caso->save();

        $this->assertSame(['Psicología'], $caso->fresh()->servicios);
    }

    public function test_marcar_dos_veces_no_duplica_el_servicio(): void
    {
        $caso = $this->crearCaso(['servicios' => ['Medicina']]);

        $caso->servicio_medicina = true;
        $caso->save();

        $this->assertSame(['Medicina'], $caso->fresh()->servicios);
    }

    public function test_los_datos_del_tamizaje_se_muestran_en_el_listado(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Juan Pérez',
            'consentimiento_otorgado' => true,
            'genero' => 'Hombre',
            'edad' => '35 a 44 años',
            'actividad_trabajo' => 'Técnicas',
            'tiempo_trabajando' => 'Más de 5 años',
            'riesgo_ansiedad' => 12,
            'riesgo_depresion' => 20,
            'riesgo_conducta_suicida' => 0,
            'nivel_ansiedad' => 'Moderada',
            'nivel_depresion' => 'Grave',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $caso = $this->crearCaso();

        // El caso no guarda esos datos, pero los resuelve desde el tamizaje.
        $this->assertTrue($caso->es_de_tamizaje);
        $this->assertSame('35 a 44 años', $caso->tamizaje->edad);

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('Moderada')
            ->assertSee('Grave');
    }

    /**
     * Reporte de Angélica del 21/08/2026: en Atención/Casos en seguimiento
     * salían vacías las columnas de función, correo y celular. El caso guarda
     * copias que nunca se llenan; el dato vive en el tamizaje.
     */
    public function test_la_funcion_el_correo_y_el_celular_se_arrastran_del_tamizaje(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Juan Pérez',
            'consentimiento_otorgado' => true,
            'actividad_trabajo' => 'Otra',
            'actividad_trabajo_otra' => 'Almacén',
            'correo' => 'juan@empresa.test',
            'telefono' => '8441234567',
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => 'Leve',
        ]);

        $this->crearCaso();

        $caso = CasoSeguimiento::where('identificador_empleado', 'Juan Pérez')->sole();

        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        $tabla = Livewire::test(ListCasoSeguimientos::class);

        $tabla->assertTableColumnStateSet('actividad_trabajo_otra', 'Almacén', $caso)
            ->assertTableColumnStateSet('correo', 'juan@empresa.test', $caso)
            // El tamizaje lo captura como `telefono`.
            ->assertTableColumnStateSet('celular', '8441234567', $caso);

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('juan@empresa.test')
            ->assertSee('8441234567');
    }

    public function test_un_caso_manual_no_se_marca_como_proveniente_de_tamizaje(): void
    {
        $this->assertFalse($this->crearCaso(['identificador_empleado' => 'Sin Tamizaje'])->es_de_tamizaje);
    }

    public function test_la_cita_asignada_se_avisa_en_el_nombre_del_colaborador(): void
    {
        $caso = $this->crearCaso(['estatus_atencion' => 'Canalizado', 'referencia_secretaria_salud' => true]);

        $solicitud = SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Juan Pérez',
        ]);

        // Sin cita todavía no hay aviso.
        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertDontSee('📅 Cita', false);

        $solicitud->update([
            'fecha_cita' => now()->addDays(3)->setTime(9, 30),
            'unidad_atencion' => 'CESAME',
        ]);

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('📅 Cita', false)
            ->assertSee(now()->addDays(3)->format('d/m/Y').' 09:30', false)
            ->assertSee('CESAME');
    }

    public function test_la_alerta_muestra_la_unidad_capturada_como_otro(): void
    {
        $caso = $this->crearCaso(['estatus_atencion' => 'Canalizado']);

        SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Juan Pérez',
            'fecha_cita' => now()->addDay(),
            'unidad_atencion' => 'Otro',
            'unidad_atencion_otra' => 'Clínica 16',
        ]);

        $this->get('/tablero/caso-seguimientos')
            ->assertSuccessful()
            ->assertSee('Clínica 16');
    }
}
