<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions;

use App\Filament\Empresa\Resources\Autoevaluacions\Pages\CreateAutoevaluacion;
use App\Filament\Empresa\Resources\Autoevaluacions\Pages\EditAutoevaluacion;
use App\Filament\Empresa\Resources\Autoevaluacions\Pages\ListAutoevaluacions;
use App\Filament\Empresa\Resources\Autoevaluacions\Schemas\AutoevaluacionForm;
use App\Filament\Empresa\Resources\Autoevaluacions\Tables\AutoevaluacionsTable;
use App\Models\Autoevaluacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AutoevaluacionResource extends Resource
{
    public static function canAccess(): bool
    {
        return \App\Models\Setting::where('key', 'global_config')->first()?->herramientas_empresa_activas ?? false;
    }

    protected static ?string $model = Autoevaluacion::class;

    protected static ?string $modelLabel = 'Autoevaluación';

    protected static ?string $pluralModelLabel = 'Autoevaluaciones';

    protected static ?string $navigationLabel = 'Autoevaluación';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return AutoevaluacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutoevaluacionsTable::configure($table);
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

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAutoevaluacions::route('/'),
            'create' => CreateAutoevaluacion::route('/create'),
            'edit' => EditAutoevaluacion::route('/{record}/edit'),
        ];
    }
}

