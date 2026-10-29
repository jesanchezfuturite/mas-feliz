<?php

namespace App\Filament\Evaluador\Resources\EmpresaResource\Pages;

use App\Filament\Evaluador\Resources\EmpresaResource;
use App\Filament\Resources\Empresas\Schemas\DictamenForm;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmpresa extends ViewRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // El evaluador dictamina desde la ficha sin entrar a editarla,
            // con el mismo formulario que usa Gobierno.
            DictamenForm::accionDeFicha($this),
            Actions\EditAction::make(),
        ];
    }
}
