<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class EmpresaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('folio')
                    ->label('Folio')
                    ->readOnly()
                    ->placeholder('Generado automáticamente al crear'),
                TextInput::make('nombre_empresa')
                    ->label('Nombre de la Empresa')
                    ->required()
                    ->maxLength(255),
                TextInput::make('municipio')
                    ->label('Municipio')
                    ->required()
                    ->maxLength(255),
                TextInput::make('dias_horario_servicio')
                    ->label('Días y Horarios de Servicio')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nombre_director')
                    ->label('Nombre del Director')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nombre_responsable')
                    ->label('Nombre del Responsable')
                    ->required()
                    ->maxLength(255),
                TextInput::make('correo')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->unique(table: 'empresas', column: 'correo', ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                TextInput::make('rubro')
                    ->label('Rubro')
                    ->required()
                    ->maxLength(255),
                TextInput::make('numero_trabajadores')
                    ->label('Número de Trabajadores')
                    ->numeric()
                    ->required(),
            ]);
    }
}
