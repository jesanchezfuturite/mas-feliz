<?php

namespace App\Filament\Empresa\Resources\Tamizajes;

use App\Filament\Empresa\Resources\Tamizajes\Pages\ManageTamizajes;
use App\Models\Tamizaje;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class TamizajeResource extends Resource
{
    protected static ?string $model = Tamizaje::class;

    protected static ?string $modelLabel = 'Evaluación Realizada';
    protected static ?string $pluralModelLabel = 'Evaluaciones Realizadas';
    protected static ?string $navigationLabel = 'Evaluaciones Realizadas';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('empresa_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        $getColor = function ($level) {
            if (in_array($level, ['Grave', 'Moderadamente grave', 'Riesgo Agudo', 'Urgente'])) return '#ef4444'; // Red
            if (in_array($level, ['Moderada', 'Evaluación Adicional', 'Moderado'])) return '#f59e0b'; // Orange
            if (in_array($level, ['Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo'])) return '#22c55e'; // Green
            return '#6b7280'; // Gray
        };

        $makeBadge = function ($field, $labelPrefix) use ($getColor) {
            return Placeholder::make($field)
                ->hiddenLabel()
                ->content(function ($record) use ($getColor, $field, $labelPrefix) {
                    $value = $record ? $record->{$field} : 'N/A';
                    $color = $getColor($value);
                    return new HtmlString("<span style=\"background-color: {$color}; color: white; padding: 8px 16px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;\">{$labelPrefix}: {$value}</span>");
                });
        };

        $makeText = function ($field, $label) {
            return Placeholder::make($field)
                ->label($label)
                ->content(function ($record) use ($field) {
                    $value = $record ? $record->{$field} : 'N/A';
                    if ($field === 'actividad_trabajo') {
                        $value = $record?->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record?->actividad_trabajo;
                    }
                    return new HtmlString("<div style=\"color: #6b7280; font-size: 0.95rem;\">{$value}</div>");
                });
        };

        return $schema
            ->columns(1)
            ->components([
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        $makeBadge('nivel_ansiedad', 'Ansiedad'),
                        $makeBadge('nivel_depresion', 'Depresión'),
                        $makeBadge('nivel_suicidio', 'Riesgo Suicida'),
                    ]),

                Placeholder::make('info_title')
                    ->hiddenLabel()
                    ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Información del Empleado</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" /></svg></span></div>')),

                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        $makeText('nombre_completo', 'Nombre Completo'),
                        $makeText('genero', 'Género'),
                        $makeText('edad', 'Grupo de Edad'),
                        $makeText('tiempo_trabajando', 'Tiempo trabajando'),
                        $makeText('actividad_trabajo', 'Departamento / Actividad')->columnSpanFull(),
                    ]),

                Placeholder::make('seguimiento_title')
                    ->hiddenLabel()
                    ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Seguimiento</h3><span style="color: #556ee6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5rem; height: 1.5rem;"><path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.158 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" /><path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" /></svg></span></div>')),

                \Filament\Schemas\Components\Grid::make(1)
                    ->schema([
                        Textarea::make('comentarios')->label('Comentarios')->rows(4)->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')->label('Nombre Completo')->searchable()->sortable(),
                TextColumn::make('edad')->label('Grupo de Edad')->alignCenter(),
                TextColumn::make('actividad_trabajo')
                    ->label('Departamento')
                    ->getStateUsing(fn ($record) => $record->actividad_trabajo === 'Otra' ? $record->actividad_trabajo_otra : $record->actividad_trabajo)
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('nivel_riesgo_general')
                    ->label('Riesgo General')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Urgente' => 'danger',
                        'Moderado' => 'warning',
                        'Leve' => 'success',
                        default => 'gray',
                    })
                    ->alignCenter(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y')->sortable()->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make('Ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalle de Evaluación')
                    ->modalSubmitActionLabel('Enviar a seguimiento')
                    ->modalFooterActionsAlignment('right'),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTamizajes::route('/'),
        ];
    }
}
