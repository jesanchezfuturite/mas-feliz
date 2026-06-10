<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Pages;

use App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAutoevaluacion extends EditRecord
{
    protected static string $resource = AutoevaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
