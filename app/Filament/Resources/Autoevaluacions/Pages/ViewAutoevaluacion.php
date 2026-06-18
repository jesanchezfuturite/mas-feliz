<?php

namespace App\Filament\Resources\Autoevaluacions\Pages;

use App\Filament\Resources\AutoevaluacionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewAutoevaluacion extends ViewRecord
{
    protected static string $resource = AutoevaluacionResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $sum = 0;
        $respuestas = $this->record->respuestas ?? [];
        foreach ($respuestas as $criterio => $elementos) {
            if (is_array($elementos)) {
                foreach ($elementos as $elemento => $data) {
                    if (isset($data['score']) && is_numeric($data['score'])) {
                        $sum += (int) $data['score'];
                    }
                }
            }
        }

        return 'Ver Autoevaluación (Puntaje Total: ' . $sum . ')';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('autorizar')
                ->label('Autorizar Autoevaluación')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Autorizar Autoevaluación')
                ->modalDescription('¿Estás seguro de que deseas autorizar esta autoevaluación?')
                ->modalSubmitActionLabel('Sí, autorizar')
                ->hidden(fn ($record) => $record->estatus !== 'En revisión')
                ->action(function ($record) {
                    $record->update(['estatus' => 'Autorizada']);
                    \Filament\Notifications\Notification::make()
                        ->title('Autoevaluación autorizada correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            \App\Filament\Resources\Empresas\EmpresaResource::getUrl('index') => 'Empresas',
            '#' => $this->record->empresa->nombre_empresa ?? 'Empresa',
            '' => 'Autoevaluación',
        ];
    }
}
