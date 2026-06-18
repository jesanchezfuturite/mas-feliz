<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

class CasoSeguimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->description(new \Illuminate\Support\HtmlString('
                <div style="display: flex; align-items: center; gap: 1rem; border-radius: 1rem; border: 1px solid #3b82f6; background-color: #eff6ff; padding: 0.75rem 1.25rem; color: #1d4ed8; margin-top: 1rem; margin-bottom: 0.5rem; text-align: left;">
                    <div style="display: flex; height: 2.5rem; width: 2.5rem; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 9999px; background-color: #dbeafe;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height: 1.25rem; width: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 500; line-height: 1.4; text-wrap: balance;">
                        Esta herramienta te permite documentar y llevar una bitácora detallada para dar seguimiento a los empleados que requieren atención o canalización.
                    </div>
                </div>
            '))
            ->columns([
                TextColumn::make('identificador_empleado')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->getStateUsing(function ($record) {
                        if (!empty($record->edad)) {
                            return $record->edad;
                        }
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)
                            ->where('nombre_completo', $record->identificador_empleado)
                            ->first();
                        return $tamizaje ? $tamizaje->edad : 'N/A';
                    })
                    ->alignCenter(),

                TextColumn::make('departamento')
                    ->label('Departamento')
                    ->getStateUsing(function ($record) {
                        if (!empty($record->actividad_trabajo)) {
                            return $record->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record->actividad_trabajo;
                        }
                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)
                            ->where('nombre_completo', $record->identificador_empleado)
                            ->first();
                        return $tamizaje ? ($tamizaje->actividad_trabajo === 'Otra' ? $tamizaje->actividad_trabajo_otra : $tamizaje->actividad_trabajo) : 'N/A';
                    })
                    ->alignCenter(),

                TextColumn::make('nivel_riesgo_detectado')
                    ->label('Nivel de Riesgo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('estatus_atencion')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En seguimiento' => 'info',
                        'Canalizado' => 'warning',
                        'Cerrado satisfactorio' => 'success',
                        'Abandonó' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->getStateUsing(function ($record) {
                        $tamizajeExists = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)
                            ->where('nombre_completo', $record->identificador_empleado)
                            ->exists();
                        return $tamizajeExists ? 'En Línea' : 'Manual';
                    })
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('VerDetalle')
                    ->label('Ver detalle')
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->tooltip('Ver detalle')
                    ->modalHeading('Detalle de Evaluación')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalFooterActionsAlignment('right')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('nivel_ansiedad')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $value = $tamizaje->nivel_ansiedad ?? 'N/A';
                                        $color = match($value) {
                                            'Grave', 'Moderadamente grave', 'Riesgo Agudo', 'Urgente' => '#ef4444',
                                            'Moderada', 'Evaluación Adicional', 'Moderado' => '#f59e0b',
                                            'Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo' => '#22c55e',
                                            default => '#6b7280',
                                        };
                                        return new \Illuminate\Support\HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Ansiedad: {$value}</span>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('nivel_depresion')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $value = $tamizaje->nivel_depresion ?? 'N/A';
                                        $color = match($value) {
                                            'Grave', 'Moderadamente grave', 'Riesgo Agudo', 'Urgente' => '#ef4444',
                                            'Moderada', 'Evaluación Adicional', 'Moderado' => '#f59e0b',
                                            'Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo' => '#22c55e',
                                            default => '#6b7280',
                                        };
                                        return new \Illuminate\Support\HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Depresión: {$value}</span>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('nivel_suicidio')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $value = $tamizaje->nivel_suicidio ?? 'N/A';
                                        $color = match($value) {
                                            'Grave', 'Moderadamente grave', 'Riesgo Agudo', 'Urgente' => '#ef4444',
                                            'Moderada', 'Evaluación Adicional', 'Moderado' => '#f59e0b',
                                            'Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo' => '#22c55e',
                                            default => '#6b7280',
                                        };
                                        return new \Illuminate\Support\HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Riesgo Suicida: {$value}</span>");
                                    }),
                            ]),

                        \Filament\Forms\Components\Placeholder::make('info_title')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Información del Empleado</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" /></svg></span></div>')),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('nombre_completo')
                                    ->label('Nombre Completo')
                                    ->content(function ($record) {
                                        if (!empty($record->identificador_empleado) && $record->identificador_empleado !== 'N/A') return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->identificador_empleado}</div>");
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->nombre_completo ?? 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('genero')
                                    ->label('Género')
                                    ->content(function ($record) {
                                        if (!empty($record->genero)) return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->genero}</div>");
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->genero ?? 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('edad')
                                    ->label('Grupo de Edad')
                                    ->content(function ($record) {
                                        if (!empty($record->edad)) return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->edad}</div>");
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->edad ?? 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('tiempo_trabajando')
                                    ->label('Tiempo trabajando')
                                    ->content(function ($record) {
                                        if (!empty($record->tiempo_trabajando)) return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->tiempo_trabajando}</div>");
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->tiempo_trabajando ?? 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('actividad_trabajo')
                                    ->label('Departamento / Actividad')
                                    ->content(function ($record) {
                                        if (!empty($record->actividad_trabajo)) {
                                            $val = $record->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record->actividad_trabajo;
                                            return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                        }
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje ? ($tamizaje->actividad_trabajo === 'Otra' ? $tamizaje->actividad_trabajo_otra : $tamizaje->actividad_trabajo) : 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                \Filament\Forms\Components\Placeholder::make('fecha_evaluacion')
                                    ->label('Fecha de Evaluación')
                                    ->content(function ($record) {
                                        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = ($tamizaje && $tamizaje->created_at) ? $tamizaje->created_at->format('d/m/Y') : 'N/A';
                                        return new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                            ]),

                        \Filament\Forms\Components\Placeholder::make('seguimiento_title')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Seguimiento</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.158 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" /><path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" /></svg></span></div>')),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('estatus_atencion')
                                    ->label('Estatus de la Atención')
                                    ->content(fn ($record) => new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">" . ($record->estatus_atencion ?? 'N/A') . "</div>")),
                                \Filament\Forms\Components\Placeholder::make('institucion_canalizacion')
                                    ->label('Institución de Canalización')
                                    ->content(fn ($record) => new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">" . ($record->institucion_canalizacion ?? 'N/A') . "</div>")),
                                \Filament\Forms\Components\Placeholder::make('comentarios')
                                    ->label('Comentarios')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => !empty($record->notas_clinicas))
                                    ->content(fn ($record) => new \Illuminate\Support\HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem; white-space: pre-wrap;\">{$record->notas_clinicas}</div>")),
                            ]),
                    ]),
                \Filament\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
