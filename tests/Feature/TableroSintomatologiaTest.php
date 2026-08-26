<?php

namespace Tests\Feature;

use App\Filament\Empresa\Widgets\EstadisticaTamizajeWidget;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\Tamizaje;
use App\Support\PrioridadAtencion;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cruce demográfico por sintomatología (petición de fondo de Angélica,
 * 21/08/2026): los perfiles de sexo/edad/antigüedad/función se cuentan por el
 * nivel del instrumento elegido en el selector —GAD-7, PHQ-9 o ASQ—, no solo
 * por la prioridad de atención, que queda como una opción más.
 */
class TableroSintomatologiaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'global_config'], [
            'herramientas_empresa_activas' => true,
            'resultados_tamizaje_visibles' => true,
        ]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Sintomatología',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'sintomatologia@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 40,
        ]);

        // El desglose se destraba cuando la empresa ya envió su autoevaluación.
        $this->empresa->autoevaluaciones()->create(['estatus' => 'En revisión']);

        // Dos perfiles con sintomatologías distintas: la ansiedad separa a los
        // hombres (Grave) de las mujeres (Leve); la prioridad los separa en
        // Alta y Leve.
        foreach ([
            ['Hombre', 'Grave', 'Mínima o ausente', PrioridadAtencion::ALTA],
            ['Mujer', 'Leve', 'Mínima o ausente', PrioridadAtencion::LEVE],
        ] as $i => [$genero, $ansiedad, $depresion, $prioridad]) {
            Tamizaje::create([
                'empresa_id' => $this->empresa->id,
                'nombre_completo' => 'Persona '.$i,
                'consentimiento_otorgado' => true,
                'genero' => $genero,
                'edad' => '25 a 34 años',
                'actividad_trabajo' => 'Administrativas',
                'riesgo_ansiedad' => 0,
                'riesgo_depresion' => 0,
                'riesgo_conducta_suicida' => 0,
                'nivel_ansiedad' => $ansiedad,
                'nivel_depresion' => $depresion,
                'nivel_suicidio' => 'Negativo',
                'nivel_riesgo_general' => $prioridad,
            ]);
        }

        Filament::setCurrentPanel(Filament::getPanel('empresa'));
        $this->actingAs($this->empresa, 'empresa');
    }

    public function test_por_defecto_cruza_por_ansiedad_y_sin_nota_legal(): void
    {
        Livewire::test(EstadisticaTamizajeWidget::class)
            ->assertSet('instrumento', 'ansiedad')
            ->assertViewHas('tituloPerfil', 'Síntomas de Ansiedad (GAD-7)')
            ->assertViewHas('escala', function (array $escala) {
                return array_column($escala, 'label') === ['Mínima o sin ansiedad', 'Leve', 'Moderada', 'Grave'];
            })
            ->assertViewHas('dimensiones', function (array $dimensiones) {
                $porSexo = collect($dimensiones)->firstWhere('titulo', 'Por sexo')['datos'];

                return $porSexo['Hombre']['Grave'] === 1
                    && $porSexo['Hombre']['Leve'] === 0
                    && $porSexo['Mujer']['Leve'] === 1
                    && $porSexo['Mujer']['total'] === 1;
            })
            // La nota es una aclaración sobre la prioridad, no sobre el GAD-7.
            ->assertViewHas('nota', null);
    }

    public function test_el_selector_cambia_la_escala_del_cruce(): void
    {
        Livewire::test(EstadisticaTamizajeWidget::class)
            ->set('instrumento', 'prioridad')
            ->assertViewHas('tituloPerfil', PrioridadAtencion::ETIQUETA)
            ->assertViewHas('escala', function (array $escala) {
                return array_column($escala, 'label') === PrioridadAtencion::ESCALA;
            })
            ->assertViewHas('dimensiones', function (array $dimensiones) {
                $porSexo = collect($dimensiones)->firstWhere('titulo', 'Por sexo')['datos'];

                return $porSexo['Hombre'][PrioridadAtencion::ALTA] === 1
                    && $porSexo['Mujer'][PrioridadAtencion::LEVE] === 1;
            })
            ->assertViewHas('nota', PrioridadAtencion::NOTA);
    }

    public function test_el_asq_cruza_con_sus_dos_resultados(): void
    {
        Livewire::test(EstadisticaTamizajeWidget::class)
            ->set('instrumento', 'suicidio')
            ->assertViewHas('escala', function (array $escala) {
                return array_column($escala, 'label') === ['Negativo', 'Positivo'];
            })
            ->assertViewHas('dimensiones', function (array $dimensiones) {
                $porSexo = collect($dimensiones)->firstWhere('titulo', 'Por sexo')['datos'];

                return $porSexo['Hombre']['Negativo'] === 1
                    && $porSexo['Mujer']['Negativo'] === 1;
            });
    }

    public function test_un_instrumento_invalido_cae_al_predeterminado(): void
    {
        Livewire::test(EstadisticaTamizajeWidget::class)
            ->set('instrumento', 'hackeo')
            ->assertViewHas('tituloPerfil', 'Síntomas de Ansiedad (GAD-7)');
    }

    public function test_el_selector_se_dibuja_con_las_cuatro_opciones(): void
    {
        Livewire::test(EstadisticaTamizajeWidget::class)
            ->assertSeeHtml('wire:model.live="instrumento"')
            ->assertSee('Síntomas de Ansiedad (GAD-7)')
            ->assertSee('Síntomas de Depresión (PHQ-9)')
            ->assertSee('Indicadores de Conducta suicida')
            ->assertSee(PrioridadAtencion::ETIQUETA);
    }
}
