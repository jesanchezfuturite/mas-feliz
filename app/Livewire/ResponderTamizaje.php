<?php

namespace App\Livewire;

use App\Models\Empresa;
use App\Models\Tamizaje;
use App\Support\PrioridadAtencion;
use Livewire\Component;

class ResponderTamizaje extends Component
{
    public $token;

    public $empresa;

    // Flow control fields
    public $step = 'consentimiento';

    public $consentimiento_otorgado = null;

    // Declarations checklist
    public bool $declaracion_1 = false;

    public bool $declaracion_2 = false;

    public bool $declaracion_3 = false;

    public bool $declaracion_4 = false;

    public bool $declaracion_5 = false;

    // Sociodemographic fields
    public $nombre_completo = null;

    public $genero = null;

    public $edad = null;

    public $actividad_trabajo = null;

    public $actividad_trabajo_otra = null;

    public $tiempo_trabajando = null;

    public $telefono = null;

    public $correo = null;

    // Form fields
    public $ansiedad_1 = null;

    public $ansiedad_2 = null;

    public $ansiedad_3 = null;

    public $ansiedad_4 = null;

    public $ansiedad_5 = null;

    public $ansiedad_6 = null;

    public $ansiedad_7 = null;

    public $depresion_1 = null;

    public $depresion_2 = null;

    public $depresion_3 = null;

    public $depresion_4 = null;

    public $depresion_5 = null;

    public $depresion_6 = null;

    public $depresion_7 = null;

    public $depresion_8 = null;

    public $depresion_9 = null;

    public $suicidio_1 = null;

    public $suicidio_2 = null;

    public $suicidio_3 = null;

    public $suicidio_4 = null;

    // Pregunta que se deriva de la 4: solo se formula a quien reporta un
    // intento previo. No puntúa ni cambia la prioridad —Angélica no la mapeó a
    // ningún nivel—; sirve para que la valoración clínica sepa si el intento
    // fue reciente. Se guarda dentro de `respuestas`.
    public $suicidio_4_ultimo_intento = null;

    // Pregunta de agudeza (ASQ). Solo se formula a quien contestó "Sí" en
    // alguna de las cuatro anteriores, y es la única que establece riesgo
    // agudo. Ver comentario en `rules()`.
    public $suicidio_5 = null;

    public bool $success = false;

    // Los máximos son 191 porque `Schema::defaultStringLength(191)` deja las
    // columnas en VARCHAR(191). Con `max:255` (o sin máximo, como estaba
    // `actividad_trabajo_otra`) el texto pasaba la validación y reventaba en el
    // INSERT con "Data too long", perdiendo el tamizaje completo del
    // colaborador ya contestado. Pasó en producción el 13/08/2026.
    /**
     * Niveles de conducta suicida.
     *
     * Antes del 18/08/2026 un "Sí" en la pregunta 4 —"¿Alguna vez has
     * intentado quitarte la vida?"— marcaba por sí solo `Riesgo Agudo`. Esa
     * pregunta mide toda la vida, mientras que las tres anteriores preguntan
     * por las últimas semanas, así que alguien con un intento hace años y sin
     * ningún síntoma actual salía como emergencia: 933 de las 1,771 alertas
     * urgentes acumuladas no tenían indicador reciente. La agudeza ahora la
     * establece únicamente la pregunta 5.
     */
    public const SUICIDIO_NEGATIVO = 'Negativo';

    public const SUICIDIO_POSITIVO = 'Positivo: requiere valoración posterior';

    public const SUICIDIO_AGUDO = 'Riesgo Agudo';

    /**
     * Conducta que sigue a cada resultado del ASQ, tal como la redactó Angélica
     * en "PARA LA REVISIÓN". El nivel dice qué se encontró; esto, qué hacer.
     * Se muestra junto al resultado en el detalle del tamizaje.
     */
    public const ACCIONES_SUICIDIO = [
        self::SUICIDIO_NEGATIVO => 'Prevención / Promoción / Psicoeducación',
        self::SUICIDIO_POSITIVO => 'Valoración psicológica adicional para confirmar o descartar riesgo y agudeza',
        self::SUICIDIO_AGUDO => 'Valoración / Atención especializada prioritaria',
    ];

