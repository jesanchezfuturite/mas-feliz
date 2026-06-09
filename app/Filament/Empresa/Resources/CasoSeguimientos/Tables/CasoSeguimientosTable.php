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
            ->columns([
                TextColumn::make('identificador_empleado')
                    ->label('Identificador del Empleado')
                    ->searchable()
                    ->sortable(),

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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
