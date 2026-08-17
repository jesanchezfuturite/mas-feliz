<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use App\Filament\Empresa\Widgets\DashboardStatsOverview;
use App\Filament\Empresa\Widgets\EstadisticaTamizajeWidget;
use App\Filament\Empresa\Widgets\RiesgosGeneralesChart;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Angélica pidió ocultar en producción el listado de resultados del tamizaje
 * colaborador por colaborador, dejando visible solo la vista general, mientras
 * se aclara en reunión cómo debe interpretarse el resultado del instrumento.
 *
 * Es un interruptor y no un borrado justamente porque es temporal.
 */
class ResultadosTamizajeOcultosTest extends TestCase
{
    use RefreshDatabase;

    private function configurar(bool $herramientas, ?bool $resultados): void
    {
        $valores = ['herramientas_empresa_activas' => $herramientas];

        if ($resultados !== null) {
            $valores['resultados_tamizaje_visibles'] = $resultados;
        }

        Setting::updateOrCreate(['key' => 'global_config'], $valores);
    }

    public function test_con_el_interruptor_encendido_el_listado_es_accesible(): void
    {
        $this->configurar(herramientas: true, resultados: true);

        $this->assertTrue(TamizajeResource::canAccess());
    }

    public function test_con_el_interruptor_apagado_el_listado_se_oculta(): void
    {
        $this->configurar(herramientas: true, resultados: false);

        $this->assertFalse(Setting::resultadosTamizajeVisibles());
        $this->assertFalse(TamizajeResource::canAccess());
    }

    /**
     * El mismo interruptor apaga las secciones de la página "Diagnóstico y
     * Tamizaje" que muestran niveles de riesgo: distribución general y desglose
     * por instrumento. Filament filtra los widgets de encabezado con `canView()`.
     */
    public function test_con_el_interruptor_apagado_se_ocultan_los_widgets_de_riesgo(): void
    {
        $this->configurar(herramientas: true, resultados: false);

        $this->assertFalse(RiesgosGeneralesChart::canView());
        $this->assertFalse(EstadisticaTamizajeWidget::canView());
    }

    public function test_con_el_interruptor_encendido_los_widgets_de_riesgo_se_ven(): void
    {
        $this->configurar(herramientas: true, resultados: true);

        $this->assertTrue(RiesgosGeneralesChart::canView());
        $this->assertTrue(EstadisticaTamizajeWidget::canView());
    }

    /** El avance de participación no depende del interruptor: siempre se muestra. */
    public function test_el_widget_de_participacion_no_se_oculta(): void
    {
        $this->configurar(herramientas: true, resultados: false);

        $this->assertTrue(DashboardStatsOverview::canView());
    }

    /** El interruptor global de herramientas sigue mandando por encima. */
    public function test_sin_herramientas_activas_no_se_ve_aunque_el_interruptor_este_encendido(): void
    {
        $this->configurar(herramientas: false, resultados: true);

        $this->assertFalse(TamizajeResource::canAccess());
    }

    /**
     * Por omisión se muestran: el despliegue no debe cambiar el comportamiento
     * de ningún ambiente por sí solo, se apaga a mano donde haga falta.
     */
    public function test_sin_configuracion_previa_los_resultados_se_muestran(): void
    {
        $this->assertTrue(Setting::resultadosTamizajeVisibles());

        // Y con la fila creada pero sin tocar la columna, igual.
        $this->configurar(herramientas: true, resultados: null);

        $this->assertTrue(Setting::resultadosTamizajeVisibles());
        $this->assertTrue(TamizajeResource::canAccess());
    }
}
