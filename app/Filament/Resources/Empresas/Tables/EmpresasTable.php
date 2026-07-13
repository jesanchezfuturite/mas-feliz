<?php

namespace App\Filament\Resources\Empresas\Tables;

use App\Models\Empresa;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                \Filament\Actions\Action::make('autoevaluacion')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->iconButton()
                    ->color(fn ($record) => optional($record->autoevaluaciones()->latest()->first())->estatus === 'Validado' ? 'success' : 'warning')
                    ->tooltip('Ver Autoevaluación')
                    ->url(fn ($record) => optional($record->autoevaluaciones()->latest()->first())->id ? \App\Filament\Resources\AutoevaluacionResource::getUrl('view', ['record' => $record->autoevaluaciones()->latest()->first()->id]) : null)
                    ->hidden(fn ($record) => !in_array(optional($record->autoevaluaciones()->latest()->first())->estatus, ['En revisión', 'Validado'])),
                \Filament\Actions\Action::make('descargar_distintivo')
                    ->label('Ver Distintivo')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Ver Distintivo')
                    ->visible(fn ($record) => !empty($record?->ruta_pdf) && $record?->estatus === 'Dictaminado')
                    ->url(fn ($record) => !empty($record?->ruta_pdf) ? '/storage/' . $record->ruta_pdf : null)
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('certificarFase')
                    ->label('Certificar Fase')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('paso_certificacion')
                            ->label('Fase Actual de Certificación')
                            ->options([
                                1 => '1. Registro',
                                2 => '2. Diagnóstico inicial/Autoevaluación',
                                3 => '3. Retroalimentación y Acompañamiento',
                                4 => '4. Plan de acción/Implementación',
                                5 => '5. Evaluación y Dictaminación',
                                6 => '6. Reconocimiento acorde al nivel de Madurez',
                            ])
                            ->required()
                            ->default(fn ($record) => $record->paso_certificacion),
                    ])
                    ->action(function (Empresa $record, array $data): void {
                        $record->update([
                            'paso_certificacion' => $data['paso_certificacion'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Fase actualizada correctamente')
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->tooltip('Actualizar Fase Oficial'),
                \Filament\Actions\Action::make('agendarVisita')
                    ->label('Agendar Visita')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\DateTimePicker::make('fecha_visita_presencial')
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
                            \Illuminate\Support\Facades\Mail::to($record->correo)
                                ->send(new \App\Mail\VisitaAgendadaMail($record));

                            \Filament\Notifications\Notification::make()
                                ->title('Visita agendada y correo enviado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Visita agendada pero el correo falló')
                                ->warning()
                                ->body('Error: ' . $e->getMessage())
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
