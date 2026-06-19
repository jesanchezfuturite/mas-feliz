<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Pages;

use App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource;
use Filament\Resources\Pages\EditRecord;

class EditAutoevaluacion extends EditRecord
{
    protected static string $resource = AutoevaluacionResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        $respuestas = $this->data['respuestas'] ?? ($this->record->respuestas ?? []);
        $total = 0;
        
        if (is_array($respuestas)) {
            foreach ($respuestas as $criterio) {
                if (is_array($criterio)) {
                    foreach ($criterio as $elemento) {
                        if (is_array($elemento) && isset($elemento['score']) && is_numeric($elemento['score'])) {
                            $total += (float) $elemento['score'];
                        }
                    }
                }
            }
        }

        return "Editar Autoevaluación (Puntaje Total: {$total})";
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Empresa\Resources\Autoevaluacions\Widgets\AutoevaluacionStatsWidget::class,
        ];
    }

    protected function getFormActions(): array
    {
        if (in_array($this->record->estatus, ['En revisión', 'Validado'])) {
            return [];
        }

        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
