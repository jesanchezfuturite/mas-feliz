<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AutoevaluacionsTable
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
                        Esta herramienta te permite consultar y gestionar los registros de autoevaluación aplicados, así como generar y descargar el acuse de cada evaluación.
                    </div>
                </div>
            '))
            ->columns([
                TextColumn::make('fecha_evaluacion')
                    ->label('Fecha de Evaluación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Borrador' => 'gray',
                        'En revisión' => 'warning',
                        'Autorizada' => 'success',
                        default => 'primary',
                    }),

                TextColumn::make('puntaje_total')
                    ->label('Puntaje Total')
                    ->badge()
                    ->getStateUsing(function ($record): int {
                        $sum = 0;
                        $respuestas = $record->respuestas ?? [];
                        foreach ($respuestas as $criterio => $elementos) {
                            if (is_array($elementos)) {
                                foreach ($elementos as $elemento => $data) {
                                    if (isset($data['score']) && is_numeric($data['score'])) {
                                        $sum += (int) $data['score'];
                                    }
                                }
                            }
                        }
                        return $sum;
                    })
                    ->color(fn (int $state): string => match (true) {
                        $state >= 200 => 'success',
                        $state >= 100 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(false), // Calculated column, sorting disabled on db level
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar'),
                Action::make('solicitar_revision')
                    ->label('Solicitar Revisión')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Solicitar Revisión')
                    ->requiresConfirmation()
                    ->modalHeading('Solicitar Revisión')
                    ->modalDescription('¿Estás seguro de solicitar la revisión?')
                    ->modalSubmitActionLabel('Sí, solicitar')
                    ->modalCancelActionLabel('No')
                    ->hidden(fn ($record) => $record->estatus !== 'Borrador')
                    ->action(function ($record) {
                        $record->update(['estatus' => 'En revisión']);
                        \Filament\Notifications\Notification::make()
                            ->title('Revisión solicitada')
                            ->success()
                            ->send();
                    }),
                Action::make('descargar_acuse')
                    ->label('Descargar Acuse')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->iconButton()
                    ->tooltip('Descargar Acuse')
                    ->action(function ($record) {
                        return response()->streamDownload(function () use ($record) {
                            echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.acuse-autoevaluacion', [
                                'autoevaluacion' => $record,
                            ])->output();
                        }, 'Acuse_Autoevaluacion_' . $record->empresa->folio . '.pdf');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
