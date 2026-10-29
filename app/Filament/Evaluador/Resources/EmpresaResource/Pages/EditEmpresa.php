<?php

namespace App\Filament\Evaluador\Resources\EmpresaResource\Pages;

use App\Filament\Evaluador\Resources\EmpresaResource;
use App\Filament\Resources\Empresas\Schemas\DictamenForm;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpresa extends EditRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    /**
     * Se propone la fecha de hoy cuando la empresa todavía no se ha
     * dictaminado, igual que en la modal del dictamen.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['fecha_dictamen'] = $data['fecha_dictamen'] ?? now();

        return $data;
    }

    /**
     * El dictamen se guarda con las mismas reglas que en la modal: una
     * empresa no aprobada no se queda con nivel de madurez.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return array_merge($data, DictamenForm::datos($data));
    }
}
