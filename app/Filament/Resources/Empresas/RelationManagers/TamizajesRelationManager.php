<?php

namespace App\Filament\Resources\Empresas\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TamizajesRelationManager extends RelationManager
{
    protected static string $relationship = 'tamizajes';

    protected static ?string $title = 'Historial de Tamizajes (Evaluaciones Anónimas)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('nivel_riesgo_general')
                    ->label('Nivel de Riesgo General')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Infolists\Components\TextEntry::make('riesgo_ansiedad')
                    ->label('Riesgo de Ansiedad (Puntuación)'),
                \Filament\Infolists\Components\TextEntry::make('riesgo_depresion')
                    ->label('Riesgo de Depresión (Puntuación)'),
                \Filament\Infolists\Components\TextEntry::make('riesgo_conducta_suicida')
                    ->label('Riesgo de Conducta Suicida (Puntuación)'),
                \Filament\Infolists\Components\TextEntry::make('created_at')
                    ->label('Fecha de Evaluación')
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nivel_riesgo_general')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha de Evaluación')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('nivel_riesgo_general')
                    ->label('Nivel de Riesgo General')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('riesgo_ansiedad')
                    ->label('Ansiedad')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('riesgo_depresion')
                    ->label('Depresión')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('riesgo_conducta_suicida')
                    ->label('Conducta Suicida')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
