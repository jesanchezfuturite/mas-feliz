<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use App\Filament\Resources\Empresas\Schemas\DictamenForm;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;

class ViewEmpresa extends ViewRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('emitir_dictamen')
                ->label('Emitir Dictamen')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->modalHeading('Dictamen de Gobierno')
                ->modalDescription(fn () => $this->record->nombre_empresa)
                ->modalFooterActionsAlignment(Alignment::Right)
                ->modalSubmitActionLabel('Guardar dictamen')
                ->modalCancelActionLabel('Cerrar')
                // Se abre con lo que ya tiene la empresa: el dictamen se
                // corrige tantas veces como haga falta, no se emite una vez.
                ->fillForm(fn () => DictamenForm::valores($this->record))
                ->form(DictamenForm::getSchema())
                ->action(function (array $data) {
                    $this->record->update(DictamenForm::datos($data));

                    $this->refreshFormData([
                        'estatus_distintivo',
                        'nivel_madurez_asignado',
                        'retroalimentacion_gobierno',
                        'fecha_dictamen',
                    ]);

                    Notification::make()
                        ->title('Dictamen guardado')
                        ->body('La empresa ya aparece con el dictamen en su tablero.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
