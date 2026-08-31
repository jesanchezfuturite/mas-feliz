<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use App\Filament\Empresa\Widgets\DashboardStatsOverview;
use App\Filament\Empresa\Widgets\EstadisticaTamizajeWidget;
use App\Filament\Empresa\Widgets\RiesgosGeneralesChart;
use App\Models\Empresa;
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

    private function empresa(string $correo, bool $resultadosVisibles = true): Empresa
    {
        return Empresa::create([
            'nombre_empresa' => 'Empresa '.$correo,
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => $correo,
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Gobierno',
            'numero_trabajadores' => 100,
            'resultados_tamizaje_visibles' => $resultadosVisibles,
        ]);
    }

    /**
     * Algunas instituciones pidieron no ver sus resultados: el admin enciende el
     * interruptor global y lo apaga solo a esas empresas desde el listado de
     * Empresas. El apagador por empresa solo restringe a esa empresa.
     */
    public function test_con_el_global_encendido_se_puede_apagar_una_sola_empresa(): void
    {
        $this->configurar(herramientas: true, resultados: true);

        $oculta = $this->empresa('oculta@gobierno.test', resultadosVisibles: false);
        $visible = $this->empresa('visible@empresa.test');

        $this->assertFalse(Setting::resultadosTamizajeVisibles($oculta));
        $this->assertTrue(Setting::resultadosTamizajeVisibles($visible));
    }

    public function test_la_empresa_apagada_pierde_el_listado_y_los_widgets(): void
    {
        $this->configurar(herramientas: true, resultados: true);

        $this->actingAs($this->empresa('oculta@gobierno.test', resultadosVisibles: false), 'empresa');

        $this->assertFalse(TamizajeResource::canAccess());
        $this->assertFalse(RiesgosGeneralesChart::canView());
        $this->assertFalse(EstadisticaTamizajeWidget::canView());
    }

    public function test_la_empresa_encendida_conserva_el_listado_y_los_widgets(): void
    {
        $this->configurar(herramientas: true, resultados: true);

        $this->actingAs($this->empresa('visible@empresa.test'), 'empresa');

        $this->assertTrue(TamizajeResource::canAccess());
        $this->assertTrue(RiesgosGeneralesChart::canView());
        $this->assertTrue(EstadisticaTamizajeWidget::canView());
    }

    /** El global sigue mandando: apagado, oculta aunque la empresa esté encendida. */
    public function test_el_global_apagado_manda_sobre_la_empresa_encendida(): void
    {
        $this->configurar(herramientas: true, resultados: false);

        $encendida = $this->empresa('encendida@empresa.test');

        $this->assertFalse(Setting::resultadosTamizajeVisibles($encendida));
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
