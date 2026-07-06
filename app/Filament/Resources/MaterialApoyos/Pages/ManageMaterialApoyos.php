<?php

namespace App\Filament\Resources\MaterialApoyos\Pages;

use App\Filament\Resources\MaterialApoyos\MaterialApoyoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterialApoyos extends ManageRecords
{
    protected static string $resource = MaterialApoyoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
