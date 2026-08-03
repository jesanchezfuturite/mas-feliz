<?php

namespace App\Filament\Resources\Gestores\Pages;

use App\Filament\Resources\Gestores\GestorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGestor extends CreateRecord
{
    protected static string $resource = GestorResource::class;

    protected static bool $canCreateAnother = false;

    protected ?string $plainPassword = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = \Illuminate\Support\Str::password(10);

        $data['password'] = \Illuminate\Support\Facades\Hash::make($this->plainPassword);
        $data['estatus'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        \Illuminate\Support\Facades\Mail::to($this->record->email)->send(
            new \App\Mail\GestorBienvenidaMail($this->record, $this->plainPassword)
        );
    }
}
