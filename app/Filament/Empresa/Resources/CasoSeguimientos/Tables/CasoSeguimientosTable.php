<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Tables;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\DetalleCasoForm;
use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\SolicitudReferenciaForm;
use App\Models\CasoSeguimiento;
use App\Support\PrioridadAtencion;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
            // La captura en línea re-renderiza la tabla en cada guardado; sin
            // un orden explícito el motor puede devolver las filas en otro
            // orden y los renglones "se mueven" bajo el cursor.
            ->defaultSort('id')
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
                    // El detalle vive en DetalleCasoForm porque el Gestor y el
                    // admin muestran exactamente lo mismo desde sus pantallas.
                    ->form(DetalleCasoForm::componentes()),

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
