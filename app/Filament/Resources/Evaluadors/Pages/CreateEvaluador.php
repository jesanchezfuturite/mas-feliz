<?php

namespace App\Filament\Resources\Evaluadors\Pages;

use App\Filament\Resources\Evaluadors\EvaluadorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvaluador extends CreateRecord
{
    protected static string $resource = EvaluadorResource::class;

    protected static bool $canCreateAnother = false;

    protected ?string $plainPassword = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generar una contraseña segura aleatoria de 10 caracteres
        $this->plainPassword = \Illuminate\Support\Str::password(10);
        
        $data['password'] = \Illuminate\Support\Facades\Hash::make($this->plainPassword);
        $data['estatus'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Enviar el correo de bienvenida
        \Illuminate\Support\Facades\Mail::to($this->record->email)->send(
            new \App\Mail\EvaluadorBienvenidaMail($this->record, $this->plainPassword)
        );
    }
}
