<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;

class CasoSeguimientoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('identificador_empleado')
                    ->label('Identificador del Empleado')
                    ->placeholder('Nombre, Iniciales o Número de Nómina')
                    ->required()
                    ->maxLength(255),

                Select::make('nivel_riesgo_detectado')
                    ->label('Nivel de Riesgo Detectado')
                    ->options([
                        'Leve' => 'Leve',
                        'Moderado' => 'Moderado',
                        'Urgente' => 'Urgente',
                    ])
                    ->required(),

                Select::make('estatus_atencion')
                    ->label('Estatus de la Atención')
                    ->options([
                        'En seguimiento' => 'En seguimiento',
                        'Abandonó' => 'Abandonó',
                        'Canalizado' => 'Canalizado',
                        'Cerrado satisfactorio' => 'Cerrado satisfactorio',
                    ])
                    ->required()
                    ->live(),

                TextInput::make('institucion_canalizacion')
                    ->label('Institución de Canalización')
                    ->placeholder('CESAME, IMSS, ISSSTE, Cruz Roja, etc.')
                    ->visible(fn (Get $get): bool => $get('estatus_atencion') === 'Canalizado')
                    ->required(fn (Get $get): bool => $get('estatus_atencion') === 'Canalizado')
                    ->maxLength(255),

                Textarea::make('notas_clinicas')
                    ->label('Notas Clínicas')
                    ->rows(4)
                    ->maxLength(65535),
            ]);
    }
}
