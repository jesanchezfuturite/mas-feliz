<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\Widget;

class CertificationTimelineWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.certification-timeline-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $empresa = auth()->user();
        $pasoActual = $empresa->paso_certificacion ?? 1;

        $steps = [
            [
                'label' => 'Registro',
                'icon' => 'heroicon-o-clipboard-document-list',
            ],
            [
                'label' => 'Diagnóstico inicial/Autoevaluación',
                'icon' => 'heroicon-o-chat-bubble-left-right',
            ],
            [
                'label' => 'Retroalimentación y Acompañamiento',
                'icon' => 'heroicon-o-arrow-path',
            ],
            [
                'label' => 'Plan de acción/Implementación',
                'icon' => 'heroicon-o-book-open',
            ],
            [
                'label' => 'Evaluación y Dictaminación',
                'icon' => 'heroicon-o-document-text',
            ],
            [
                'label' => 'Reconocimiento acorde al nivel de Madurez',
                'icon' => 'heroicon-o-shield-check',
            ],
        ];

        foreach ($steps as $index => &$step) {
            $pasoNumero = $index + 1;
            if ($pasoNumero < $pasoActual) {
                $step['status'] = 'completed';
            } elseif ($pasoNumero == $pasoActual) {
                $step['status'] = 'active';
            } else {
                $step['status'] = 'pending';
            }
        }

        return [
            'steps' => $steps,
        ];
    }
}
