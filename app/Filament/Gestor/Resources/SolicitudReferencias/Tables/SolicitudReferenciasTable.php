<?php

namespace App\Filament\Gestor\Resources\SolicitudReferencias\Tables;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\SolicitudReferenciaForm;
use App\Support\CatalogoUnidadesAtencion;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Listado de referencias a Secretaría de Salud. Lo comparten el panel del
 * Gestor y el de administración: ambos perfiles pueden asignar la cita.
 */
class SolicitudReferenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('nombre_usuario')
                    ->label('Persona referida')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => trim(collect([$record->sexo, $record->edad])->filter()->implode(' · ')) ?: null),

                TextColumn::make('empresa.nombre_empresa')
                    ->label('Empresa que solicita')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('municipio')
                    ->label('Municipio')
                    ->searchable()
                    ->description(fn ($record) => $record->jurisdiccion ? 'Jurisdicción '.$record->jurisdiccion : null)
                    ->sortable(),

                TextColumn::make('nivel_riesgo')
                    ->label('Nivel de riesgo')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('servicio_solicitado')
                    ->label('Servicio')
                    ->wrap()
                    ->placeholder('N/A'),

                TextColumn::make('fecha_solicitud')
                    ->label('Solicitud')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_cita')
                    ->label('Cita')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->fecha_cita
                        ? $record->fecha_cita->format('d/m/Y H:i')
                        : 'Pendiente')
                    ->color(fn ($record) => $record->fecha_cita ? 'success' : 'warning')
                    ->description(fn ($record) => $record->unidad_atencion_completa)
                    ->sortable(),

                // Avance de la cita, con los mismos colores que Salud usa en su
                // propio concentrado para no tener que reinterpretarlos.
                TextColumn::make('estatus_cita')
                    ->label('Estatus')
                    ->badge()
                    ->placeholder('Sin registrar')
                    ->color(fn (?string $state): string => SolicitudReferenciaForm::COLORES_ESTATUS_CITA[$state] ?? 'gray')
                    ->sortable(),
            ])
            ->defaultSort('fecha_solicitud', 'desc')
            ->filters([
                Filter::make('sin_cita')
                    ->label('Pendientes de agendar')
                    ->query(fn ($query) => $query->whereNull('fecha_cita'))
                    ->default(),

                SelectFilter::make('nivel_riesgo')
                    ->label('Nivel de riesgo')
                    ->options([
                        'Leve' => 'Leve',
                        'Moderado' => 'Moderado',
                        'Urgente' => 'Urgente',
                    ]),

                SelectFilter::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre_empresa')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('asignarCita')
                    ->label('Asignar cita')
                    ->icon('heroicon-o-calendar-days')
                    ->iconButton()
                    ->tooltip('Asignar cita y unidad de atención')
                    ->color('primary')
                    ->modalHeading('Asignar cita')
                    ->modalDescription(fn ($record) => 'Folio '.$record->folio.' · '.$record->nombre_usuario)
                    ->modalSubmitActionLabel('Guardar cita')
                    ->fillForm(fn ($record) => $record->only([
                        'fecha_cita', 'unidad_atencion', 'unidad_atencion_otra', 'estatus_cita',
                    ]))
                    ->form([
                        DateTimePicker::make('fecha_cita')
                            ->label('Fecha y hora de la cita')
                            ->displayFormat('d/m/Y h:i A')
                            ->seconds(false)
                            ->required(),

                        Select::make('unidad_atencion')
                            ->label('Unidad de atención')
                            ->options(SolicitudReferenciaForm::unidadesAtencion())
                            ->searchable()
                            ->optionsLimit(CatalogoUnidadesAtencion::limiteOpciones())
                            ->live()
                            ->required(),

                        TextInput::make('unidad_atencion_otra')
                            ->label('¿En dónde?')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('unidad_atencion') === 'Otro')
                            ->required(fn (Get $get): bool => $get('unidad_atencion') === 'Otro'),

                        Select::make('estatus_cita')
                            ->label('Estatus')
                            ->options(SolicitudReferenciaForm::ESTATUS_CITA),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update($data + ['asignada_por' => auth()->id()]);

                        Notification::make()
                            ->title('Cita asignada')
                            ->body('La empresa solicitante ya puede ver la fecha y la unidad de atención.')
                            ->success()
                            ->send();
                    }),

                Action::make('verFormato')
                    ->label('Ver formato')
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->tooltip('Ver formato de referencia completo')
                    ->color('gray')
                    ->modalHeading('Formato de referencia')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->fillForm(fn ($record) => $record->attributesToArray())
                    ->form(SolicitudReferenciaForm::componentes(puedeAgendar: true, soloLectura: true)),
            ]);
    }
}
