<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\ListCasoSeguimientos;
use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\SolicitudReferenciaForm as Formato;
use App\Filament\Gestor\Resources\SolicitudReferencias\Pages\ManageSolicitudReferencias;
use App\Livewire\ResponderTamizaje;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use App\Models\User;
use App\Support\CatalogoUnidadesAtencion;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema as SchemaBd;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
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

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

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

    public function test_el_catalogo_de_unidades_es_el_concentrado_oficial_de_salud(): void
    {
        $catalogo = CatalogoUnidadesAtencion::UNIDADES;

        $this->assertCount(156, $catalogo);
        $this->assertSame($catalogo, array_unique($catalogo), 'El catálogo no debe traer unidades repetidas');

        // Las ocho CECOSAMAs que mencionó Angélica.
        $cecosamas = array_values(array_filter($catalogo, fn ($u) => str_starts_with($u, 'CECOSAMA')));
        $this->assertCount(8, $cecosamas);
        $this->assertContains('CECOSAMA SALTILLO', $cecosamas);

        $this->assertContains('CENTRO INTEGRAL DE SALUD MENTAL', $catalogo);
        $this->assertContains('HG DE SALTILLO', $catalogo);
    }

    public function test_las_unidades_se_ofrecen_agrupadas_y_con_opcion_otro(): void
    {
        $opciones = Formato::unidadesAtencion();

        $this->assertSame(
            ['Centro Integral de Salud Mental', 'CECOSAMA', 'Hospitales', 'Centros de Salud', 'Otra unidad'],
            array_keys($opciones),
        );

        $this->assertCount(8, $opciones['CECOSAMA']);
        $this->assertArrayHasKey('Otro', $opciones['Otra unidad']);

        // Cada unidad se guarda con su nombre tal cual, sin traducciones.
        $this->assertSame('CECOSAMA TORREON', $opciones['CECOSAMA']['CECOSAMA TORREON']);
    }

    /**
     * Angélica reportó el 08/08/2026 que el desplegable se cortaba en
     * "COMUNIDAD NEGROS MASCOGOS": es la opción número 50 y ese es el tope que
     * Filament aplica por omisión a los Select buscables.
     */
    public function test_el_desplegable_alcanza_a_mostrar_todo_el_catalogo(): void
    {
        $limite = CatalogoUnidadesAtencion::limiteOpciones();

        $planas = CatalogoUnidadesAtencion::planas();

        $this->assertGreaterThanOrEqual(count($planas), $limite);
        $this->assertGreaterThan(50, $limite, 'Debe superar el tope por omisión de Filament');
    }

    public function test_hay_ocho_jurisdicciones_numeradas(): void
    {
        $this->assertCount(8, Formato::JURISDICCIONES);
        // PHP convierte las claves numéricas en enteros; el valor se guarda como texto.
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8], array_keys(Formato::JURISDICCIONES));
        // Angélica pidió (31/08/2026) que la etiqueta lleve el municipio sede.
        $this->assertSame('Jurisdicción 1: Piedras Negras', Formato::JURISDICCIONES[1]);
        $this->assertSame('Jurisdicción 8: Saltillo', Formato::JURISDICCIONES[8]);
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
        $this->assertTrue(SchemaBd::hasColumn('solicitudes_referencia', 'estatus_cita'));
        $this->assertFalse(SchemaBd::hasColumn('solicitudes_referencia', 'estatus_somos'));
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

    /**
     * Campo abierto que pidió Angélica el 10/09/2026: el motivo viaja con la
     * solicitud, se recupera al reabrir el formato y lo lee quien agenda.
     */
    public function test_el_motivo_de_referencia_se_captura_y_lo_ve_quien_agenda(): void
    {
        $this->assertTrue(SchemaBd::hasColumn('solicitudes_referencia', 'motivo_referencia'));

        $motivo = 'Crisis de ansiedad recurrentes en el turno nocturno; pide valoración.';

        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Referida',
            'nivel_riesgo_detectado' => 'Alta',
            'estatus_atencion' => 'Canalizado',
        ]);

        $solicitud = SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Referida',
            'motivo_referencia' => $motivo,
        ]);

        $this->assertSame($motivo, $solicitud->fresh()->motivo_referencia);

        // Al reabrir el formato la empresa vuelve a ver lo que escribió.
        $this->assertSame($motivo, Formato::valoresIniciales($caso->fresh())['motivo_referencia']);

        $gestor = User::create([
            'name' => 'Gestor',
            'apellidos' => 'Motivo',
            'email' => 'gestor.motivo@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'gestor',
        ]);

        $this->actingAs($gestor, 'web');

        // Sin fecha de cita entra en la bandeja de pendientes, que es el filtro
        // que el listado aplica por omisión.
        $this->get('/gestor/referencias')
            ->assertSuccessful()
            ->assertSee('Crisis de ansiedad recurrentes en el turno nocturno');
    }

    /**
     * Texto visible del formato para un registro dado (un caso o una
     * solicitud). Se arma el esquema en vez de montar la modal porque
     * Filament v5 no renderiza el contenido de la modal en la respuesta de
     * la prueba, y lo que hay que comprobar son los Placeholder.
     */
    private function textoDelFormato($record, string $pagina, bool $puedeAgendar = false, bool $soloLectura = false): string
    {
        $esquema = Schema::make(Livewire::test($pagina)->instance())
            ->components(Formato::componentes($puedeAgendar, $soloLectura))
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

    /** Un campo del formato, para revisar sus reglas sin montar la modal. */
    private function campoDelFormato(string $nombre, bool $soloLectura = false)
    {
        $esquema = Schema::make(Livewire::test(ListCasoSeguimientos::class)->instance())
            ->components(Formato::componentes(soloLectura: $soloLectura));

        return collect($esquema->getFlatComponents())
            ->first(fn ($componente) => method_exists($componente, 'getName') && $componente->getName() === $nombre);
    }

    /**
     * Angélica, 11/09/2026: "si la carga del INE en el formato de referencia
     * puede ser obligatorio". Solo al capturar: en modo consulta el formato ya
     * está hecho y exigirla bloquearía al Gestor y al admin.
     */
    public function test_la_ine_es_obligatoria_al_capturar_el_formato(): void
    {
        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        $this->assertTrue($this->campoDelFormato('ine_path')->isRequired());
        $this->assertFalse($this->campoDelFormato('ine_path', soloLectura: true)->isRequired());
    }

    /**
     * Angélica pidió por audio el 10/09/2026 que "me arrastre los resultados"
     * al formato: la sintomatología de ansiedad, la de depresión y la conducta
     * suicida. Se leen del tamizaje —no se capturan ni se copian a columnas
     * propias— siguiendo su instrucción del 06/08/2026.
     */
    public function test_el_formato_arrastra_los_resultados_del_tamizaje(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Persona Referida',
            'consentimiento_otorgado' => true,
            'genero' => 'Mujer',
            'edad' => '25 a 34 años',
            'riesgo_ansiedad' => 16,
            'nivel_ansiedad' => 'Grave',
            'riesgo_depresion' => 21,
            'nivel_depresion' => 'Grave',
            'riesgo_conducta_suicida' => 2,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_POSITIVO,
            'nivel_riesgo_general' => PrioridadAtencion::URGENTE,
            'respuestas' => ['conducta_suicida' => [1 => 1, 2 => 1, 3 => 0, 4 => 0, 5 => 1]],
        ]);

        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Referida',
            'nivel_riesgo_detectado' => PrioridadAtencion::URGENTE,
            'estatus_atencion' => 'Canalizado',
            'referencia_secretaria_salud' => true,
        ]);

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        $texto = $this->textoDelFormato($caso, ListCasoSeguimientos::class);

        $this->assertStringContainsString('Síntomas de Ansiedad: Grave', $texto);
        $this->assertStringContainsString('GAD-7: 16 de 21', $texto);
        $this->assertStringContainsString('Síntomas de Depresión: Grave', $texto);
        $this->assertStringContainsString('PHQ-9: 21 de 27', $texto);
        // El ASQ va con el título completo de la pantalla y su acción debajo.
        $this->assertStringContainsString('Indicadores de Conducta suicida: '.ResultadoAsq::TITULO_AGUDO, $texto);
        $this->assertStringContainsString(ResponderTamizaje::ACCION_SUICIDIO_AGUDO, $texto);

        // El badge del ASQ se pinta por el valor guardado ("Positivo"), no por
        // el título de pantalla: si no, ColorNivel no lo reconoce y sale gris.
        $this->assertStringContainsString(
            'background-color: '.ColorNivel::hex(ResponderTamizaje::SUICIDIO_POSITIVO),
            $texto,
        );
    }

    /** Un caso capturado a mano no tiene de dónde arrastrar: se dice, no se finge. */
    public function test_un_caso_sin_tamizaje_lo_avisa_en_vez_de_inventar_resultados(): void
    {
        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Capturada A Mano',
            'nivel_riesgo_detectado' => PrioridadAtencion::MODERADA,
            'estatus_atencion' => 'Canalizado',
            'referencia_secretaria_salud' => true,
        ]);

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        $texto = $this->textoDelFormato($caso, ListCasoSeguimientos::class);

        $this->assertStringContainsString('Este caso se capturó a mano', $texto);
        $this->assertStringNotContainsString('Síntomas de Ansiedad:', $texto);
    }

    /**
     * El Gestor y el admin llegan al formato desde la solicitud, no desde el
     * caso: los resultados tienen que resolverse por ese camino también.
     */
    public function test_el_gestor_ve_los_resultados_en_el_formato(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Persona Referida',
            'consentimiento_otorgado' => true,
            'riesgo_ansiedad' => 11,
            'nivel_ansiedad' => 'Moderada',
            'riesgo_depresion' => 12,
            'nivel_depresion' => 'Moderada',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::MODERADA,
        ]);

        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Referida',
            'nivel_riesgo_detectado' => PrioridadAtencion::MODERADA,
            'estatus_atencion' => 'Canalizado',
            'referencia_secretaria_salud' => true,
        ]);

        $solicitud = SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Referida',
            'motivo_referencia' => 'Requiere valoración psicológica.',
        ]);

        $gestor = User::create([
            'name' => 'Gestor',
            'apellidos' => 'Resultados',
            'email' => 'gestor.resultados@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'gestor',
        ]);

        $this->actingAs($gestor, 'web');
        Filament::setCurrentPanel(Filament::getPanel('gestor'));

        // Mismo esquema, pero entrando por la solicitud y en solo lectura,
        // como lo abren el Gestor y el admin.
        $texto = $this->textoDelFormato(
            $solicitud,
            ManageSolicitudReferencias::class,
            puedeAgendar: true,
            soloLectura: true,
        );

        $this->assertStringContainsString('Síntomas de Ansiedad: Moderada', $texto);
        $this->assertStringContainsString('Síntomas de Depresión: Moderada', $texto);
        $this->assertStringContainsString(
            'Indicadores de Conducta suicida: '.ResponderTamizaje::SUICIDIO_NEGATIVO,
            $texto,
        );
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
            'nivel_riesgo_general' => 'Moderada',
        ]);

        // El caso trae un valor viejo distinto al del cuestionario.
        $caso = CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Colaborador Tamizado',
            'nivel_riesgo_detectado' => 'Moderada',
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
