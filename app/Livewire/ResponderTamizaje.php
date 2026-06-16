<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\Tamizaje;

class ResponderTamizaje extends Component
{
    public $token;
    public $empresa;

    // Sociodemographic fields
    public $nombre_completo = null;
    public $genero = null;
    public $edad = null;
    public $actividad_trabajo = null;
    public $actividad_trabajo_otra = null;
    public $tiempo_trabajando = null;

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

    public bool $success = false;

    protected $rules = [
        'nombre_completo' => 'required|string|max:255',
        'genero' => 'required|string',
        'edad' => 'required|string',
        'actividad_trabajo' => 'required|string',
        'actividad_trabajo_otra' => 'required_if:actividad_trabajo,Otra',
        'tiempo_trabajando' => 'required|string',
        
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
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->empresa = Empresa::where('token_tamizaje', $token)->first();

        if (!$this->empresa) {
            abort(404);
        }
    }

    public function submit()
    {
        $this->validate();

        $scoreAnsiedad = (int)$this->ansiedad_1 + (int)$this->ansiedad_2 + (int)$this->ansiedad_3 + (int)$this->ansiedad_4 + (int)$this->ansiedad_5 + (int)$this->ansiedad_6 + (int)$this->ansiedad_7;
        
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
        $scoreDepresion = (int)$this->depresion_1 + (int)$this->depresion_2 + (int)$this->depresion_3 + (int)$this->depresion_4 + (int)$this->depresion_5 + (int)$this->depresion_6 + (int)$this->depresion_7 + (int)$this->depresion_8 + (int)$this->depresion_9;
        
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
        $s1 = (int)$this->suicidio_1;
        $s2 = (int)$this->suicidio_2;
        $s3 = (int)$this->suicidio_3;
        $s4 = (int)$this->suicidio_4;

        if ($s4 === 1) {
            $nivelSuicidio = 'Riesgo Agudo';
        } elseif ($s1 === 1 || $s2 === 1 || $s3 === 1) {
            $nivelSuicidio = 'Evaluación Adicional';
        } else {
            $nivelSuicidio = 'Negativo';
        }

        $scoreSuicidio = $s1 + $s2 + $s3 + $s4;

        $total = $scoreAnsiedad + $scoreDepresion;

        if ($nivelSuicidio === 'Riesgo Agudo' || $nivelSuicidio === 'Evaluación Adicional') {
            $nivelRiesgo = 'Urgente';
        } elseif ($nivelDepresion === 'Grave' || $nivelDepresion === 'Moderadamente grave' || $nivelAnsiedad === 'Grave') {
            $nivelRiesgo = 'Urgente';
        } elseif ($nivelDepresion === 'Moderada' || $nivelAnsiedad === 'Moderada') {
            $nivelRiesgo = 'Moderado';
        } else {
            $nivelRiesgo = 'Leve';
        }

        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'nombre_completo' => $this->nombre_completo,
            'genero' => $this->genero,
            'edad' => $this->edad,
            'actividad_trabajo' => $this->actividad_trabajo,
            'actividad_trabajo_otra' => $this->actividad_trabajo === 'Otra' ? $this->actividad_trabajo_otra : null,
            'tiempo_trabajando' => $this->tiempo_trabajando,
            'riesgo_ansiedad' => $scoreAnsiedad,
            'nivel_ansiedad' => $nivelAnsiedad,
            'riesgo_depresion' => $scoreDepresion,
            'nivel_depresion' => $nivelDepresion,
            'riesgo_conducta_suicida' => $scoreSuicidio,
            'nivel_suicidio' => $nivelSuicidio,
            'nivel_riesgo_general' => $nivelRiesgo,
        ]);

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.responder-tamizaje')
            ->layout('components.layouts.app');
    }
}
