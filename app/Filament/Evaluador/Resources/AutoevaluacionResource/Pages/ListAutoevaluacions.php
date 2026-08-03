<?php

namespace App\Filament\Evaluador\Resources\AutoevaluacionResource\Pages;

use App\Filament\Evaluador\Resources\AutoevaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListAutoevaluacions extends ListRecords
{
    protected static string $resource = AutoevaluacionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresa.nombre_empresa')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estatus')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Validado' => 'success',
                        'En revisión' => 'warning',
                        'Borrador' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('fecha_evaluacion')
                    ->label('Fecha de Envío')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                // Permite al acompañante detectar cuándo la empresa movió su autoevaluación
                // sin tener que entrar criterio por criterio.
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->description(fn ($record) => $record->updated_at?->diffForHumans())
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estatus')
                    ->options([
                        'En revisión' => 'En revisión',
                        'Validado' => 'Validado',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }
}
