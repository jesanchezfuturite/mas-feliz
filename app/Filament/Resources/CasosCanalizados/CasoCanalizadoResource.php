<?php

namespace App\Filament\Resources\CasosCanalizados;

use App\Filament\Gestor\Resources\CasosCanalizados\Tables\CasosCanalizadosTable;
use App\Filament\Resources\CasosCanalizados\Pages\ManageCasosCanalizados;
use App\Models\CasoSeguimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Misma bandeja de canalizados que ve el Gestor, disponible también para el
 * administrador.
 */
class CasoCanalizadoResource extends Resource
{
    protected static ?string $model = CasoSeguimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $modelLabel = 'Caso canalizado';
    protected static ?string $pluralModelLabel = 'Casos canalizados';
    protected static ?string $navigationLabel = 'Casos canalizados';
    protected static ?string $slug = 'casos-canalizados';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('estatus_atencion', 'Canalizado');
    }

    public static function table(Table $table): Table
    {
        return CasosCanalizadosTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCasosCanalizados::route('/'),
        ];
    }
}
