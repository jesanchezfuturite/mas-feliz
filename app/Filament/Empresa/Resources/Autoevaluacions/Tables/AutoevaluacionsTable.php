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
            ->columns([
                TextColumn::make('fecha_evaluacion')
                    ->label('Fecha de Evaluación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('puntaje_total')
                    ->label('Puntaje Total')
                    ->badge()
                    ->getStateUsing(function ($record): int {
                        $sum = 0;
                        for ($i = 1; $i <= 25; $i++) {
                            $val = $record->{"criterio_{$i}"};
                            if (is_numeric($val)) {
                                    $sum += (int) $val;
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
