<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Tamizaje;
use App\Support\ResultadoAsq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cómo se muestra el resultado del ASQ (Angélica, 27/08/2026): completo, con
 * la acción que corresponde, y con el positivo agudo distinguido como
 * "Positivo: Riesgo Agudo". Es texto de pantalla: `nivel_suicidio` sigue
 * guardando solo Negativo/Positivo.
 */
class ResultadoAsqTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa ASQ',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'asq@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
        ]);
    }

    private function tamizaje(string $nivelSuicidio, ?array $conductaSuicida = null): Tamizaje
    {
        return Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => $nivelSuicidio === 'Positivo' ? 1 : 0,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'nivel_depresion' => 'Mínima o ausente',
            'nivel_suicidio' => $nivelSuicidio,
            'nivel_riesgo_general' => 'Leve',
            'respuestas' => $conductaSuicida === null ? [] : ['conducta_suicida' => $conductaSuicida],
        ]);
    }

    public function test_el_negativo_muestra_prevencion(): void
    {
        $t = $this->tamizaje('Negativo');

        $this->assertSame('Negativo', ResultadoAsq::titulo($t));
        $this->assertSame('Prevención/ Promoción/ Psicoeducación', ResultadoAsq::accion($t));
    }

    public function test_el_positivo_no_agudo_pide_valoracion_adicional(): void
    {
        $t = $this->tamizaje('Positivo', [1 => 1, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);

        $this->assertSame('Positivo', ResultadoAsq::titulo($t));
        $this->assertSame(
            'Valoración psicológica adicional para confirmar/ descartar riesgo/agudeza',
            ResultadoAsq::accion($t)
        );
    }

    /**
     * Histórico anterior a la pregunta 5: la agudeza es desconocida, no
     * aguda. Se muestra como Positivo a secas; la espera ya la señala su
     * prioridad "Agudeza pendiente de confirmar".
     */
    public function test_el_positivo_sin_pregunta_5_no_se_marca_agudo(): void
    {
        $t = $this->tamizaje('Positivo', [1 => 1, 2 => 0, 3 => 0, 4 => 0]);

        $this->assertSame('Positivo', ResultadoAsq::titulo($t));
        $this->assertSame(
            'Valoración psicológica adicional para confirmar/ descartar riesgo/agudeza',
            ResultadoAsq::accion($t)
        );
    }

    public function test_el_positivo_agudo_se_distingue_con_su_propia_accion(): void
    {
        $t = $this->tamizaje('Positivo', [1 => 1, 2 => 0, 3 => 0, 4 => 1, 5 => 1]);

        $this->assertSame('Positivo: Riesgo Agudo', ResultadoAsq::titulo($t));
        $this->assertSame('Valoración/ Atención Especializada prioritaria', ResultadoAsq::accion($t));
    }

    /** Un valor histórico fuera de la escala se muestra tal cual, sin acción. */
    public function test_un_valor_historico_no_revienta(): void
    {
        $t = $this->tamizaje('Evaluación Adicional');

        $this->assertSame('Evaluación Adicional', ResultadoAsq::titulo($t));
        $this->assertNull(ResultadoAsq::accion($t));
    }

    /** La columna sigue guardando solo dos valores; el agudo es despliegue. */
    public function test_no_se_agrego_un_tercer_valor_a_la_columna(): void
    {
        $t = $this->tamizaje('Positivo', [5 => 1]);

        $this->assertSame('Positivo', $t->refresh()->nivel_suicidio);
    }
}
