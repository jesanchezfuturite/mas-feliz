<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Tamizaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReclasificarTamizajesTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test',
            'municipio' => 'Toluca',
            'dias_horario_servicio' => 'Lunes a viernes 9am-6pm',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'test@empresa.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 50,
            'password' => bcrypt('password'),
        ]);
    }

    private function tamizaje(array $datos): Tamizaje
    {
        return Tamizaje::create(array_merge([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'nivel_depresion' => 'Mínima o ausente',
        ], $datos));
    }

    public function test_el_intento_en_el_pasado_deja_de_ser_riesgo_agudo(): void
    {
        $tamizaje = $this->tamizaje([
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Riesgo Agudo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();

        $tamizaje->refresh();
        $this->assertSame('Positivo: requiere valoración posterior', $tamizaje->nivel_suicidio);
        $this->assertSame('Moderado', $tamizaje->nivel_riesgo_general);
    }

    public function test_la_ansiedad_grave_deja_de_ser_urgente(): void
    {
        $tamizaje = $this->tamizaje([
            'riesgo_ansiedad' => 21,
            'nivel_ansiedad' => 'Grave',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();

        $this->assertSame('Moderado', $tamizaje->refresh()->nivel_riesgo_general);
    }

    public function test_conserva_el_nivel_anterior_para_la_auditoria(): void
    {
        $tamizaje = $this->tamizaje([
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Riesgo Agudo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();

        $constancia = $tamizaje->refresh()->respuestas['reclasificacion_18_08_2026'];
        $this->assertSame('Riesgo Agudo', $constancia['nivel_suicidio_anterior']);
        $this->assertSame('Urgente', $constancia['nivel_riesgo_general_anterior']);
    }

    public function test_la_simulacion_no_escribe_nada(): void
    {
        $tamizaje = $this->tamizaje([
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Riesgo Agudo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $this->artisan('tamizajes:reclasificar')->assertSuccessful();

        $this->assertSame('Riesgo Agudo', $tamizaje->refresh()->nivel_suicidio);
    }

    public function test_no_toca_a_quien_declino_participar(): void
    {
        $tamizaje = $this->tamizaje([
            'consentimiento_otorgado' => false,
            'nivel_riesgo_general' => 'No participó',
        ]);

        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();

        $this->assertSame('No participó', $tamizaje->refresh()->nivel_riesgo_general);
    }

    public function test_correrlo_dos_veces_no_cambia_el_resultado(): void
    {
        $tamizaje = $this->tamizaje([
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Riesgo Agudo',
            'nivel_riesgo_general' => 'Urgente',
        ]);

        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();
        $this->artisan('tamizajes:reclasificar --aplicar')->assertSuccessful();

        $constancia = $tamizaje->refresh()->respuestas['reclasificacion_18_08_2026'];
        $this->assertSame('Riesgo Agudo', $constancia['nivel_suicidio_anterior']);
        $this->assertSame('Moderado', $tamizaje->nivel_riesgo_general);
    }
}
