<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Pages;

use App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource;
use App\Models\Autoevaluacion;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutoevaluacions extends ListRecords
{
    protected static string $resource = AutoevaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->hidden(function () {
                    return Autoevaluacion::where('empresa_id', auth()->id())
                        ->whereIn('estatus', ['Borrador', 'En revisión'])
                        ->exists();
                }),
        ];
    }
}
