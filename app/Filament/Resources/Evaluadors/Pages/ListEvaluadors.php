<?php

namespace App\Filament\Resources\Evaluadors\Pages;

use App\Filament\Resources\Evaluadors\EvaluadorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluadors extends ListRecords
{
    protected static string $resource = EvaluadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
