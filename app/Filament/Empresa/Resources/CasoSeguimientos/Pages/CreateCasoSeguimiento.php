<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Pages;

use App\Filament\Empresa\Resources\CasoSeguimientos\CasoSeguimientoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCasoSeguimiento extends CreateRecord
{
    protected static string $resource = CasoSeguimientoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['empresa_id'] = auth()->id();

        return $data;
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Guardar y crear otro');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
