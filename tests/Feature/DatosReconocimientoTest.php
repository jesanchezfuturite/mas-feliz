<?php

namespace Tests\Feature;

use App\Filament\Empresa\Pages\DatosReconocimiento;
use App\Filament\Resources\Empresas\Schemas\EmpresaForm;
use App\Filament\Resources\Empresas\Schemas\ReconocimientoForm;
use App\Filament\Resources\Reconocimientos\Pages\ManageReconocimientos;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\User;
use App\Support\ExportacionReconocimientos;
use App\Support\RegionesCoahuila;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;
use ZipArchive;

/**
 * Angélica (31/10/2026): para la entrega de Laguna se pidió a mano, empresa
 * por empresa, el nombre tal cual va en el reconocimiento, el logotipo en HD
 * y los contactos de Director y enlace. Ahora la empresa aprobada los
 * confirma en la plataforma —con candado sobre su resultado— y Gobierno se
 * lleva la tabla por región.
 */
class DatosReconocimientoTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = $this->crearEmpresa([
            'estatus_distintivo' => 'Aprobado',
            'nivel_madurez_asignado' => 'Avanzado',
            'fecha_dictamen' => '2026-10-28 10:00:00',
            'retroalimentacion_gobierno' => 'Buen trabajo en prevención.',
        ]);
    }

    private function crearEmpresa(array $datos = []): Empresa
    {
        static $consecutivo = 0;
        $consecutivo++;

        return Empresa::create(array_merge([
            'nombre_empresa' => 'Peñoles',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Ing. Directora Test',
            'nombre_responsable' => 'Lic. Enlace Test',
            'cargo_enlace' => 'Gerente de Recursos Humanos',
            'correo' => "reconocimiento{$consecutivo}@empresa.test",
            'password' => bcrypt('secret'),
            'telefono' => '8711234567',
            'rubro' => 'Industrial',
            'numero_trabajadores' => 120,
        ], $datos));
    }

    private function comoEmpresa(?Empresa $empresa = null): void
    {
        $this->actingAs($empresa ?? $this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));
    }

    private function comoAdmin(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'apellidos' => 'De Prueba',
            'email' => 'admin.reconocimientos@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'web');
        Filament::setCurrentPanel('admin');
    }

    /** @return array<string, mixed> */
    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'nombre_reconocimiento' => 'Metalúrgica Met-Mex Peñoles – Laboratorio Central',
            'logo_reconocimiento' => UploadedFile::fake()->image('logo.png', 1600, 900),
            'nombre_director' => 'Ing. Directora Test',
            'cargo_director' => 'Gerente de Planta',
            'celular_director' => '871 123 4567',
            'nombre_responsable' => 'Lic. Enlace Test',
            'cargo_enlace' => 'Gerente de Recursos Humanos',
            'celular_enlace' => '(871) 765-4321',
        ], $cambios);
    }

    // ── El candado ──────────────────────────────────────────────────────

    public function test_la_empresa_aprobada_no_ve_su_nivel_hasta_confirmar(): void
    {
        $this->comoEmpresa();

        $this->assertTrue($this->empresa->reconocimientoPendiente());

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Resultado de la Dictaminación')
            ->assertSee('Confirmar mis datos')
            ->assertSee(DatosReconocimiento::getUrl())
            // Ni el nivel ni el comentario viajan al navegador.
            ->assertDontSee('Avanzado')
            ->assertDontSee('Buen trabajo en prevención.');
    }

    public function test_el_candado_tambien_aplica_con_las_herramientas_apagadas(): void
    {
        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => false]);

        $this->comoEmpresa();

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Confirmar mis datos')
            ->assertDontSee('Avanzado');

        // Y la página para abrirlo sigue disponible: no es una herramienta.
        $this->get(DatosReconocimiento::getUrl())->assertSuccessful();
    }

    public function test_a_la_empresa_no_aprobada_no_se_le_pide_nada(): void
    {
        $noAprobada = $this->crearEmpresa([
            'estatus_distintivo' => 'No aprobado',
            'retroalimentacion_gobierno' => 'Falta participación en el tamizaje.',
        ]);

        $this->comoEmpresa($noAprobada);

        $this->assertFalse($noAprobada->reconocimientoPendiente());
        $this->assertFalse(DatosReconocimiento::canAccess());

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('No aprobado')
            ->assertSee('Falta participación en el tamizaje.')
            ->assertDontSee('Confirmar mis datos');

        $this->get('/tablero/reconocimiento')->assertForbidden();
    }

    // ── La confirmación ─────────────────────────────────────────────────

    public function test_el_formulario_propone_el_nombre_con_el_que_se_registro(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->assertFormSet([
                'nombre_reconocimiento' => 'Peñoles',
                'nombre_director' => 'Ing. Directora Test',
                'cargo_enlace' => 'Gerente de Recursos Humanos',
            ]);
    }

    public function test_al_confirmar_se_guardan_los_datos_y_se_abre_el_candado(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->fillForm($this->datosValidos())
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect('/tablero');

        $empresa = $this->empresa->fresh();

        $this->assertSame('Metalúrgica Met-Mex Peñoles – Laboratorio Central', $empresa->nombre_reconocimiento);
        // El nombre de registro no se toca: es con el que Gobierno la ubica.
        $this->assertSame('Peñoles', $empresa->nombre_empresa);
        $this->assertSame('Gerente de Planta', $empresa->cargo_director);
        // Los celulares se guardan limpios, a 10 dígitos.
        $this->assertSame('8711234567', $empresa->celular_director);
        $this->assertSame('8717654321', $empresa->celular_enlace);
        $this->assertNotNull($empresa->reconocimiento_confirmado_at);
        $this->assertFalse($empresa->reconocimientoPendiente());

        $this->assertStringStartsWith(ReconocimientoForm::CARPETA_LOGOS.'/', $empresa->logo_reconocimiento);
        Storage::disk('public')->assertExists($empresa->logo_reconocimiento);

        $this->get('/tablero')
            ->assertSuccessful()
            ->assertSee('Avanzado')
            ->assertSee('Buen trabajo en prevención.')
            ->assertSee('Metalúrgica Met-Mex Peñoles – Laboratorio Central')
            ->assertDontSee('Confirmar mis datos');
    }

    public function test_no_se_puede_confirmar_sin_logotipo_ni_contactos(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->fillForm($this->datosValidos([
                'logo_reconocimiento' => null,
                'cargo_director' => '',
                'celular_enlace' => '',
            ]))
            ->call('save')
            ->assertHasFormErrors(['logo_reconocimiento', 'cargo_director', 'celular_enlace']);

        $this->assertTrue($this->empresa->fresh()->reconocimientoPendiente());
    }

    public function test_un_logotipo_chico_se_rechaza_porque_no_sirve_para_imprimir(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->fillForm($this->datosValidos([
                'logo_reconocimiento' => UploadedFile::fake()->image('logo-chico.png', 300, 120),
            ]))
            ->call('save')
            ->assertHasFormErrors(['logo_reconocimiento']);

        $this->assertTrue($this->empresa->fresh()->reconocimientoPendiente());
    }

    public function test_un_logotipo_vectorial_en_pdf_si_se_acepta(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->fillForm($this->datosValidos([
                'logo_reconocimiento' => UploadedFile::fake()->create('logo.pdf', 200, 'application/pdf'),
            ]))
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertStringEndsWith('.pdf', $this->empresa->fresh()->logo_reconocimiento);
    }

    public function test_un_celular_que_no_tiene_diez_digitos_se_rechaza(): void
    {
        $this->comoEmpresa();

        Livewire::test(DatosReconocimiento::class)
            ->fillForm($this->datosValidos(['celular_director' => '871 123']))
            ->call('save')
            ->assertHasFormErrors(['celular_director']);
    }

    // ── Las regiones ────────────────────────────────────────────────────

    public function test_todos_los_municipios_del_catalogo_tienen_region(): void
    {
        $municipios = collect(EmpresaForm::getSchema())
            ->first(fn ($campo) => $campo->getName() === 'municipio')
            ->getOptions();

        $this->assertCount(38, $municipios);

        foreach ($municipios as $municipio) {
            $this->assertNotNull(RegionesCoahuila::de($municipio), "«{$municipio}» no tiene región.");
        }

        $this->assertSame(38, collect(RegionesCoahuila::MUNICIPIOS)->flatten()->unique()->count());
    }

    public function test_la_region_sale_del_municipio(): void
    {
        $this->assertSame('Laguna', $this->empresa->region);
        $this->assertSame('Laguna', RegionesCoahuila::de('San Pedro'));
        $this->assertSame('Sureste', RegionesCoahuila::de('Saltillo'));
        $this->assertNull(RegionesCoahuila::de('Monterrey'));
        $this->assertNull(RegionesCoahuila::de(null));
    }

    // ── El listado y la exportación de Gobierno ─────────────────────────

    public function test_el_listado_de_reconocimientos_solo_trae_aprobadas_y_filtra_por_region(): void
    {
        $saltillo = $this->crearEmpresa(['nombre_empresa' => 'Empresa del Sureste', 'municipio' => 'Saltillo', 'estatus_distintivo' => 'Validado']);
        $noAprobada = $this->crearEmpresa(['nombre_empresa' => 'Empresa No Aprobada', 'estatus_distintivo' => 'No aprobado']);
        $sinDictamen = $this->crearEmpresa(['nombre_empresa' => 'Empresa Sin Dictamen']);

        $this->comoAdmin();

        $this->get('/admin/reconocimientos')->assertSuccessful();

        Livewire::test(ManageReconocimientos::class)
            ->assertCanSeeTableRecords([$this->empresa, $saltillo])
            ->assertCanNotSeeTableRecords([$noAprobada, $sinDictamen])
            ->filterTable('region', 'Laguna')
            ->assertCanSeeTableRecords([$this->empresa])
            ->assertCanNotSeeTableRecords([$saltillo]);
    }

    public function test_el_administrador_captura_los_datos_y_con_eso_quita_el_candado(): void
    {
        $this->comoAdmin();

        Livewire::test(ManageReconocimientos::class)
            ->callAction(
                TestAction::make('datos_reconocimiento')->table($this->empresa),
                $this->datosValidos(['logo_reconocimiento' => null]),
            )
            ->assertHasNoFormErrors();

        $empresa = $this->empresa->fresh();

        $this->assertSame('Metalúrgica Met-Mex Peñoles – Laboratorio Central', $empresa->nombre_reconocimiento);
        $this->assertFalse($empresa->reconocimientoPendiente());
    }

    public function test_el_administrador_puede_quitar_el_candado_y_volver_a_pedir_los_datos(): void
    {
        $this->comoAdmin();

        Livewire::test(ManageReconocimientos::class)
            ->callTableBulkAction('quitar_candado', [$this->empresa]);

        $this->assertFalse($this->empresa->fresh()->reconocimientoPendiente());

        Livewire::test(ManageReconocimientos::class)
            ->callTableBulkAction('pedir_de_nuevo', [$this->empresa]);

        $this->assertTrue($this->empresa->fresh()->reconocimientoPendiente());
    }

    public function test_la_hoja_trae_una_organizacion_por_renglon_ordenada_por_region(): void
    {
        $evaluador = User::create([
            'name' => 'Alejandra',
            'apellidos' => 'Acompañante Test',
            'email' => 'acompanante@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'evaluador',
        ]);

        $this->empresa->evaluadores()->attach($evaluador->id);
        $this->empresa->update([
            'nombre_reconocimiento' => 'Peñoles – Laboratorio Central',
            'logo_reconocimiento' => ReconocimientoForm::CARPETA_LOGOS.'/abc123.png',
            'cargo_director' => 'Gerente de Planta',
            'celular_director' => '8711234567',
            'celular_enlace' => '8717654321',
            'reconocimiento_confirmado_at' => '2026-11-09 12:00:00',
        ]);

        $this->crearEmpresa(['nombre_empresa' => 'Empresa del Sureste', 'municipio' => 'Saltillo', 'estatus_distintivo' => 'Aprobado', 'nivel_madurez_asignado' => 'Inicial']);
        $this->crearEmpresa(['nombre_empresa' => 'Empresa No Aprobada', 'estatus_distintivo' => 'No aprobado']);

        $ruta = tempnam(sys_get_temp_dir(), 'mf-test-reconocimientos-');

        $total = ExportacionReconocimientos::escribirExcel(Empresa::query()->aprobadas(), $ruta);

        $this->assertSame(2, $total);

        $hojas = $this->leerExcel($ruta);
        @unlink($ruta);

        $this->assertSame(['Reconocimientos', 'Resumen'], array_keys($hojas));

        [$encabezados, $laguna, $sureste] = $hojas['Reconocimientos'];

        $this->assertSame(ExportacionReconocimientos::ENCABEZADOS, $encabezados);

        $fila = array_combine($encabezados, array_pad($laguna, count($encabezados), ''));

        $this->assertSame('Peñoles – Laboratorio Central', $fila['Organización (nombre para el reconocimiento)']);
        $this->assertSame('Peñoles', $fila['Nombre registrado en la plataforma']);
        $this->assertSame('Torreón', $fila['Municipio']);
        $this->assertSame('Laguna', $fila['Región']);
        $this->assertSame('Avanzado', $fila['Nivel de Madurez']);
        $this->assertSame('Gerente de Planta', $fila['Director/Gerente: puesto']);
        $this->assertSame('8711234567', $fila['Director/Gerente: celular']);
        $this->assertSame('8717654321', $fila['Enlace: celular']);
        $this->assertSame('Alejandra Acompañante Test', $fila['Acompañante actual']);
        $this->assertSame('Penoles - Laboratorio Central ('.$this->empresa->folio.').png', $fila['Logotipo (archivo)']);
        $this->assertSame('Sí', $fila['Datos confirmados']);

        // La que no ha confirmado sale igual, marcada, para saber a quién buscar.
        $this->assertSame('Empresa del Sureste', $sureste[0]);
        $this->assertSame('Sureste', $sureste[4]);
        $this->assertSame('Pendiente', $sureste[14]);
        $this->assertSame('Pendiente', $sureste[15]);

        // El recuadro de niveles que Angélica traía junto a su tabla.
        $resumen = collect($hojas['Resumen'])->mapWithKeys(fn ($r) => [(string) ($r[0] ?? '') => $r[1] ?? null]);

        $this->assertEquals(2, $resumen['Organizaciones en el archivo']);
        $this->assertEquals(1, $resumen['Inicial']);
        $this->assertEquals(1, $resumen['Avanzado']);
        $this->assertEquals(0, $resumen['Excelencia']);
    }

    public function test_el_zip_trae_los_logotipos_con_el_nombre_de_la_organizacion(): void
    {
        Storage::disk('public')->put(ReconocimientoForm::CARPETA_LOGOS.'/abc123.png', 'contenido-del-logo');

        $this->empresa->update([
            'nombre_reconocimiento' => 'Peñoles – Laboratorio Central',
            'logo_reconocimiento' => ReconocimientoForm::CARPETA_LOGOS.'/abc123.png',
        ]);

        // Aprobada pero sin logotipo: no entra al ZIP.
        $this->crearEmpresa(['nombre_empresa' => 'Empresa del Sureste', 'municipio' => 'Saltillo', 'estatus_distintivo' => 'Aprobado']);

        $ruta = tempnam(sys_get_temp_dir(), 'mf-test-logos-');

        $total = ExportacionReconocimientos::escribirZipLogos(Empresa::query()->aprobadas(), $ruta);

        $this->assertSame(1, $total);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($ruta));
        $this->assertSame(1, $zip->numFiles);

        $nombre = 'Laguna/Penoles - Laboratorio Central ('.$this->empresa->folio.').png';

        $this->assertSame($nombre, $zip->getNameIndex(0));
        $this->assertSame('contenido-del-logo', $zip->getFromName($nombre));

        $zip->close();
        @unlink($ruta);
    }

    public function test_los_botones_de_exportacion_avisan_cuando_no_hay_nada(): void
    {
        $this->empresa->update(['estatus_distintivo' => 'En revisión']);

        $this->comoAdmin();

        Livewire::test(ManageReconocimientos::class)
            ->callAction('exportarExcel')
            ->assertNotified('No hay organizaciones para exportar')
            ->callAction('descargarLogos')
            ->assertNotified('Todavía no hay logotipos');
    }

    public function test_los_botones_de_exportacion_descargan_los_archivos(): void
    {
        Storage::disk('public')->put(ReconocimientoForm::CARPETA_LOGOS.'/abc123.png', 'contenido-del-logo');
        $this->empresa->update(['logo_reconocimiento' => ReconocimientoForm::CARPETA_LOGOS.'/abc123.png']);

        $this->comoAdmin();

        Livewire::test(ManageReconocimientos::class)
            ->filterTable('region', 'Laguna')
            ->callAction('exportarExcel')
            ->assertFileDownloaded('Reconocimientos_Laguna_'.now()->format('Y-m-d').'.xlsx');

        Livewire::test(ManageReconocimientos::class)
            ->callAction('descargarLogos')
            ->assertFileDownloaded('Logotipos_Todas-las-regiones_'.now()->format('Y-m-d').'.zip');
    }

    /** @return array<string, list<list<mixed>>> Renglones por hoja. */
    private function leerExcel(string $ruta): array
    {
        $lector = new Reader;
        $lector->open($ruta);

        $hojas = [];

        foreach ($lector->getSheetIterator() as $hoja) {
            $renglones = [];

            foreach ($hoja->getRowIterator() as $renglon) {
                $renglones[] = $renglon->toArray();
            }

            $hojas[$hoja->getName()] = $renglones;
        }

        $lector->close();

        return $hojas;
    }
}
