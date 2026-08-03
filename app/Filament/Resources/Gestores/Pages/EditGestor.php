<?php

namespace App\Filament\Resources\Gestores\Pages;

use App\Filament\Resources\Gestores\GestorResource;
use Filament\Resources\Pages\EditRecord;

class EditGestor extends EditRecord
{
    protected static string $resource = GestorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
