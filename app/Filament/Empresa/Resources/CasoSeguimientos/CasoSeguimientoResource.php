<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos;

use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\CreateCasoSeguimiento;
use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\EditCasoSeguimiento;
use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\ListCasoSeguimientos;
use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\CasoSeguimientoForm;
use App\Filament\Empresa\Resources\CasoSeguimientos\Tables\CasoSeguimientosTable;
use App\Models\CasoSeguimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CasoSeguimientoResource extends Resource
{
    protected static ?string $model = CasoSeguimiento::class;

    protected static ?string $modelLabel = 'Caso de Seguimiento';

    protected static ?string $pluralModelLabel = 'Casos de Seguimiento';

    protected static ?string $navigationLabel = 'Casos de Seguimiento';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CasoSeguimientoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CasoSeguimientosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('empresa_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCasoSeguimientos::route('/'),
            'create' => CreateCasoSeguimiento::route('/create'),
            'edit' => EditCasoSeguimiento::route('/{record}/edit'),
        ];
    }
}
