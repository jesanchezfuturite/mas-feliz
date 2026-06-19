<?php

namespace App\Filament\Resources\Evaluadors\Pages;

use App\Filament\Resources\Evaluadors\EvaluadorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvaluador extends EditRecord
{
    protected static string $resource = EvaluadorResource::class;

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
