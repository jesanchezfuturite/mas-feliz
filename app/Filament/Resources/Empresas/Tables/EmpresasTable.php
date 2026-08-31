<?php

namespace App\Filament\Resources\Empresas\Tables;

use App\Filament\Resources\AutoevaluacionResource;
use App\Mail\VisitaAgendadaMail;
use App\Models\Empresa;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class EmpresasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Listado')
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre_empresa')
                    ->label('Nombre de la Empresa')
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'col-nombre-empresa']),
                TextColumn::make('municipio')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('rubro')
                    ->label('Rubro')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('numero_trabajadores')
                    ->label('Número de Trabajadores')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                // Algunas instituciones pidieron no ver sus resultados: este
                // apagador restringe solo a esa empresa. El interruptor global de
                // Configuración General sigue mandando por encima de todos.
                ToggleColumn::make('resultados_tamizaje_visibles')
                    ->label('Resultados visibles')
                    ->tooltip('Si se apaga, esta empresa deja de ver el listado de resultados del tamizaje y las gráficas de riesgo, aunque el interruptor global esté encendido.')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('municipio')
                    ->label('Municipio')
                    ->options(fn () => Empresa::query()->distinct()->pluck('municipio', 'municipio')->filter()->toArray()),
                SelectFilter::make('rubro')
                    ->label('Rubro')
                    ->options(fn () => Empresa::query()->distinct()->pluck('rubro', 'rubro')->filter()->toArray()),
            ])
            ->recordActions([
                Action::make('autoevaluacion')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->iconButton()
                    ->color(fn ($record) => optional($record->autoevaluaciones()->latest()->first())->estatus === 'Validado' ? 'success' : 'warning')
                    ->tooltip('Ver Autoevaluación')
                    ->url(fn ($record) => optional($record->autoevaluaciones()->latest()->first())->id ? AutoevaluacionResource::getUrl('view', ['record' => $record->autoevaluaciones()->latest()->first()->id]) : null)
                    ->hidden(fn ($record) => ! in_array(optional($record->autoevaluaciones()->latest()->first())->estatus, ['En revisión', 'Validado'])),
                Action::make('descargar_distintivo')
                    ->label('Ver Distintivo')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Ver Distintivo')
                    ->visible(fn ($record) => ! empty($record?->ruta_pdf) && $record?->estatus === 'Dictaminado')
                    ->url(fn ($record) => ! empty($record?->ruta_pdf) ? '/storage/'.$record->ruta_pdf : null)
                    ->openUrlInNewTab(),
                Action::make('certificarFase')
                    ->label('Certificar Fase')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->form([
                        Select::make('paso_certificacion')
                            ->label('Fase Actual de Certificación')
                            ->options(Empresa::opcionesPasoCertificacion())
                            ->required()
                            ->default(fn ($record) => $record->paso_certificacion),
                    ])
                    ->action(function (Empresa $record, array $data): void {
                        $record->update([
                            'paso_certificacion' => $data['paso_certificacion'],
                        ]);

                        Notification::make()
                            ->title('Fase actualizada correctamente')
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->tooltip('Actualizar Fase Oficial'),
                Action::make('agendarVisita')
                    ->label('Agendar Visita')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->form([
                        DateTimePicker::make('fecha_visita_presencial')
                            ->label('Fecha y Hora de la Visita')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('d/m/Y h:i A')
                            ->default(fn ($record) => $record->fecha_visita_presencial),
                    ])
                    ->action(function (Empresa $record, array $data): void {
                        $record->update([
                            'fecha_visita_presencial' => $data['fecha_visita_presencial'],
                        ]);

                        try {
                            Mail::to($record->correo)
                                ->send(new VisitaAgendadaMail($record));

                            Notification::make()
                                ->title('Visita agendada y correo enviado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Visita agendada pero el correo falló')
                                ->warning()
                                ->body('Error: '.$e->getMessage())
                                ->send();
                        }
                    })
                    ->iconButton()
                    ->tooltip('Agendar Visita Presencial'),
                ViewAction::make()
                    ->iconButton()
                    ->color('gray')
                    ->tooltip('Ver detalle'),
                EditAction::make()
                    ->iconButton()
                    ->color('gray')
                    ->tooltip('Editar empresa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
