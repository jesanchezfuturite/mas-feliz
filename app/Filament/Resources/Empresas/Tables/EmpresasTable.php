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
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre_empresa')
                    ->label('Nombre de la Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('municipio')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rubro')
                    ->label('Rubro')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('numero_trabajadores')
                    ->label('Número de Trabajadores')
                    ->numeric()
                    ->sortable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
