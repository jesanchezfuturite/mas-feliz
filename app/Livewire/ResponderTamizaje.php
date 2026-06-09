<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\Tamizaje;

class ResponderTamizaje extends Component
{
    public $token;
    public $empresa;

    // Form fields
    public $ansiedad_1 = null;
    public $ansiedad_2 = null;
    public $ansiedad_3 = null;

    public $depresion_1 = null;
    public $depresion_2 = null;
    public $depresion_3 = null;

    public $suicidio_1 = null;
    public $suicidio_2 = null;
    public $suicidio_3 = null;

    public bool $success = false;

    protected $rules = [
        'ansiedad_1' => 'required|in:0,1,2',
        'ansiedad_2' => 'required|in:0,1,2',
        'ansiedad_3' => 'required|in:0,1,2',
        'depresion_1' => 'required|in:0,1,2',
        'depresion_2' => 'required|in:0,1,2',
        'depresion_3' => 'required|in:0,1,2',
        'suicidio_1' => 'required|in:0,1,2',
        'suicidio_2' => 'required|in:0,1,2',
        'suicidio_3' => 'required|in:0,1,2',
    ];

    protected $messages = [
        'required' => 'Esta pregunta es obligatoria.',
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

        $scoreAnsiedad = (int)$this->ansiedad_1 + (int)$this->ansiedad_2 + (int)$this->ansiedad_3;
        $scoreDepresion = (int)$this->depresion_1 + (int)$this->depresion_2 + (int)$this->depresion_3;
        $scoreSuicidio = (int)$this->suicidio_1 + (int)$this->suicidio_2 + (int)$this->suicidio_3;

        $total = $scoreAnsiedad + $scoreDepresion + $scoreSuicidio;

        // Custom clinical heuristic logic
        if ($scoreSuicidio >= 2 || $total >= 12) {
            $nivelRiesgo = 'Urgente';
        } elseif ($total >= 6) {
            $nivelRiesgo = 'Moderado';
        } else {
            $nivelRiesgo = 'Leve';
        }

        Tamizaje::create([
            'empresa_id' => $this->empresa->id,
            'riesgo_ansiedad' => $scoreAnsiedad,
            'riesgo_depresion' => $scoreDepresion,
            'riesgo_conducta_suicida' => $scoreSuicidio,
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