    /** Opciones de "¿Cuándo fue el último intento?". */
    public const ULTIMO_INTENTO = [
        'Menos de 12 meses',
        'Más de 12 meses',
    ];

    /**
     * `suicidio_5` solo es obligatoria si hubo al menos un "Sí" en las cuatro
     * anteriores: es la exploración de agudeza del instrumento, no una
     * pregunta que se le haga a todo el mundo. Por eso las reglas son un
     * método y no una propiedad — dependen del estado del formulario.
     */
    protected function rules(): array
    {
        return array_merge($this->reglasBase, [
            'suicidio_5' => $this->requiereAgudeza()
                ? 'required|in:0,1'
                : 'nullable|in:0,1',
            'suicidio_4_ultimo_intento' => $this->reportaIntentoPrevio()
                ? 'required|in:'.implode(',', self::ULTIMO_INTENTO)
                : 'nullable|in:'.implode(',', self::ULTIMO_INTENTO),
        ]);
    }

    /** ¿Contestó "Sí" a alguna de las cuatro preguntas de conducta suicida? */
    public function requiereAgudeza(): bool
    {
        return (int) $this->suicidio_1 === 1
            || (int) $this->suicidio_2 === 1
            || (int) $this->suicidio_3 === 1
            || (int) $this->suicidio_4 === 1;
    }

    /** ¿Reportó un intento previo? Es lo que despliega la pregunta derivada. */
    public function reportaIntentoPrevio(): bool
    {
        return (int) $this->suicidio_4 === 1;
    }

    protected $reglasBase = [
        'consentimiento_otorgado' => 'required|in:si',
        'nombre_completo' => 'required|string|max:191',
        'genero' => 'required|string',
        'edad' => 'required|string',
        'actividad_trabajo' => 'required|string',
        'actividad_trabajo_otra' => 'required_if:actividad_trabajo,Otra|nullable|string|max:191',
        'tiempo_trabajando' => 'required|string',
        'telefono' => 'required|string|max:20',
        'correo' => 'nullable|email|max:191',

        'ansiedad_1' => 'required|in:0,1,2,3',
        'ansiedad_2' => 'required|in:0,1,2,3',
        'ansiedad_3' => 'required|in:0,1,2,3',
        'ansiedad_4' => 'required|in:0,1,2,3',
        'ansiedad_5' => 'required|in:0,1,2,3',
        'ansiedad_6' => 'required|in:0,1,2,3',
        'ansiedad_7' => 'required|in:0,1,2,3',
        'depresion_1' => 'required|in:0,1,2,3',
        'depresion_2' => 'required|in:0,1,2,3',
        'depresion_3' => 'required|in:0,1,2,3',
        'depresion_4' => 'required|in:0,1,2,3',
        'depresion_5' => 'required|in:0,1,2,3',
        'depresion_6' => 'required|in:0,1,2,3',
        'depresion_7' => 'required|in:0,1,2,3',
        'depresion_8' => 'required|in:0,1,2,3',
        'depresion_9' => 'required|in:0,1,2,3',
        'suicidio_1' => 'required|in:0,1',
        'suicidio_2' => 'required|in:0,1',
        'suicidio_3' => 'required|in:0,1',
        'suicidio_4' => 'required|in:0,1',
    ];

    protected $messages = [
        'required' => 'Esta pregunta es obligatoria.',
        'required_if' => 'Por favor, especifica tu actividad.',
        'in' => 'Por favor, selecciona una opción válida.',
        'consentimiento_otorgado.in' => 'Debes otorgar tu consentimiento para participar.',
        'consentimiento_otorgado.required' => 'Esta pregunta es obligatoria.',
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->empresa = Empresa::where('token_tamizaje', $token)->first();

        if (! $this->empresa) {
            abort(404);
        }
    }

