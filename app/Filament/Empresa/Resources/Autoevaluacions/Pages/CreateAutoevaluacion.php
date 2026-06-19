<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Pages;

use App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutoevaluacion extends CreateRecord
{
    protected static string $resource = AutoevaluacionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['empresa_id'] = auth()->id();
        $data['fecha_evaluacion'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
        ];
    }
}
