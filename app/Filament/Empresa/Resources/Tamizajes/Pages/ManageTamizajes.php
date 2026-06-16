<?php

namespace App\Filament\Empresa\Resources\Tamizajes\Pages;

use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTamizajes extends ManageRecords
{
    protected static string $resource = TamizajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