    public function updatedConsentimientoOtorgado($value)
    {
        if ($value === 'no') {
            \Log::info("Colaborador declinó participar en el tamizaje de la empresa ID: {$this->empresa->id} ({$this->empresa->nombre_empresa})");
            $this->declaracion_1 = false;
            $this->declaracion_2 = false;
            $this->declaracion_3 = false;
            $this->declaracion_4 = false;
            $this->declaracion_5 = false;
        }
    }

    public function enviarNoParticipacion()
    {
        // Solo aplica cuando la persona declinó explícitamente participar.
        if ($this->consentimiento_otorgado !== 'no') {
            return;
        }

        // Se registra la no-participación para que cuente dentro del total de
        // trabajadores (porcentaje de avance del tablero), sin datos personales
        // ni evaluación de riesgo. 'No participó' queda fuera del gráfico de riesgos.
        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => false,
            'riesgo_ansiedad' => 0,
            'riesgo_depresion' => 0,
            'riesgo_conducta_suicida' => 0,
            'nivel_riesgo_general' => PrioridadAtencion::NO_PARTICIPO,
        ]);

        $this->success = true;
    }

    public function irADemograficos()
    {
        $this->validate([
            'consentimiento_otorgado' => 'required|in:si',
            'declaracion_1' => 'accepted',
            'declaracion_2' => 'accepted',
            'declaracion_3' => 'accepted',
            'declaracion_4' => 'accepted',
            'declaracion_5' => 'accepted',
        ], [
            'accepted' => 'Debes confirmar y aceptar este punto para continuar.',
        ]);

        $this->step = 'demograficos';
    }

    public function irACuestionario()
    {
        // Mismos máximos que $rules: el ancho real de las columnas es 191.
        $this->validate([
            'nombre_completo' => 'required|string|max:191',
            'genero' => 'required|string',
            'edad' => 'required|string',
            'actividad_trabajo' => 'required|string',
            'actividad_trabajo_otra' => 'required_if:actividad_trabajo,Otra|nullable|string|max:191',
            'tiempo_trabajando' => 'required|string',
            'telefono' => 'required|string|max:20',
            'correo' => 'nullable|email|max:191',
        ]);

        $this->step = 'cuestionario';
    }

    public function submit()
    {
        $this->validate();

        $scoreAnsiedad = (int) $this->ansiedad_1 + (int) $this->ansiedad_2 + (int) $this->ansiedad_3 + (int) $this->ansiedad_4 + (int) $this->ansiedad_5 + (int) $this->ansiedad_6 + (int) $this->ansiedad_7;

        // GAD-7 Heuristics
        if ($scoreAnsiedad >= 15) {
            $nivelAnsiedad = 'Grave';
        } elseif ($scoreAnsiedad >= 10) {
            $nivelAnsiedad = 'Moderada';
        } elseif ($scoreAnsiedad >= 5) {
            $nivelAnsiedad = 'Leve';
        } else {
            $nivelAnsiedad = 'Mínima o sin ansiedad';
        }
        $scoreDepresion = (int) $this->depresion_1 + (int) $this->depresion_2 + (int) $this->depresion_3 + (int) $this->depresion_4 + (int) $this->depresion_5 + (int) $this->depresion_6 + (int) $this->depresion_7 + (int) $this->depresion_8 + (int) $this->depresion_9;

        // PHQ-9 Heuristics
        if ($scoreDepresion >= 20) {
            $nivelDepresion = 'Grave';
        } elseif ($scoreDepresion >= 15) {
            $nivelDepresion = 'Moderadamente grave';
        } elseif ($scoreDepresion >= 10) {
            $nivelDepresion = 'Moderada';
        } elseif ($scoreDepresion >= 5) {
            $nivelDepresion = 'Leve';
        } else {
            $nivelDepresion = 'Mínima o ausente';
        }
        $s1 = (int) $this->suicidio_1;
        $s2 = (int) $this->suicidio_2;
        $s3 = (int) $this->suicidio_3;
        $s4 = (int) $this->suicidio_4;

        // El puntaje sigue siendo el de los cuatro ítems del ASQ: la pregunta
        // de agudeza no puntúa, califica. Así los registros nuevos siguen
        // siendo comparables con los ya aplicados.
        $scoreSuicidio = $s1 + $s2 + $s3 + $s4;
        $s5 = $this->suicidio_5 === null || $this->suicidio_5 === '' ? null : (int) $this->suicidio_5;

        if ($scoreSuicidio > 0 && $s5 === 1) {
            $nivelSuicidio = self::SUICIDIO_AGUDO;
        } elseif ($scoreSuicidio > 0) {
            $nivelSuicidio = self::SUICIDIO_POSITIVO;
        } else {
            $nivelSuicidio = self::SUICIDIO_NEGATIVO;
        }

        // La escala la define PrioridadAtencion, que también usa el comando de
        // reclasificación: si la tabla de Angélica vuelve a cambiar, cambia en
        // un solo lugar y los históricos se recalculan igual que los nuevos.
        $nivelRiesgo = PrioridadAtencion::calcular($nivelAnsiedad, $nivelDepresion, $scoreSuicidio, $s5);

        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'consentimiento_otorgado' => $this->consentimiento_otorgado === 'si',
            'nombre_completo' => $this->nombre_completo,
            'genero' => $this->genero,
            'edad' => $this->edad,
            'actividad_trabajo' => $this->actividad_trabajo,
            'actividad_trabajo_otra' => $this->actividad_trabajo === 'Otra' ? $this->actividad_trabajo_otra : null,
            'tiempo_trabajando' => $this->tiempo_trabajando,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'riesgo_ansiedad' => $scoreAnsiedad,
            'nivel_ansiedad' => $nivelAnsiedad,
            'riesgo_depresion' => $scoreDepresion,
            'nivel_depresion' => $nivelDepresion,
            'riesgo_conducta_suicida' => $scoreSuicidio,
            'nivel_suicidio' => $nivelSuicidio,
            'nivel_riesgo_general' => $nivelRiesgo,
            'respuestas' => $this->respuestasCapturadas($s5),
        ]);

        $this->success = true;
    }

    /**
     * Respuesta a respuesta, tal como la contestó la persona.
     *
     * Hasta ahora solo se guardaba la ponderación, así que revisar un caso
     * concreto obligaba a pedirle capturas de pantalla al colaborador.
     */
    private function respuestasCapturadas(?int $s5): array
    {
        $recoger = function (string $prefijo, int $hasta): array {
            $items = [];
            for ($i = 1; $i <= $hasta; $i++) {
                $items[$i] = (int) $this->{$prefijo.'_'.$i};
            }

            return $items;
        };

        return [
            'ansiedad' => $recoger('ansiedad', 7),
            'depresion' => $recoger('depresion', 9),
            'conducta_suicida' => $recoger('suicidio', 4) + [
                5 => $s5,
                'ultimo_intento' => $this->reportaIntentoPrevio() ? $this->suicidio_4_ultimo_intento : null,
            ],
        ];
    }

    /**
     * Si la persona se retracta, las preguntas que se desplegaron dejan de
     * aplicar y su respuesta previa no debe quedar guardada.
     */
    public function updated($property): void
    {
        if (! str_starts_with($property, 'suicidio_')) {
            return;
        }

        if (! $this->requiereAgudeza()) {
            $this->suicidio_5 = null;
            $this->resetValidation('suicidio_5');
        }

        if (! $this->reportaIntentoPrevio()) {
            $this->suicidio_4_ultimo_intento = null;
            $this->resetValidation('suicidio_4_ultimo_intento');
        }
    }

    public function render()
    {
        return view('livewire.responder-tamizaje')
            ->layout('components.layouts.app');
    }
}
