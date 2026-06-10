<?php

namespace App\Filament\Resources\Empresas\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CasosSeguimientoRelationManager extends RelationManager
{
    protected static string $relationship = 'casosSeguimiento';

    protected static ?string $title = 'Bitácora de Casos Clínicos y Seguimiento';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('identificador_empleado')
                    ->label('Identificador del Empleado'),
                \Filament\Infolists\Components\TextEntry::make('nivel_riesgo_detectado')
                    ->label('Nivel de Riesgo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Infolists\Components\TextEntry::make('estatus_atencion')
                    ->label('Estatus de Atención')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En seguimiento' => 'info',
                        'Canalizado' => 'warning',
                        'Cerrado satisfactorio' => 'success',
                        'Abandonó' => 'gray',
                        default => 'gray',
                    }),
                \Filament\Infolists\Components\TextEntry::make('institucion_canalizacion')
                    ->label('Institución de Canalización')
                    ->placeholder('N/A'),
                \Filament\Infolists\Components\TextEntry::make('notas_clinicas')
                    ->label('Notas Clínicas')
                    ->placeholder('Sin notas'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Bitácora de Casos Clínicos y Seguimiento')
            ->recordTitleAttribute('identificador_empleado')
            ->columns([
                TextColumn::make('identificador_empleado')
                    ->label('Identificador')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nivel_riesgo_detectado')
                    ->label('Riesgo Detectado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leve' => 'success',
                        'Moderado' => 'warning',
                        'Urgente' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('estatus_atencion')
                    ->label('Estatus de Atención')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En seguimiento' => 'info',
                        'Canalizado' => 'warning',
                        'Cerrado satisfactorio' => 'success',
                        'Abandonó' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('institucion_canalizacion')
                    ->label('Institución de Canalización')
                    ->placeholder('N/A')
                    ->searchable(),

                TextColumn::make('notas_clinicas')
                    ->label('Notas Clínicas')
                    ->limit(50)
                    ->placeholder('Sin notas'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('icon')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->link()
                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                    ->label('')
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Ver detalle')
                    ->modalCancelAction(false),
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
