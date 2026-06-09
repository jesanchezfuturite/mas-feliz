<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Pages;

use App\Filament\Empresa\Resources\CasoSeguimientos\CasoSeguimientoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCasoSeguimientos extends ListRecords
{
    protected static string $resource = CasoSeguimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
