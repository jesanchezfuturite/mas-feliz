<?php

namespace Tests\Feature;

use App\Filament\Widgets\AvancePorEmpresa;
use App\Models\Empresa;
use App\Models\Tamizaje;
use App\Models\User;
use App\Support\PrioridadAtencion;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Angélica (31/08/2026): el "Avance por organización" mostraba un bulto "En
 * riesgo" que sumaba cuatro niveles y se leía como dato inflado. En su lugar
 * va el desglose por prioridad de atención: cuántos Urgente, Alta, Moderada,
 * Leve y agudeza pendiente tiene cada organización.
 */
class AvancePorEmpresaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Avance',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'avance@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
        ]);
    }

    private function tamizaje(string $nivel): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => $nivel !== PrioridadAtencion::NO_PARTICIPO,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => $nivel,
        ]);
    }

    public function test_los_alias_cubren_toda_la_escala_sin_duplicarla(): void
    {
        $conteos = AvancePorEmpresa::conteosPorPrioridad();

        $this->assertSame(PrioridadAtencion::ESCALA, array_values($conteos));
        $this->assertSame('Urgente', $conteos['prioridad_urgente']);
        $this->assertSame(
            'Agudeza pendiente de confirmar',
            $conteos['prioridad_agudeza_pendiente_de_confirmar'],
        );
    }

    public function test_el_listado_desglosa_por_prioridad_en_vez_del_bulto(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'apellidos' => 'Avance',
            'email' => 'admin.avance@test.com',
            'password' => bcrypt('secret'),
            'estatus' => true,
            'role' => 'admin',
        ]);

        $this->tamizaje(PrioridadAtencion::URGENTE);
        $this->tamizaje(PrioridadAtencion::MODERADA);
        $this->tamizaje(PrioridadAtencion::MODERADA);
        $this->tamizaje(PrioridadAtencion::LEVE);
        $this->tamizaje(PrioridadAtencion::NO_PARTICIPO);

        $this->actingAs($admin, 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $tabla = Livewire::test(AvancePorEmpresa::class);

        $tabla->assertSuccessful()
            ->assertSee('Empresa Avance')
            ->assertSee(PrioridadAtencion::NOTA);

        // Cada nivel de la escala es columna propia...
        foreach (PrioridadAtencion::ESCALA as $nivel) {
            $tabla->assertSee($nivel);
        }

        // ...y el bulto "En riesgo" ya no aparece como encabezado.
        $tabla->assertDontSee('En riesgo');

        // Los conteos por nivel llegan al registro que pinta la fila.
        $registro = $tabla->instance()->getTableRecords()->firstWhere('id', $this->empresa->id);

        $this->assertSame(1, (int) $registro->prioridad_urgente);
        $this->assertSame(2, (int) $registro->prioridad_moderada);
        $this->assertSame(1, (int) $registro->prioridad_leve);
        $this->assertSame(0, (int) $registro->prioridad_alta);
        $this->assertSame(0, (int) $registro->prioridad_agudeza_pendiente_de_confirmar);
    }
}
