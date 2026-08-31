<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Tables;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\SolicitudReferenciaForm;
use App\Models\CasoSeguimiento;
use App\Models\Tamizaje;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CasoSeguimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->description(new HtmlString('
                <div style="display: flex; align-items: center; gap: 1rem; border-radius: 1rem; border: 1px solid #3b82f6; background-color: #eff6ff; padding: 0.75rem 1.25rem; color: #1d4ed8; margin-top: 1rem; margin-bottom: 0.5rem; text-align: left;">
                    <div style="display: flex; height: 2.5rem; width: 2.5rem; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 9999px; background-color: #dbeafe;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height: 1.25rem; width: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 500; line-height: 1.4; text-wrap: balance;">
                        Captura directamente sobre la tabla: los cambios se guardan al momento. El nombre del colaborador y los títulos permanecen visibles al desplazarte hacia la derecha.
                    </div>
                </div>
            '))
            ->columns(Columnas::definicion())
            ->filters([
                SelectFilter::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->options(PrioridadAtencion::opciones()),
                SelectFilter::make('estatus_atencion')
                    ->label('Estatus de Atención')
                    ->options(CasoSeguimiento::ESTATUS_ATENCION),
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
                                Placeholder::make('servicios')
                                    ->label('Servicio que requiere')
                                    ->content(fn ($record) => new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$record->servicios_texto}</div>")),
                                Placeholder::make('consentimiento')
                                    ->label('Consentimiento')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.match ($record->consentimiento) {
                                        true => 'Sí',
                                        false => 'No',
                                        default => 'Sin registrar',
                                    }.'</div>')),
                                Placeholder::make('comentarios')
                                    ->label('Comentarios')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => ! empty($record->notas_clinicas))
                                    ->content(fn ($record) => new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem; white-space: pre-wrap;\">{$record->notas_clinicas}</div>")),
                            ]),

                        // Bloque que llena Secretaría de Salud y que la empresa consulta aquí.
                        Placeholder::make('referencia_title')
                            ->hiddenLabel()
                            ->visible(fn ($record) => $record->solicitudReferencia !== null)
                            ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Referencia a Secretaría de Salud</h3></div>')),

                        Grid::make(2)
                            ->visible(fn ($record) => $record->solicitudReferencia !== null)
                            ->schema([
                                Placeholder::make('folio_referencia')
                                    ->label('Folio')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #0f766e; font-size: 0.95rem; font-weight: 600;">'.($record->solicitudReferencia?->folio ?? 'N/A').'</div>')),
                                Placeholder::make('estatus_cita')
                                    ->label('Estatus de la cita')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.($record->solicitudReferencia?->estatus_cita ?: 'Sin registrar').'</div>')),
                                Placeholder::make('fecha_cita')
                                    ->label('Fecha de la cita')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.($record->solicitudReferencia?->fecha_cita?->format('d/m/Y H:i') ?: 'Pendiente de asignar').'</div>')),
                                Placeholder::make('unidad_atencion')
                                    ->label('Unidad de atención')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.($record->solicitudReferencia?->unidad_atencion_completa ?: 'Pendiente de asignar').'</div>')),
                            ]),
                    ]),
                Action::make('formatoReferencia')
                    ->label('Formato de referencia')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->iconButton()
                    ->tooltip('Formato de referencia a Secretaría de Salud')
                    ->color('warning')
                    ->modalHeading('Solicitud de referencia complementaria')
                    ->modalDescription('Secretaría de Salud usa estos datos para asignar la cita de la persona referida.')
                    ->modalSubmitActionLabel('Guardar solicitud')
                    ->modalWidth('5xl')
                    // Solo aparece cuando la empresa marcó que el caso requiere referencia.
                    ->visible(fn ($record) => (bool) $record->referencia_secretaria_salud)
                    ->fillForm(fn ($record) => SolicitudReferenciaForm::valoresIniciales($record))
                    ->form(SolicitudReferenciaForm::componentes(puedeAgendar: false))
                    ->action(function (array $data, $record) {
                        // La empresa nunca escribe el bloque de cita: son campos
                        // deshabilitados y Filament no los envía, pero se descartan
                        // de forma explícita para que no puedan colarse.
                        unset($data['fecha_cita'], $data['unidad_atencion'], $data['unidad_atencion_otra'], $data['estatus_cita']);

                        $record->solicitudReferencia()->updateOrCreate(
                            ['caso_seguimiento_id' => $record->id],
                            $data + ['empresa_id' => $record->empresa_id],
                        );

                        Notification::make()
                            ->title('Solicitud de referencia guardada')
                            ->body('Secretaría de Salud podrá asignar la cita. La verás en esta misma tabla.')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
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
