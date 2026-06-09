<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * Override to change the label of the email field to Spanish.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Correo Electrónico')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    /**
     * Map the form's "email" state key to the model's "correo" database column.
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'correo' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
