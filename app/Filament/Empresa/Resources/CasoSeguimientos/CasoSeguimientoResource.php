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
    public static function canAccess(): bool
    {
        return \App\Models\Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;
    }

    protected static ?string $model = CasoSeguimiento::class;

    protected static ?string $modelLabel = 'Caso en seguimiento';

    protected static ?string $pluralModelLabel = 'Casos en seguimiento';

    protected static ?string $navigationLabel = 'Atención/ Casos en seguimiento (Apoyo al criterio indispensable 15)';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

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
