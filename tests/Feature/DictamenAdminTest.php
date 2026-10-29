<?php

namespace Tests\Feature;

use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Filament\Resources\Empresas\Pages\ViewEmpresa;
use App\Filament\Resources\Empresas\Schemas\DictamenForm;
use App\Models\Empresa;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El administrador llena el «Dictamen de Gobierno» de cada empresa desde el
 * listado, desde la ficha y desde la propia tarjeta del dictamen, con los
 * cuatro niveles de madurez que pidió Angélica (28/10/2026).
 */
class DictamenAdminTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Servicio Minero Industrial',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'dictamen.admin@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Industrial',
            'numero_trabajadores' => 38,
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'apellidos' => 'De Prueba',
            'email' => 'admin.dictamen@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'web');
        Filament::setCurrentPanel('admin');
    }

    public function test_los_niveles_de_madurez_son_los_cuatro_que_pidio_angelica(): void
    {
        $this->assertSame(
            ['Inicial', 'Intermedio', 'Avanzado', 'Excelencia'],
            array_keys(Empresa::opcionesNivelMadurez())
        );

        $this->assertSame(
            ['Inicial', 'Intermedio', 'Avanzado', 'Excelencia'],
            array_values(Empresa::opcionesNivelMadurez())
        );
    }

    public function test_un_estatus_heredado_no_desaparece_del_desplegable(): void
    {
        // 'Validado' lo pone la validación de la autoevaluación.
        $this->assertArrayHasKey('Validado', Empresa::opcionesEstatusDistintivo('Validado'));
        $this->assertArrayNotHasKey('Validado', Empresa::opcionesEstatusDistintivo('Aprobado'));
    }

    public function test_la_ficha_de_la_empresa_ofrece_llenar_el_dictamen(): void
    {
        $this->get('/admin/empresas/'.$this->empresa->getRouteKey())
            ->assertSuccessful()
            ->assertSee('Dictamen de Gobierno')
            ->assertSee('Llenar dictamen')
            ->assertSee('Emitir Dictamen');
    }

    public function test_el_admin_dictamina_desde_la_ficha_de_la_empresa(): void
    {
        Livewire::test(ViewEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->callAction('emitir_dictamen', [
                'estatus_distintivo' => 'Aprobado',
                'nivel_madurez_asignado' => 'Intermedio',
                'fecha_dictamen' => '2026-11-04 10:00:00',
                'retroalimentacion_gobierno' => 'Cumple los criterios indispensables.',
            ])
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('Aprobado', $this->empresa->estatus_distintivo);
        $this->assertSame('Intermedio', $this->empresa->nivel_madurez_asignado);
        $this->assertSame('Cumple los criterios indispensables.', $this->empresa->retroalimentacion_gobierno);
        $this->assertSame('04/11/2026', $this->empresa->fecha_dictamen->format('d/m/Y'));
    }

    public function test_el_admin_dictamina_desde_la_tarjeta_del_dictamen(): void
    {
        Livewire::test(ViewEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->callAction(
                TestAction::make('dictamen')->schemaComponent('dictamen_gobierno'),
                [
                    'estatus_distintivo' => 'Aprobado',
                    'nivel_madurez_asignado' => 'Excelencia',
                    'fecha_dictamen' => '2026-11-04 10:00:00',
                    'retroalimentacion_gobierno' => 'Sobresaliente.',
                ]
            )
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('Aprobado', $this->empresa->estatus_distintivo);
        $this->assertSame('Excelencia', $this->empresa->nivel_madurez_asignado);
    }

    public function test_el_admin_dictamina_desde_el_listado(): void
    {
        Livewire::test(ListEmpresas::class)
            ->callAction(
                TestAction::make('emitir_dictamen')->table($this->empresa),
                [
                    'estatus_distintivo' => 'Aprobado',
                    'nivel_madurez_asignado' => 'Avanzado',
                    'fecha_dictamen' => '2026-11-04 10:00:00',
                    'retroalimentacion_gobierno' => 'Avanza al siguiente nivel.',
                ]
            )
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('Avanzado', $this->empresa->nivel_madurez_asignado);
    }

    public function test_una_empresa_no_aprobada_no_conserva_nivel_de_madurez(): void
    {
        $this->empresa->update([
            'estatus_distintivo' => 'Aprobado',
            'nivel_madurez_asignado' => 'Excelencia',
        ]);

        Livewire::test(ViewEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->callAction('emitir_dictamen', [
                'estatus_distintivo' => 'No aprobado',
                'fecha_dictamen' => '2026-11-04 10:00:00',
                'retroalimentacion_gobierno' => 'Falta participación en el tamizaje.',
            ])
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('No aprobado', $this->empresa->estatus_distintivo);
        $this->assertNull($this->empresa->nivel_madurez_asignado);
    }

    public function test_el_comentario_es_un_campo_abierto_que_no_obliga(): void
    {
        Livewire::test(ViewEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->callAction('emitir_dictamen', [
                'estatus_distintivo' => 'No aprobado',
                'retroalimentacion_gobierno' => null,
            ])
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('No aprobado', $this->empresa->estatus_distintivo);
        $this->assertNull($this->empresa->retroalimentacion_gobierno);
    }

    public function test_los_estatus_son_los_tres_que_pidio_angelica(): void
    {
        $this->assertSame(
            ['Aprobado', 'No aprobado', 'En revisión'],
            array_keys(Empresa::opcionesEstatusDistintivo())
        );
    }

    public function test_la_migracion_renombra_los_rechazados_ya_guardados(): void
    {
        $this->empresa->update(['estatus_distintivo' => 'Rechazado']);

        $migracion = require database_path('migrations/2026_10_28_120000_renombra_rechazado_a_no_aprobado.php');
        $migracion->up();

        $this->assertSame('No aprobado', $this->empresa->fresh()->estatus_distintivo);

        $migracion->down();

        $this->assertSame('Rechazado', $this->empresa->fresh()->estatus_distintivo);
    }

    public function test_el_comentario_se_llama_comentario_y_es_campo_abierto(): void
    {
        $comentario = collect(DictamenForm::getSchema())
            ->first(fn ($componente) => $componente->getName() === 'retroalimentacion_gobierno');

        $this->assertNotNull($comentario);
        $this->assertSame('Comentario', $comentario->getLabel());
    }
}
