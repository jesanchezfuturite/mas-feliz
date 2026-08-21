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
                    ->label('Nombre Completo')
                    ->placeholder('Escribe tu nombre completo')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists()),

                // El orden de los campos sigue la jerarquía del documento de Salud
                // "ATENCIÓN EMPRESAS +FELIZ": nombre, rango de edad, sexo, funciones,
                // tiempo trabajando, correo y celular.
                Select::make('edad')
                    ->label('Rango de edad')
                    ->options([
                        'Menor de 18 años' => 'Menor de 18 años',
                        '18 a 24 años' => '18 a 24 años',
                        '25 a 34 años' => '25 a 34 años',
                        '35 a 44 años' => '35 a 44 años',
                        '45 a 54 años' => '45 a 54 años',
                        '55 años o más' => '55 años o más',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->edad : null;
                    })
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())
                    ->required(fn ($record) => !($record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())),

                Select::make('genero')
                    ->label('Sexo')
                    ->options([
                        'Hombre' => 'Hombre',
                        'Mujer' => 'Mujer',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->genero : null;
                    })
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())
                    ->required(fn ($record) => !($record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())),

                Select::make('actividad_trabajo')
                    ->label('Funciones')
                    ->options([
                        'Operativas' => 'Operativas',
                        'Administrativas' => 'Administrativas',
                        'Técnicas' => 'Técnicas',
                        'Profesionales especializadas' => 'Profesionales especializadas',
                        'Supervisión o coordinación' => 'Supervisión o coordinación',
                        'Dirección o gerencia' => 'Dirección o gerencia',
                        'Atención directa al público, usuarios o clientes' => 'Atención directa al público, usuarios o clientes',
                        'Otra' => 'Otra',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->actividad_trabajo : null;
                    })
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())
                    ->required(fn ($record) => !($record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists()))
                    ->live(),

                TextInput::make('actividad_trabajo_otra')
                    ->label('Especifica la función')
                    ->placeholder('Escribe tu actividad')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->actividad_trabajo_otra : null;
                    })
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('actividad_trabajo') === 'Otra')
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get, $record): bool => $get('actividad_trabajo') === 'Otra' && !($record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists()))
                    ->maxLength(255),

                Select::make('tiempo_trabajando')
                    ->label('Tiempo trabajando en la empresa')
                    ->options([
                        'Menos de 6 meses' => 'Menos de 6 meses',
                        'De 6 meses a 1 año' => 'De 6 meses a 1 año',
                        'Más de 1 año a 3 años' => 'Más de 1 año a 3 años',
                        'Más de 3 años a 5 años' => 'Más de 3 años a 5 años',
                        'Más de 5 años' => 'Más de 5 años',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->tiempo_trabajando : null;
                    })
                    ->disabled(fn ($record) => $record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())
                    ->required(fn ($record) => !($record && \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->exists())),

                // Datos de contacto: el tamizaje es anónimo en su origen, pero para
                // canalizar a la persona Salud necesita cómo localizarla. Se
                // prellenan desde el tamizaje y quedan editables por si cambiaron.
                TextInput::make('correo')
                    ->label('Correo')
                    ->email()
                    ->maxLength(255)
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->correo : null;
                    }),

                TextInput::make('celular')
                    ->label('Celular')
                    ->tel()
                    ->maxLength(20)
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return null;
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                        return $tamizaje ? $tamizaje->telefono : null;
                    }),

                \Filament\Forms\Components\Radio::make('consentimiento')
                    ->label('Consentimiento')
                    ->boolean('Sí', 'No')
                    ->inline()
                    ->inlineLabel(false),

                \Filament\Forms\Components\CheckboxList::make('servicios')
                    ->label('Servicio')
                    ->options([
                        'Medicina' => 'Medicina',
                        'Psicología' => 'Psicología',
                        'Psiquiatría' => 'Psiquiatría',
                        'Otro' => 'Otro',
                    ])
                    ->columns(4)
                    ->live()
                    ->columnSpanFull(),

                TextInput::make('servicio_otro')
                    ->label('Especifica el servicio')
                    ->placeholder('¿Qué otro servicio requiere?')
                    ->maxLength(255)
                    ->visible(fn (Get $get): bool => in_array('Otro', $get('servicios') ?? []))
                    ->required(fn (Get $get): bool => in_array('Otro', $get('servicios') ?? [])),

                Select::make('nivel_riesgo_detectado')
                    ->label(\App\Support\PrioridadAtencion::ETIQUETA)
                    ->options(\App\Support\PrioridadAtencion::opciones())
                    ->helperText(\App\Support\PrioridadAtencion::NOTA)
                    ->required(),

                Select::make('estatus_atencion')
                    ->label('Estatus de atención')
                    ->options(\App\Models\CasoSeguimiento::ESTATUS_ATENCION)
                    ->required()
                    ->live(),

                TextInput::make('institucion_canalizacion')
                    ->label('Institución de canalización')
                    ->placeholder('CESAME, IMSS, ISSSTE, Cruz Roja, etc.')
                    ->visible(fn (Get $get): bool => $get('estatus_atencion') === 'Canalizado')
                    ->required(fn (Get $get): bool => $get('estatus_atencion') === 'Canalizado')
                    ->maxLength(255),

                \Filament\Forms\Components\Checkbox::make('referencia_secretaria_salud')
                    ->label('Solicitud de referencia complementaria: Secretaría de Salud')
                    ->helperText('Al marcarlo podrás llenar el formato de referencia desde el listado de casos, con la acción "Formato de referencia".')
                    ->columnSpanFull(),

                Textarea::make('notas_clinicas')
                    ->label('Notas clínicas')
                    ->rows(4)
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
