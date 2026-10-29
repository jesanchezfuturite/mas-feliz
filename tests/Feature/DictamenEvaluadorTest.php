<?php

namespace Tests\Feature;

use App\Filament\Evaluador\Resources\EmpresaResource\Pages\EditEmpresa;
use App\Filament\Evaluador\Resources\EmpresaResource\Pages\ListEmpresas;
use App\Filament\Evaluador\Resources\EmpresaResource\Pages\ViewEmpresa;
use App\Filament\Evaluador\Widgets\EvaluadorStatsOverview;
use App\Models\Empresa;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El evaluador llena el mismo «Dictamen de Gobierno» que el administrador
 * (Angélica, 29/10/2026): desde el listado, desde la ficha y desde la
 * pantalla de edición, con los mismos estatus y niveles de madurez.
 */
class DictamenEvaluadorTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    private User $evaluador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = $this->empresa('Fundición del Norte', 'dictamen.evaluador@empresa.test');

        $this->evaluador = User::create([
            'name' => 'Evaluadora',
            'apellidos' => 'De Prueba',
            'email' => 'evaluadora.dictamen@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'evaluador',
        ]);

        $this->evaluador->empresas()->attach($this->empresa->id);

        $this->actingAs($this->evaluador, 'web');
        Filament::setCurrentPanel('evaluador');
    }

    private function empresa(string $nombre, string $correo): Empresa
    {
        return Empresa::create([
            'nombre_empresa' => $nombre,
            'municipio' => 'Monclova',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => $correo,
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Industrial',
            'numero_trabajadores' => 42,
        ]);
    }

    public function test_el_evaluador_dictamina_desde_el_listado(): void
    {
        Livewire::test(ListEmpresas::class)
            ->callAction(
                TestAction::make('emitir_dictamen')->table($this->empresa),
                [
                    'estatus_distintivo' => 'Aprobado',
                    'nivel_madurez_asignado' => 'Intermedio',
                    'fecha_dictamen' => '2026-11-04 10:00:00',
                    'retroalimentacion_gobierno' => 'Cumple los criterios indispensables.',
                ]
            )
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('Aprobado', $this->empresa->estatus_distintivo);
        $this->assertSame('Intermedio', $this->empresa->nivel_madurez_asignado);
        $this->assertSame('Cumple los criterios indispensables.', $this->empresa->retroalimentacion_gobierno);
        $this->assertSame('04/11/2026', $this->empresa->fecha_dictamen->format('d/m/Y'));
    }

    public function test_el_evaluador_dictamina_desde_la_ficha(): void
    {
        Livewire::test(ViewEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->callAction('emitir_dictamen', [
                'estatus_distintivo' => 'Aprobado',
                'nivel_madurez_asignado' => 'Excelencia',
                'fecha_dictamen' => '2026-11-04 10:00:00',
            ])
            ->assertHasNoActionErrors();

        $this->empresa->refresh();

        $this->assertSame('Aprobado', $this->empresa->estatus_distintivo);
        $this->assertSame('Excelencia', $this->empresa->nivel_madurez_asignado);
    }

    public function test_la_pantalla_de_edicion_guarda_el_dictamen(): void
    {
        Livewire::test(EditEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->fillForm([
                'estatus_distintivo' => 'Aprobado',
                'nivel_madurez_asignado' => 'Avanzado',
                'fecha_dictamen' => '2026-11-04 10:00:00',
                'retroalimentacion_gobierno' => 'Avanza al siguiente nivel.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->empresa->refresh();

        $this->assertSame('Aprobado', $this->empresa->estatus_distintivo);
        $this->assertSame('Avanzado', $this->empresa->nivel_madurez_asignado);
        $this->assertSame('Avanza al siguiente nivel.', $this->empresa->retroalimentacion_gobierno);
        $this->assertSame('04/11/2026', $this->empresa->fecha_dictamen->format('d/m/Y'));
    }

    public function test_al_editar_una_empresa_no_aprobada_no_le_queda_nivel(): void
    {
        $this->empresa->update([
            'estatus_distintivo' => 'Aprobado',
            'nivel_madurez_asignado' => 'Excelencia',
        ]);

        Livewire::test(EditEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->fillForm(['estatus_distintivo' => 'No aprobado'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->empresa->refresh();

        $this->assertSame('No aprobado', $this->empresa->estatus_distintivo);
        $this->assertNull($this->empresa->nivel_madurez_asignado);
    }

    public function test_la_edicion_propone_la_fecha_de_hoy_cuando_no_hay_dictamen(): void
    {
        $this->assertNull($this->empresa->fecha_dictamen);

        Livewire::test(EditEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->fillForm(['estatus_distintivo' => 'Aprobado', 'nivel_madurez_asignado' => 'Inicial'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->empresa->refresh();

        $this->assertNotNull($this->empresa->fecha_dictamen);
        $this->assertSame(now()->format('d/m/Y'), $this->empresa->fecha_dictamen->format('d/m/Y'));
    }

    public function test_el_evaluador_solo_puede_elegir_los_cuatro_niveles(): void
    {
        Livewire::test(EditEmpresa::class, ['record' => $this->empresa->getRouteKey()])
            ->fillForm([
                'estatus_distintivo' => 'Aprobado',
                // Así se llamaba antes del cambio que pidió Angélica.
                'nivel_madurez_asignado' => 'Sobresaliente',
            ])
            ->call('save')
            ->assertHasFormErrors(['nivel_madurez_asignado']);
    }

    public function test_el_evaluador_solo_ve_las_empresas_que_tiene_asignadas(): void
    {
        $ajena = $this->empresa('Empresa De Otro', 'ajena.evaluador@empresa.test');

        Livewire::test(ListEmpresas::class)
            ->assertCanSeeTableRecords([$this->empresa])
            ->assertCanNotSeeTableRecords([$ajena]);
    }

    public function test_el_tablero_cuenta_las_aprobadas_como_dictaminadas(): void
    {
        $this->empresa->update(['estatus_distintivo' => 'Aprobado']);

        $validada = $this->empresa('Empresa Validada', 'validada.evaluador@empresa.test');
        $validada->update(['estatus_distintivo' => 'Validado']);

        $enRevision = $this->empresa('Empresa En Revisión', 'revision.evaluador@empresa.test');

        $this->evaluador->empresas()->attach([$validada->id, $enRevision->id]);

        $widget = Livewire::test(EvaluadorStatsOverview::class)->instance();
        $stats = \Closure::bind(fn () => $this->getStats(), $widget, EvaluadorStatsOverview::class)();

        $this->assertSame(3, $stats[0]->getValue(), 'Empresas asignadas');
        $this->assertSame(2, $stats[2]->getValue(), 'Empresas dictaminadas');
    }
}
