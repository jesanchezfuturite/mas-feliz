<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Pages;

use App\Filament\Empresa\Resources\CasoSeguimientos\CasoSeguimientoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCasoSeguimiento extends EditRecord
{
    protected static string $resource = CasoSeguimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
