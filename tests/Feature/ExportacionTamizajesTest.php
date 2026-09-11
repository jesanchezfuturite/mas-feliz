<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\Tamizajes\Pages\ManageTamizajes;
use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Filament\Resources\Empresas\Pages\ViewEmpresa;
use App\Filament\Resources\Empresas\RelationManagers\TamizajesRelationManager;
use App\Livewire\ResponderTamizaje;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\Tamizaje;
use App\Models\User;
use App\Support\ExportacionTamizajes;
use App\Support\PrioridadAtencion;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

/**
 * Angélica pidió el 03/09/2026 poder llevarse a Excel el listado de personas
 * con sus resultados, porque las empresas ya están explorando los suyos.
 *
 * La hoja se lee de vuelta con openspout en vez de revisar el texto del
 * archivo: así se comprueba que sale un .xlsx válido y no solo que el código
 * corrió.
 */
class ExportacionTamizajesTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    private string $ruta;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Exportadora',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => 'exporta@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 20,
        ]);

        $this->ruta = tempnam(sys_get_temp_dir(), 'mf-test-export-');
    }

    protected function tearDown(): void
    {
        @unlink($this->ruta);

        parent::tearDown();
    }

    /** @return array<string, list<list<mixed>>> Renglones por hoja. */
    private function leerArchivo(): array
    {
        $lector = new Reader;
        $lector->open($this->ruta);

        $hojas = [];

        foreach ($lector->getSheetIterator() as $hoja) {
            $renglones = [];

            foreach ($hoja->getRowIterator() as $fila) {
                $renglones[] = $fila->toArray();
            }

            $hojas[$hoja->getName()] = $renglones;
        }

        $lector->close();

        return $hojas;
    }

    private function exportar(): array
    {
        $total = ExportacionTamizajes::escribir(
            Tamizaje::query()->where('empresa_id', $this->empresa->id)->orderBy('id'),
            $this->empresa,
            $this->ruta,
        );

        return [$total, $this->leerArchivo()];
    }

    public function test_la_hoja_de_resultados_usa_las_etiquetas_de_la_plataforma(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Colaboradora Uno',
            'genero' => 'Mujer',
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Otra',
            'actividad_trabajo_otra' => 'Almacén',
            'tiempo_trabajando' => 'Más de 5 años',
            'telefono' => '8441112233',
            'correo' => 'colaboradora@empresa.test',
            'riesgo_ansiedad' => 16,
            'nivel_ansiedad' => 'Grave',
            'riesgo_depresion' => 21,
            'nivel_depresion' => 'Grave',
            'riesgo_conducta_suicida' => 2,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_POSITIVO,
            'nivel_riesgo_general' => PrioridadAtencion::URGENTE,
            'respuestas' => ['conducta_suicida' => [1 => 1, 2 => 1, 3 => 0, 4 => 0, 5 => 1]],
            'comentarios' => 'Enviada a seguimiento',
        ]);

        [$total, $hojas] = $this->exportar();

        $this->assertSame(1, $total);
        $this->assertSame(['Resultados', 'Información'], array_keys($hojas));
        $this->assertSame(ExportacionTamizajes::ENCABEZADOS, $hojas['Resultados'][0]);

        $fila = $hojas['Resultados'][1];

        $this->assertSame('Colaboradora Uno', $fila[0]);
        $this->assertSame('Mujer', $fila[1]);
        $this->assertSame('25 a 34 años', $fila[2]);
        // "Otra" se resuelve al texto que escribió la persona, como en el listado.
        $this->assertSame('Almacén', $fila[3]);
        $this->assertSame('8441112233', $fila[5]);
        // La fecha viaja como fecha, no como texto: así se puede ordenar en Excel.
        $this->assertInstanceOf(\DateTimeInterface::class, $fila[7]);
        $this->assertSame('Sí', $fila[8]);
        $this->assertSame('Grave', $fila[9]);
        $this->assertSame(16, $fila[10]);
        $this->assertSame('Grave', $fila[11]);
        $this->assertSame(21, $fila[12]);
        // El ASQ va con el mismo título que la pantalla, agudeza incluida.
        $this->assertSame('Positivo: Riesgo Agudo', $fila[13]);
        $this->assertSame(2, $fila[14]);
        $this->assertSame(PrioridadAtencion::URGENTE, $fila[15]);
        $this->assertSame(ResponderTamizaje::ACCION_SUICIDIO_AGUDO, $fila[16]);
        $this->assertSame('Enviada a seguimiento', $fila[17]);
    }

    /**
     * Quien declinó cuenta en el avance de participación, así que su renglón
     * se queda; lo que no puede aparecer es un 0 donde no hubo evaluación,
     * porque se leería como "sin síntomas".
     */
    public function test_quien_declino_participar_va_sin_resultados(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => false,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => PrioridadAtencion::NO_PARTICIPO,
        ]);

        [$total, $hojas] = $this->exportar();

        $this->assertSame(1, $total);

        $fila = $hojas['Resultados'][1];

        $this->assertSame('No', $fila[8]);
        $this->assertSame('', $fila[9]);
        $this->assertSame('', $fila[10]);
        $this->assertSame('', $fila[11]);
        $this->assertSame('', $fila[12]);
        $this->assertSame('', $fila[13]);
        $this->assertSame('', $fila[14]);
        $this->assertSame(PrioridadAtencion::NO_PARTICIPO, $fila[15]);
    }

    /** La nota de la escala viaja con el archivo, igual que en los listados. */
    public function test_la_segunda_hoja_identifica_a_la_empresa_y_lleva_la_nota(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Colaborador',
            'riesgo_ansiedad' => 3,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'riesgo_depresion' => 2,
            'nivel_depresion' => 'Mínima o ausente',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        [, $hojas] = $this->exportar();

        $texto = collect($hojas['Información'])
            ->map(fn (array $renglon) => implode(' ', array_map(fn ($celda) => (string) $celda, $renglon)))
            ->implode("\n");

        $this->assertStringContainsString('Empresa Exportadora', $texto);
        $this->assertStringContainsString($this->empresa->folio, $texto);
        $this->assertStringContainsString(PrioridadAtencion::NOTA, $texto);
        $this->assertStringContainsString('datos personales y de salud', $texto);
    }

    /**
     * El aislamiento entre empresas es manual en este proyecto: el archivo
     * sale de la consulta del recurso, que ya filtra por `empresa_id`.
     */
    public function test_la_empresa_solo_exporta_sus_propios_tamizajes(): void
    {
        $otra = Empresa::create([
            'nombre_empresa' => 'Empresa Ajena',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => 'ajena@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 5,
        ]);

        foreach ([[$this->empresa, 'Persona Propia'], [$otra, 'Persona Ajena']] as [$empresa, $nombre]) {
            Tamizaje::create([
                'empresa_id' => $empresa->id,
                'consentimiento_otorgado' => true,
                'nombre_completo' => $nombre,
                'riesgo_ansiedad' => 1,
                'nivel_ansiedad' => 'Mínima o sin ansiedad',
                'riesgo_depresion' => 1,
                'nivel_depresion' => 'Mínima o ausente',
                'riesgo_conducta_suicida' => 0,
                'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
                'nivel_riesgo_general' => PrioridadAtencion::LEVE,
            ]);
        }

        $this->actingAs($this->empresa, 'empresa');

        $total = ExportacionTamizajes::escribir(
            TamizajeResource::getEloquentQuery(),
            $this->empresa,
            $this->ruta,
        );

        $hojas = $this->leerArchivo();
        $nombres = array_column(array_slice($hojas['Resultados'], 1), 0);

        $this->assertSame(1, $total);
        $this->assertSame(['Persona Propia'], $nombres);
    }

    /**
     * El camino completo del botón: la acción del encabezado del listado
     * devuelve la descarga con el nombre del archivo de la empresa.
     */
    public function test_el_boton_del_listado_descarga_la_hoja(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Colaborador',
            'riesgo_ansiedad' => 6,
            'nivel_ansiedad' => 'Leve',
            'riesgo_depresion' => 6,
            'nivel_depresion' => 'Leve',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        Livewire::test(ManageTamizajes::class)
            ->callAction('exportarTamizajes')
            ->assertFileDownloaded(ExportacionTamizajes::nombreArchivo($this->empresa));
    }

    /**
     * El archivo sale de la consulta de la tabla, así que respeta lo que la
     * empresa tiene en pantalla: si buscó a alguien, exporta esa búsqueda.
     */
    public function test_el_archivo_respeta_la_busqueda_de_la_tabla(): void
    {
        foreach (['Persona Buscada', 'Persona Aparte'] as $nombre) {
            Tamizaje::create([
                'empresa_id' => $this->empresa->id,
                'consentimiento_otorgado' => true,
                'nombre_completo' => $nombre,
                'riesgo_ansiedad' => 2,
                'nivel_ansiedad' => 'Mínima o sin ansiedad',
                'riesgo_depresion' => 2,
                'nivel_depresion' => 'Mínima o ausente',
                'riesgo_conducta_suicida' => 0,
                'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
                'nivel_riesgo_general' => PrioridadAtencion::LEVE,
            ]);
        }

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        $componente = Livewire::test(ManageTamizajes::class)
            ->set('tableSearch', 'Buscada')
            ->callAction('exportarTamizajes');

        file_put_contents($this->ruta, base64_decode($componente->effects['download']['content']));

        $hojas = $this->leerArchivo();
        $nombres = array_column(array_slice($hojas['Resultados'], 1), 0);

        $this->assertSame(['Persona Buscada'], $nombres);
    }

    /** Sin registros no se descarga un archivo vacío: se avisa y no pasa nada. */
    public function test_sin_registros_avisa_en_vez_de_descargar(): void
    {
        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));

        Livewire::test(ManageTamizajes::class)
            ->callAction('exportarTamizajes')
            ->assertNotified('No hay registros para exportar');
    }

    /** Crea otra organización con una persona tamizada y la devuelve. */
    private function otraEmpresaConTamizaje(string $nombreEmpresa, string $nombrePersona): Empresa
    {
        $otra = Empresa::create([
            'nombre_empresa' => $nombreEmpresa,
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director',
            'nombre_responsable' => 'Responsable',
            'correo' => Str::slug($nombreEmpresa).'@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 5,
        ]);

        Tamizaje::create([
            'empresa_id' => $otra->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => $nombrePersona,
            'riesgo_ansiedad' => 2,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'riesgo_depresion' => 2,
            'nivel_depresion' => 'Mínima o ausente',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        return $otra;
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Exporta',
            'email' => 'admin.exporta@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);
    }

    /**
     * Cuando el archivo cruza organizaciones —el admin exportando el listado
     * completo— cada renglón tiene que decir de quién es.
     */
    public function test_el_archivo_que_cruza_organizaciones_trae_la_columna_organizacion(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Persona Propia',
            'riesgo_ansiedad' => 2,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'riesgo_depresion' => 2,
            'nivel_depresion' => 'Mínima o ausente',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        $this->otraEmpresaConTamizaje('Empresa Vecina', 'Persona Vecina');

        $total = ExportacionTamizajes::escribir(
            Tamizaje::query()->orderBy('empresa_id')->orderBy('id'),
            null,
            $this->ruta,
        );

        $hojas = $this->leerArchivo();

        $this->assertSame(2, $total);
        $this->assertSame('Organización', $hojas['Resultados'][0][0]);
        $this->assertSame(ExportacionTamizajes::ENCABEZADOS, array_slice($hojas['Resultados'][0], 1));

        $renglones = array_slice($hojas['Resultados'], 1);

        $this->assertSame(
            [['Empresa Exportadora', 'Persona Propia'], ['Empresa Vecina', 'Persona Vecina']],
            array_map(fn (array $fila) => [$fila[0], $fila[1]], $renglones),
        );

        // Y la hoja de información dice que no es de una sola organización.
        $texto = collect($hojas['Información'])
            ->map(fn (array $renglon) => implode(' ', array_map(fn ($celda) => (string) $celda, $renglon)))
            ->implode("\n");

        $this->assertStringContainsString('Todas las organizaciones del listado', $texto);
    }

    /**
     * El admin puede lo mismo que la empresa: exportar desde el historial de
     * tamizajes de una organización, con las mismas columnas.
     */
    public function test_el_admin_exporta_el_historial_de_una_empresa(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Persona Propia',
            'riesgo_ansiedad' => 2,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'riesgo_depresion' => 2,
            'nivel_depresion' => 'Mínima o ausente',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        $this->otraEmpresaConTamizaje('Empresa Vecina', 'Persona Vecina');

        $this->actingAs($this->admin(), 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $componente = Livewire::test(TamizajesRelationManager::class, [
            'ownerRecord' => $this->empresa,
            'pageClass' => ViewEmpresa::class,
        ])->callAction(TestAction::make('exportarTamizajes')->table());

        file_put_contents($this->ruta, base64_decode($componente->effects['download']['content']));

        $hojas = $this->leerArchivo();

        // Archivo de una sola organización: sin columna "Organización" y solo
        // con la gente de esa empresa.
        $this->assertSame(ExportacionTamizajes::ENCABEZADOS, $hojas['Resultados'][0]);
        $this->assertSame(['Persona Propia'], array_column(array_slice($hojas['Resultados'], 1), 0));
    }

    /** Y desde el listado de empresas, todas de un jalón. */
    public function test_el_admin_exporta_todas_las_organizaciones_desde_el_listado(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Persona Propia',
            'riesgo_ansiedad' => 2,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'riesgo_depresion' => 2,
            'nivel_depresion' => 'Mínima o ausente',
            'riesgo_conducta_suicida' => 0,
            'nivel_suicidio' => ResponderTamizaje::SUICIDIO_NEGATIVO,
            'nivel_riesgo_general' => PrioridadAtencion::LEVE,
        ]);

        $this->otraEmpresaConTamizaje('Empresa Vecina', 'Persona Vecina');

        $this->actingAs($this->admin(), 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $componente = Livewire::test(ListEmpresas::class)
            ->callAction('exportarTamizajes');

        file_put_contents($this->ruta, base64_decode($componente->effects['download']['content']));

        $hojas = $this->leerArchivo();
        $personas = array_column(array_slice($hojas['Resultados'], 1), 1);

        $this->assertSame('Organización', $hojas['Resultados'][0][0]);
        $this->assertEqualsCanonicalizing(['Persona Propia', 'Persona Vecina'], $personas);
    }

    public function test_el_nombre_del_archivo_lleva_el_folio_de_la_empresa(): void
    {
        $nombre = ExportacionTamizajes::nombreArchivo($this->empresa);

        $this->assertStringStartsWith('Tamizajes_'.$this->empresa->folio.'_', $nombre);
        $this->assertStringEndsWith('.xlsx', $nombre);

        // Sin empresa el nombre dice que el archivo trae varias.
        $this->assertStringStartsWith('Tamizajes_Todas-las-organizaciones_', ExportacionTamizajes::nombreArchivo(null));
    }
}
