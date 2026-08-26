<?php

namespace Tests\Feature;

use App\Livewire\ResponderTamizaje;
use App\Models\Empresa;
use App\Models\Tamizaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Tests\TestCase;

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
            ->set('telefono', '8441234567')
            ->call('irACuestionario')
            ->assertHasNoErrors()
            ->assertSet('step', 'cuestionario');
    }

    /**
     * Las columnas son VARCHAR(191) por `Schema::defaultStringLength(191)`. Sin
     * tope en la validación, un texto largo pasaba el paso de demográficos y
     * reventaba con "Data too long" al insertar, perdiendo el tamizaje ya
     * contestado. Ocurrió en producción el 13/08/2026 con
     * `actividad_trabajo_otra`, que no tenía máximo alguno.
     */
    public function test_los_textos_largos_se_rechazan_antes_de_llegar_a_la_base(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('step', 'demograficos')
            ->set('nombre_completo', str_repeat('a', 192))
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Otra')
            ->set('actividad_trabajo_otra', str_repeat('b', 192))
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('telefono', '8711234567')
            ->call('irACuestionario')
            ->assertHasErrors([
                'nombre_completo' => 'max',
                'actividad_trabajo_otra' => 'max',
            ])
            ->assertSet('step', 'demograficos');
    }

    /** Justo en el límite sí debe pasar. */
    public function test_un_texto_de_191_caracteres_es_valido(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('step', 'demograficos')
            ->set('nombre_completo', str_repeat('a', 191))
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Otra')
            ->set('actividad_trabajo_otra', str_repeat('b', 191))
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('telefono', '8711234567')
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
            ->set('telefono', '8441234567')
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

    public function test_un_si_no_agudo_queda_positivo_y_prioridad_alta(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'si')
            ->set('nombre_completo', 'Juan Pérez')
            ->set('genero', 'Hombre')
            ->set('edad', '25 a 34 años')
            ->set('actividad_trabajo', 'Administrativas')
            ->set('tiempo_trabajando', 'De 6 meses a 1 año')
            ->set('telefono', '8441234567')
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
            ->set('suicidio_5', '0')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => true,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 1,
            'nivel_suicidio' => 'Positivo',
            'nivel_riesgo_general' => 'Alta',
        ]);
    }

    public function test_declined_consent_logs_message(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'Colaborador declinó participar')
                    && str_contains($message, (string) $this->empresa->id)
                    && str_contains($message, $this->empresa->nombre_empresa);
            }));

        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'no');
    }

    /**
     * Base de respuestas "todo en cero": ansiedad y depresión mínimas y las
     * cuatro de conducta suicida en No. Cada prueba altera solo lo que evalúa.
     */
    private function cuestionario(array $respuestas = []): array
    {
        $base = [
            'consentimiento_otorgado' => 'si',
            'nombre_completo' => 'Juan Pérez',
            'genero' => 'Hombre',
            'edad' => '25 a 34 años',
            'actividad_trabajo' => 'Administrativas',
            'tiempo_trabajando' => 'De 6 meses a 1 año',
            'telefono' => '8441234567',
        ];

        foreach (range(1, 7) as $i) {
            $base['ansiedad_'.$i] = '0';
        }
        foreach (range(1, 9) as $i) {
            $base['depresion_'.$i] = '0';
        }
        foreach (range(1, 4) as $i) {
            $base['suicidio_'.$i] = '0';
        }

        return array_merge($base, $respuestas);
    }

    private function responder(array $respuestas = [])
    {
        $componente = Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje]);

        foreach ($this->cuestionario($respuestas) as $campo => $valor) {
            $componente->set($campo, $valor);
        }

        return $componente->call('submit');
    }

    /**
     * El caso que originó la revisión del 18/08/2026: la persona reportó un
     * intento en el pasado —pregunta 4, que abarca toda la vida— sin ningún
     * síntoma actual, y el sistema la marcaba como emergencia.
     */
    public function test_intento_en_el_pasado_sin_agudeza_no_es_urgente(): void
    {
        $this->responder(['suicidio_4' => '1', 'suicidio_5' => '0'])
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_ansiedad' => 'Mínima o sin ansiedad',
            'nivel_depresion' => 'Mínima o ausente',
            'nivel_suicidio' => 'Positivo',
            'nivel_riesgo_general' => 'Alta',
        ]);
    }

    /**
     * La agudeza no cambia el resultado del ASQ —el recuadro de Angélica solo
     * define "Negativo" y "Positivo"—: lo que hace la pregunta 5 en "Sí" es
     * subir la prioridad de atención a "Urgente".
     */
    public function test_la_pregunta_cinco_sube_la_prioridad_sin_cambiar_el_resultado(): void
    {
        $this->responder(['suicidio_4' => '1', 'suicidio_5' => '1'])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_suicidio' => 'Positivo',
            'nivel_riesgo_general' => 'Urgente',
        ]);
    }

    public function test_ansiedad_y_depresion_graves_son_prioridad_alta_no_urgente(): void
    {
        $graves = [];
        foreach (range(1, 7) as $i) {
            $graves['ansiedad_'.$i] = '3';
        }
        foreach (range(1, 9) as $i) {
            $graves['depresion_'.$i] = '3';
        }

        $this->responder($graves)->assertHasNoErrors();

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'riesgo_ansiedad' => 21,
            'riesgo_depresion' => 27,
            'nivel_ansiedad' => 'Grave',
            'nivel_depresion' => 'Grave',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Alta',
        ]);
    }

    public function test_la_pregunta_cinco_es_obligatoria_si_hubo_algun_si(): void
    {
        $this->responder(['suicidio_2' => '1'])
            ->assertHasErrors(['suicidio_5' => 'required']);

        $this->assertDatabaseCount('tamizajes', 0);
    }

    public function test_la_pregunta_cinco_no_se_pide_si_las_cuatro_son_no(): void
    {
        $this->responder()
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Leve',
        ]);
    }

    public function test_la_pregunta_cinco_se_limpia_si_la_persona_se_retracta(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('suicidio_3', '1')
            ->set('suicidio_5', '1')
            ->assertSet('suicidio_5', '1')
            ->set('suicidio_3', '0')
            ->assertSet('suicidio_5', null);
    }

    public function test_se_guarda_cada_respuesta_por_separado(): void
    {
        $this->responder([
            'ansiedad_1' => '2',
            'depresion_9' => '3',
            'suicidio_1' => '1',
            'suicidio_5' => '0',
        ])->assertHasNoErrors();

        $tamizaje = Tamizaje::firstOrFail();

        $this->assertSame(2, $tamizaje->respuestas['ansiedad'][1]);
        $this->assertSame(0, $tamizaje->respuestas['ansiedad'][7]);
        $this->assertSame(3, $tamizaje->respuestas['depresion'][9]);
        $this->assertSame(1, $tamizaje->respuestas['conducta_suicida'][1]);
        $this->assertSame(0, $tamizaje->respuestas['conducta_suicida'][5]);
    }

    public function test_la_confirmacion_muestra_la_linea_de_vida(): void
    {
        $this->responder()
            ->assertSee('¿Necesitas hablar con alguien?')
            ->assertSee('Línea de la Vida')
            ->assertSee('800 953 6453')
            ->assertSee('Ninguno de estos cuestionarios reemplaza un diagnóstico médico formal');
    }

    public function test_quien_declina_tambien_ve_la_linea_de_vida(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'no')
            ->call('enviarNoParticipacion')
            ->assertSet('success', true)
            ->assertSee('Línea de la Vida')
            ->assertSee('800 953 6453');
    }

    public function test_los_modulos_indican_el_periodo_de_las_dos_semanas(): void
    {
        Livewire::test(ResponderTamizaje::class, ['token' => $this->empresa->token_tamizaje])
            ->set('consentimiento_otorgado', 'si')
            ->set('step', 'cuestionario')
            ->assertSeeHtml('Responda, en las últimas dos semanas, ¿con qué frecuencia le han molestado los siguientes problemas?');
    }

    /**
     * Escala de prioridad de atención del 21/08/2026, corregida por Angélica
     * el 26/08/2026: los niveles Leve con ASQ negativo quedan en prioridad
     * Leve (su tabla original los ponía en Moderada y le inflaba ese nivel);
     * la severidad de cualquiera de los dos instrumentos sube a Alta.
     */
    public function test_ansiedad_leve_sin_asq_es_prioridad_leve(): void
    {
        // 5 puntos en el GAD-7 es el umbral de "Leve".
        $this->responder(['ansiedad_1' => '2', 'ansiedad_2' => '3'])->assertHasNoErrors();

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_ansiedad' => 'Leve',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Leve',
        ]);
    }

    public function test_ansiedad_moderada_sin_asq_es_prioridad_moderada(): void
    {
        // 10 puntos en el GAD-7 es el umbral de "Moderada".
        $respuestas = [];
        foreach (range(1, 4) as $i) {
            $respuestas['ansiedad_'.$i] = '3';
        }

        $this->responder($respuestas)->assertHasNoErrors();

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_ansiedad' => 'Moderada',
            'nivel_suicidio' => 'Negativo',
            'nivel_riesgo_general' => 'Moderada',
        ]);
    }

    public function test_depresion_moderadamente_grave_sin_asq_es_prioridad_alta(): void
    {
        // 15 puntos en el PHQ-9 es el umbral de "Moderadamente grave".
        $respuestas = [];
        foreach (range(1, 5) as $i) {
            $respuestas['depresion_'.$i] = '3';
        }

        $this->responder($respuestas)->assertHasNoErrors();

        $this->assertDatabaseHas('tamizajes', [
            'empresa_id' => $this->empresa->id,
            'nivel_depresion' => 'Moderadamente grave',
            'nivel_riesgo_general' => 'Alta',
        ]);
    }

    /**
     * Angélica retiró el 25/08/2026 la pregunta que se derivaba de la 4
     * ("¿Cuándo fue el último intento?"): a esas alturas de la aplicación la
     * habrían contestado muy pocos y no serviría para reportar. El ASQ se queda
     * con sus cinco preguntas, y la 5 sigue siendo la que establece la agudeza.
     */
    public function test_ya_no_se_pregunta_por_el_ultimo_intento(): void
    {
        $this->responder(['suicidio_4' => '1', 'suicidio_5' => '0'])
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $tamizaje = Tamizaje::where('empresa_id', $this->empresa->id)->sole();

        $this->assertArrayNotHasKey('ultimo_intento', $tamizaje->respuestas['conducta_suicida']);
    }
}
