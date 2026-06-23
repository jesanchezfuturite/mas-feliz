<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\Tamizaje;
use Livewire\Livewire;
use App\Livewire\ResponderTamizaje;

class ResponderTamizajeTest extends TestCase
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

    public function test_tamizaje_page_loads_with_valid_token(): void
    {
        $response = $this->get(route('tamizaje.publico', ['token' => $this->empresa->token_tamizaje]));
        $response->assertStatus(200);
        $response->assertSee('Empresa Test');
    }

    public function test_tamizaje_page_returns_404_with_invalid_token(): void
    {
        $response = $this->get(route('tamizaje.publico', ['token' => 'invalid-token-123']));
        $response->assertStatus(404);
    }

    public function test_consentimiento_requires_valid_consent_to_advance(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', null)
            ->call('irADemograficos')
            ->assertHasErrors(['consentimiento_otorgado' => 'required'])
            ->set('consentimiento_otorgado', 'no')
            ->call('irADemograficos')
            ->assertHasErrors(['consentimiento_otorgado' => 'in'])
            ->set('consentimiento_otorgado', 'si')
            ->call('irADemograficos')
            ->assertHasErrors([
                'declaracion_1' => 'accepted',
                'declaracion_2' => 'accepted',
                'declaracion_3' => 'accepted',
                'declaracion_4' => 'accepted',
                'declaracion_5' => 'accepted',
            ])
            ->set('declaracion_1', true)
            ->set('declaracion_2', true)
            ->set('declaracion_3', true)
            ->set('declaracion_4', true)
            ->set('declaracion_5', true)
            ->call('irADemograficos')
            ->assertHasNoErrors()
            ->assertSet('step', 'demograficos');
    }

    public function test_demographics_require_all_fields_to_advance(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('step', 'demograficos')
            ->call('irACuestionario')
            ->assertHasErrors([
                'nombre_completo' => 'required',
                'genero' => 'required',
                'edad' => 'required',
                'actividad_trabajo' => 'required',
                'tiempo_trabajando' => 'required',
            ])
            ->set('nombre_completo', 'Juan Pérez')
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Administrativas')
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->call('irACuestionario')
            ->assertHasNoErrors()
            ->assertSet('step', 'cuestionario');
    }

    public function test_tamizaje_validation_requires_all_fields(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'si')
            ->set('nombre_completo', 'Juan Pérez')
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Administrativas')
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('step', 'cuestionario')
            ->call('submit')
            ->assertHasErrors([
                'ansiedad_1' => 'required',
                'ansiedad_2' => 'required',
                'ansiedad_3' => 'required',
                'depresion_1' => 'required',
                'depresion_2' => 'required',
                'depresion_3' => 'required',
                'suicidio_1' => 'required',
                'suicidio_2' => 'required',
                'suicidio_3' => 'required',
            ]);
    }

    public function test_tamizaje_calculates_leve_risk_correctly(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'si')
            ->set('nombre_completo', 'Juan Pérez')
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Administrativas')
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('ansiedad_1', '0')
            ->set('ansiedad_2', '1')
            ->set('ansiedad_3', '0')
            ->set('ansiedad_4', '0')
            ->set('ansiedad_5', '0')
            ->set('ansiedad_6', '0')
            ->set('ansiedad_7', '0')
            ->set('depresion_1', '1')
            ->set('depresion_2', '0')
            ->set('depresion_3', '0')
            ->set('depresion_4', '0')
            ->set('depresion_5', '0')
            ->set('depresion_6', '0')
            ->set('depresion_7', '0')
            ->set('depresion_8', '0')
            ->set('depresion_9', '0')
            ->set('suicidio_1', '0')
            ->set('suicidio_2', '0')
            ->set('suicidio_3', '0')
            ->set('suicidio_4', '0')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'nombre_completo' => 'Juan Pérez',
            'genero' => 'Hombre',
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Administrativas',
            'tiempo_trabajando' => 'De 6 meses a 1 año',
            'riesgo_ansiedad' => 1,
            'riesgo_depresion' => 1,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => 'Leve',
        ]);
    }

    public function test_tamizaje_calculates_urgente_risk_due_to_suicidal_score(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'si')
            ->set('nombre_completo', 'Juan Pérez')
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Administrativas')
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('ansiedad_1', '0')
            ->set('ansiedad_2', '0')
            ->set('ansiedad_3', '0')
            ->set('ansiedad_4', '0')
            ->set('ansiedad_5', '0')
            ->set('ansiedad_6', '0')
            ->set('ansiedad_7', '0')
            ->set('depresion_1', '0')
            ->set('depresion_2', '0')
            ->set('depresion_3', '0')
            ->set('depresion_4', '0')
            ->set('depresion_5', '0')
            ->set('depresion_6', '0')
            ->set('depresion_7', '0')
            ->set('depresion_8', '0')
            ->set('depresion_9', '0')
            ->set('suicidio_1', '1')
            ->set('suicidio_2', '0')
            ->set('suicidio_3', '0')
            ->set('suicidio_4', '0')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 1,
            'nivel_riesgo_general' => 'Urgente',
        ]);
    }

    public function test_declined_consent_logs_message(): void
    {
        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'Colaborador declinó participar') 
                    && str_contains($message, (string)$this->empresa->id)
                    && str_contains($message, $this->empresa->nombre_empresa);
            }));

        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'no');
    }
}
