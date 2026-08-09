<?php

namespace App\Filament\Resources\Gestores\Pages;

use App\Filament\Resources\Gestores\GestorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGestores extends ListRecords
{
    protected static string $resource = GestorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo gestor'),
        ];
    }
}
