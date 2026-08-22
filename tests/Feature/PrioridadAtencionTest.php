<?php

namespace Tests\Feature;

use App\Filament\Empresa\Widgets\EstadisticaTamizajeWidget;
use App\Livewire\ResponderTamizaje;
use App\Models\Empresa;
use App\Models\Setting;
use App\Models\Tamizaje;
use App\Support\PrioridadAtencion;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Escala de prioridad de atención que Angélica definió el 21/08/2026, tabla por
 * tabla. Es la especificación del cambio: si alguien mueve un umbral, aquí se
 * ve contra qué renglón de su documento se está midiendo.
 */
class PrioridadAtencionTest extends TestCase
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
            'nombre_empresa' => 'Empresa Prioridad',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'prioridad@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 40,
        ]);
    }

    /**
     * Cada caso es un renglón de la tabla del documento "AJUSTES +FELIZ".
     *
     * @return array<string, array{0: ?string, 1: ?string, 2: int, 3: ?int, 4: string}>
     */
    public static function renglonesDeLaTabla(): array
    {
        return [
            'mínima + mínima + ASQ negativo' => ['Mínima o sin ansiedad', 'Mínima o ausente', 0, null, PrioridadAtencion::LEVE],
            'ansiedad leve + ASQ negativo' => ['Leve', 'Mínima o ausente', 0, null, PrioridadAtencion::MODERADA],
            'depresión moderada + ASQ negativo' => ['Mínima o sin ansiedad', 'Moderada', 0, null, PrioridadAtencion::MODERADA],
            'ansiedad grave + ASQ negativo' => ['Grave', 'Mínima o ausente', 0, null, PrioridadAtencion::ALTA],
            'depresión mod. grave + ASQ negativo' => ['Mínima o sin ansiedad', 'Moderadamente grave', 0, null, PrioridadAtencion::ALTA],
            'depresión grave + ASQ negativo' => ['Mínima o sin ansiedad', 'Grave', 0, null, PrioridadAtencion::ALTA],
            // La severidad manda: moderada + grave no baja a Moderada.
            'ansiedad moderada + depresión grave' => ['Moderada', 'Grave', 0, null, PrioridadAtencion::ALTA],
            'ASQ positivo no agudo, sin síntomas' => ['Mínima o sin ansiedad', 'Mínima o ausente', 1, 0, PrioridadAtencion::ALTA],
            'ASQ positivo no agudo, con síntomas graves' => ['Grave', 'Grave', 3, 0, PrioridadAtencion::ALTA],
            'ASQ positivo con pregunta 5 en Sí' => ['Mínima o sin ansiedad', 'Mínima o ausente', 1, 1, PrioridadAtencion::URGENTE],
            // Histórico: el ASQ salió positivo y la pregunta 5 no existía.
            'ASQ positivo sin agudeza explorada' => ['Mínima o sin ansiedad', 'Mínima o ausente', 1, null, PrioridadAtencion::AGUDEZA_PENDIENTE],
        ];
    }

    #[DataProvider('renglonesDeLaTabla')]
    public function test_la_tabla_de_angelica_se_respeta_renglon_por_renglon(
        ?string $ansiedad,
        ?string $depresion,
        int $puntajeSuicidio,
        ?int $agudeza,
        string $esperado
    ): void {
        $this->assertSame(
            $esperado,
            PrioridadAtencion::calcular($ansiedad, $depresion, $puntajeSuicidio, $agudeza)
        );
    }

    public function test_el_listado_de_tamizajes_usa_la_etiqueta_y_la_nota_nuevas(): void
    {
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => 'Ana López',
            'consentimiento_otorgado' => true,
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Administrativas',
            'riesgo_ansiedad' => 16,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_ansiedad' => 'Grave',
            'nivel_depresion' => 'Mínima o ausente',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => PrioridadAtencion::ALTA,
        ]);

        $this->actingAs($this->empresa, 'empresa')
            ->get('/tablero/tamizajes')
            ->assertSuccessful()
            ->assertSee('Prioridad de atención')
            // Ya no debe llamarse "Riesgo General".
            ->assertDontSee('Riesgo General')
            ->assertSee('Alta')
            ->assertSee('clasificación operativa del Distintivo', false);
    }

    /**
     * Angélica reportó el 21/08/2026 que el desglose por instrumento mostraba
     * "Positivo: requiere valoración posterior" y "Evaluación Adicional" como
     * dos categorías, la segunda siempre en cero. "Evaluación Adicional" es el
     * nombre que tuvo el positivo antes de la corrección; el ASQ tiene tres
     * resultados, no cuatro.
     */
    public function test_el_desglose_del_asq_solo_muestra_los_tres_resultados(): void
    {
        // El desglose se destraba cuando la empresa ya envió su autoevaluación.
        $this->empresa->autoevaluaciones()->create(['estatus' => 'En revisión']);

        foreach ([['Negativo', 0], ['Positivo: requiere valoración posterior', 1], ['Riesgo Agudo', 2]] as $i => [$nivelSuicidio, $puntaje]) {
            Tamizaje::create([
                'empresa_id' => $this->empresa->id,
                'nombre_completo' => 'Persona '.$i,
                'consentimiento_otorgado' => true,
                'riesgo_ansiedad' => 0,
                'riesgo_depresion' => 0,
                'riesgo_conducta_suicida' => $puntaje,
                'nivel_ansiedad' => 'Mínima o sin ansiedad',
                'nivel_depresion' => 'Mínima o ausente',
                'nivel_suicidio' => $nivelSuicidio,
                'nivel_riesgo_general' => PrioridadAtencion::LEVE,
            ]);
        }

        Filament::setCurrentPanel(Filament::getPanel('empresa'));
        $this->actingAs($this->empresa, 'empresa');

        $widget = Livewire::test(EstadisticaTamizajeWidget::class);

        $widget->assertSee('Riesgo suicida')
            ->assertSee('Negativo')
            ->assertSee('Riesgo Agudo')
            // El cuarto nivel ya no se dibuja.
            ->assertDontSee('Evaluación Adicional');
    }

    public function test_los_niveles_del_asq_son_exactamente_tres(): void
    {
        $this->assertSame(
            ['Negativo', 'Positivo: requiere valoración posterior', 'Riesgo Agudo'],
            ResponderTamizaje::NIVELES_SUICIDIO
        );
    }
}
