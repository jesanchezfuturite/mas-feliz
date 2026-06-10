<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmpresa extends CreateRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Guardar y crear otro');
    }
}
