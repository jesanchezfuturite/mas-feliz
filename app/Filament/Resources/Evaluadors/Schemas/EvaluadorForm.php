<?php

namespace App\Filament\Resources\Evaluadors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EvaluadorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombres')
                    ->required()
                    ->maxLength(255),
                TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?string $operation) => $operation === 'edit')
                    ->maxLength(255),
                TextInput::make('telefono')
                    ->tel()
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
