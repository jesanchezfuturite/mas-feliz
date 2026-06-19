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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $respuestas = $data['respuestas'] ?? [];
        $originalRespuestas = $this->record->respuestas ?? [];
        
        for ($i = 1; $i <= 20; $i++) {
            $criterioKey = "criterio_{$i}";
            if (isset($respuestas[$criterioKey]) && is_array($respuestas[$criterioKey])) {
                $hasChanges = false;
                $originalCriterioStatus = $originalRespuestas[$criterioKey]['status'] ?? null;
                
                foreach ($respuestas[$criterioKey] as $key => $elemento) {
                    if (str_starts_with($key, 'elemento_') && is_array($elemento)) {
                        $originalElement = $originalRespuestas[$criterioKey][$key] ?? [];
                        $originalCalif = $originalElement['calificacion_politica'] ?? null;
                        
                        if ($originalCalif === 'Rechazado') {
                            $commentChanged = ($elemento['comentario'] ?? null) !== ($originalElement['comentario'] ?? null);
                            $fileChanged = ($elemento['archivo'] ?? null) !== ($originalElement['archivo'] ?? null);
                            $scoreChanged = ($elemento['score'] ?? null) !== ($originalElement['score'] ?? null);
                            
                            if ($commentChanged || $fileChanged || $scoreChanged) {
                                $respuestas[$criterioKey][$key]['calificacion_politica'] = null;
                                $respuestas[$criterioKey][$key]['evaluador_email'] = null;
                                $hasChanges = true;
                            }
                        }
                    }
                }
                
                if ($hasChanges && $originalCriterioStatus === 'Rechazado') {
                    $respuestas[$criterioKey]['status'] = null;
                    $respuestas[$criterioKey]['feedback'] = null;
                    $respuestas[$criterioKey]['evaluador_email'] = null;
                }
            }
        }
        
        $data['respuestas'] = $respuestas;
        
        return $data;
    }
}
