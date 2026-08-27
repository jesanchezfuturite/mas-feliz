<?php

namespace App\Filament\Resources\Empresas\RelationManagers;

use App\Models\CasoSeguimiento;
use App\Models\Tamizaje;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CasosSeguimientoRelationManager extends RelationManager
{
    protected static string $relationship = 'casosSeguimiento';

    protected static ?string $title = 'Bitácora de Casos Clínicos y Seguimiento';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('identificador_empleado')
                    ->label('Identificador del Empleado'),
                TextEntry::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->badge()
                    ->color(fn (string $state): string => ColorNivel::badge($state)),
                TextEntry::make('estatus_atencion')
                    ->label('Estatus de Atención')
                    ->badge()
                    ->color(fn (string $state): string => CasoSeguimiento::COLORES_ESTATUS[$state] ?? 'gray'),
                TextEntry::make('institucion_canalizacion')
                    ->label('Institución de Canalización')
                    ->placeholder('N/A'),
                TextEntry::make('notas_clinicas')
                    ->label('Notas Clínicas')
                    ->placeholder('Sin notas'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Bitácora de Casos Clínicos y Seguimiento')
            ->recordTitleAttribute('identificador_empleado')
            ->columns([
                TextColumn::make('identificador_empleado')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->getStateUsing(function ($record) {
                        if (! empty($record->edad)) {
                            return $record->edad;
                        }
                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)
                            ->where('nombre_completo', $record->identificador_empleado)
                            ->first();

                        return $tamizaje ? $tamizaje->edad : 'N/A';
                    })
                    ->alignCenter(),

                TextColumn::make('departamento')
                    ->label('Departamento')
                    ->getStateUsing(function ($record) {
                        if (! empty($record->actividad_trabajo)) {
                            return $record->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record->actividad_trabajo;
                        }
                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)
                            ->where('nombre_completo', $record->identificador_empleado)
                            ->first();

                        return $tamizaje ? ($tamizaje->actividad_trabajo === 'Otra' ? $tamizaje->actividad_trabajo_otra : $tamizaje->actividad_trabajo) : 'N/A';
                    })
                    ->alignCenter(),

                TextColumn::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->badge()
                    ->color(fn (string $state): string => ColorNivel::badge($state))
                    ->sortable(),

                TextColumn::make('estatus_atencion')
                    ->label('Estatus de Atención')
                    ->badge()
                    ->color(fn (string $state): string => CasoSeguimiento::COLORES_ESTATUS[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->getStateUsing(function ($record) {
                        $tamizajeExists = Tamizaje::where('empresa_id', $record->empresa_id)
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
            ->headerActions([
                Action::make('icon')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->link()
                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                    ->label(''),
            ])
            ->recordActions([
                Action::make('VerDetalle')
                    ->label('Ver detalle')
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->tooltip('Ver detalle')
                    ->modalHeading('Detalle de Evaluación')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalFooterActionsAlignment('right')
                    ->form([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('nivel_ansiedad')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $value = $tamizaje->nivel_ansiedad ?? 'N/A';
                                        $color = ColorNivel::hex($value);

                                        return new HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Síntomas de Ansiedad: {$value}</span>");
                                    }),
                                Placeholder::make('nivel_depresion')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $value = $tamizaje->nivel_depresion ?? 'N/A';
                                        $color = ColorNivel::hex($value);

                                        return new HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Síntomas de Depresión: {$value}</span>");
                                    }),
                                Placeholder::make('nivel_suicidio')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        // El resultado del ASQ va completo, con la
                                        // agudeza y su acción en letra chica
                                        // (Angélica, 27/08/2026). Despliegue: la
                                        // columna sigue en dos valores.
                                        $color = ColorNivel::hex($tamizaje->nivel_suicidio ?? null);
                                        $titulo = $tamizaje ? e(ResultadoAsq::titulo($tamizaje)) : 'N/A';
                                        $accion = $tamizaje ? ResultadoAsq::accion($tamizaje) : null;
                                        $lineaAccion = $accion
                                            ? '<span style="display: block; font-size: 0.72rem; font-weight: 500; margin-top: 2px;">'.e($accion).'</span>'
                                            : '';

                                        return new HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 1rem; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">Indicadores de Conducta suicida: {$titulo}{$lineaAccion}</span>");
                                    }),
                            ]),

                        Placeholder::make('info_title')
                            ->hiddenLabel()
                            ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Información del Empleado</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" /></svg></span></div>')),

                        Grid::make(2)
                            ->schema([
                                Placeholder::make('nombre_completo')
                                    ->label('Nombre Completo')
                                    ->content(function ($record) {
                                        if (! empty($record->identificador_empleado) && $record->identificador_empleado !== 'N/A') {
                                            return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->identificador_empleado}</div>");
                                        }
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->nombre_completo ?? 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                Placeholder::make('genero')
                                    ->label('Sexo')
                                    ->content(function ($record) {
                                        if (! empty($record->genero)) {
                                            return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->genero}</div>");
                                        }
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->genero ?? 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                Placeholder::make('edad')
                                    ->label('Grupo de Edad')
                                    ->content(function ($record) {
                                        if (! empty($record->edad)) {
                                            return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->edad}</div>");
                                        }
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->edad ?? 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                Placeholder::make('tiempo_trabajando')
                                    ->label('Tiempo trabajando')
                                    ->content(function ($record) {
                                        if (! empty($record->tiempo_trabajando)) {
                                            return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->tiempo_trabajando}</div>");
                                        }
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje->tiempo_trabajando ?? 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                Placeholder::make('actividad_trabajo')
                                    ->label('Departamento / Actividad')
                                    ->content(function ($record) {
                                        if (! empty($record->actividad_trabajo)) {
                                            $val = $record->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record->actividad_trabajo;

                                            return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                        }
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = $tamizaje ? ($tamizaje->actividad_trabajo === 'Otra' ? $tamizaje->actividad_trabajo_otra : $tamizaje->actividad_trabajo) : 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                                Placeholder::make('fecha_evaluacion')
                                    ->label('Fecha de Evaluación')
                                    ->content(function ($record) {
                                        $tamizaje = Tamizaje::where('empresa_id', $record->empresa_id)->where('nombre_completo', $record->identificador_empleado)->first();
                                        $val = ($tamizaje && $tamizaje->created_at) ? $tamizaje->created_at->format('d/m/Y') : 'N/A';

                                        return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$val}</div>");
                                    }),
                            ]),

                        Placeholder::make('seguimiento_title')
                            ->hiddenLabel()
                            ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Seguimiento</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.158 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" /><path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" /></svg></span></div>')),

                        Grid::make(2)
                            ->schema([
                                Placeholder::make('estatus_atencion')
                                    ->label('Estatus de la Atención')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.($record->estatus_atencion ?? 'N/A').'</div>')),
                                Placeholder::make('institucion_canalizacion')
                                    ->label('Institución de Canalización')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.($record->institucion_canalizacion ?? 'N/A').'</div>')),
                                Placeholder::make('comentarios')
                                    ->label('Comentarios')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => ! empty($record->notas_clinicas))
                                    ->content(fn ($record) => new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem; white-space: pre-wrap;\">{$record->notas_clinicas}</div>")),
                            ]),
                    ]),
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
