<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\Widget;

class CertificationTimelineWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.certification-timeline-widget';

    protected int | string | array $columnSpan = 'full';

    // Es lo primero que la empresa busca al entrar a su escritorio: se dibuja
    // junto con la página, no en diferido.
    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $empresa = auth()->user();
        $pasoActual = $empresa->paso_certificacion ?? 1;

        // Las etiquetas vienen del modelo para que la línea de tiempo que ve la
        // empresa y el desplegable que usan acompañante y administración no se
        // puedan desincronizar.
        $iconos = [
            'heroicon-o-clipboard-document-list',
            'heroicon-o-chat-bubble-left-right',
            'heroicon-o-arrow-path',
            'heroicon-o-book-open',
            'heroicon-o-document-text',
            'heroicon-o-shield-check',
        ];

        $steps = [];

        foreach (array_values(\App\Models\Empresa::PASOS_CERTIFICACION) as $indice => $etiqueta) {
            $steps[] = [
                'label' => $etiqueta,
                'icon' => $iconos[$indice] ?? 'heroicon-o-check-circle',
            ];
        }

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
