<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class EmpresaForm
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('folio')
                ->label('Folio')
                ->readOnly()
                ->hiddenOn('create')
                ->placeholder('Generado automáticamente al crear'),
            TextInput::make('nombre_empresa')
                ->label('Nombre de la Empresa')
                ->required()
                ->maxLength(255),
            TextInput::make('rfc')
                ->label('RFC')
                ->maxLength(15),
            Select::make('ambito')
                ->label('Ámbito')
                ->options([
                    'Público' => 'Público',
                    'Privado' => 'Privado',
                    'Productivo/Industrial' => 'Productivo/Industrial',
                    'Social/Comunitario' => 'Social/Comunitario',
                    'Otro' => 'Otro',
                ])
                ->required(),
            TextInput::make('domicilio')
                ->label('Domicilio')
                ->required()
                ->maxLength(255),
            Select::make('municipio')
                ->label('Municipio')
                ->options(
                    array_combine(
                        ['Abasolo', 'Acuña', 'Allende', 'Arteaga', 'Candela', 'Castaños', 'Cuatro Ciénegas', 'Escobedo', 'Francisco I. Madero', 'Frontera', 'General Cepeda', 'Guerrero', 'Hidalgo', 'Jiménez', 'Juárez', 'Lamadrid', 'Matamoros', 'Monclova', 'Morelos', 'Múzquiz', 'Nadadores', 'Nava', 'Ocampo', 'Parras', 'Piedras Negras', 'Progreso', 'Ramos Arizpe', 'Sabinas', 'Sacramento', 'Saltillo', 'San Buenaventura', 'San Juan de Sabinas', 'San Pedro', 'Sierra Mojada', 'Torreón', 'Viesca', 'Villa Unión', 'Zaragoza'],
                        ['Abasolo', 'Acuña', 'Allende', 'Arteaga', 'Candela', 'Castaños', 'Cuatro Ciénegas', 'Escobedo', 'Francisco I. Madero', 'Frontera', 'General Cepeda', 'Guerrero', 'Hidalgo', 'Jiménez', 'Juárez', 'Lamadrid', 'Matamoros', 'Monclova', 'Morelos', 'Múzquiz', 'Nadadores', 'Nava', 'Ocampo', 'Parras', 'Piedras Negras', 'Progreso', 'Ramos Arizpe', 'Sabinas', 'Sacramento', 'Saltillo', 'San Buenaventura', 'San Juan de Sabinas', 'San Pedro', 'Sierra Mojada', 'Torreón', 'Viesca', 'Villa Unión', 'Zaragoza']
                    )
                )
                ->required(),
            TextInput::make('dias_horario_servicio')
                ->label('Horarios de la empresa')
                ->required()
                ->maxLength(255),
            TextInput::make('nombre_director')
                ->label('Nombre del Director')
                ->required()
                ->maxLength(255),
            TextInput::make('nombre_responsable')
                ->label('Persona enlace')
                ->required()
                ->maxLength(255),
            TextInput::make('cargo_enlace')
                ->label('Cargo de la Persona Enlace')
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
                ->label('Número de colaboradores')
                ->numeric()
                ->required(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getSchema());
    }
}
