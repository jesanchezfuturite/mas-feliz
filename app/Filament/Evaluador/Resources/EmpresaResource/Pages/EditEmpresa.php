<?php

namespace App\Filament\Evaluador\Resources\EmpresaResource\Pages;

use App\Filament\Evaluador\Resources\EmpresaResource;
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
}
