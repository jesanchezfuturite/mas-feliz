<?php

namespace App\Filament\Evaluador\Resources;

use App\Filament\Evaluador\Resources\AutoevaluacionResource\Pages;
use App\Models\Autoevaluacion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Filament\Empresa\Resources\Autoevaluacions\Schemas\AutoevaluacionForm;
use Illuminate\Database\Eloquent\Builder;

class AutoevaluacionResource extends Resource
{
    protected static ?string $model = Autoevaluacion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Auditoría';

    protected static ?string $modelLabel = 'Autoevaluación';
    protected static ?string $pluralModelLabel = 'Autoevaluaciones';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('empresa.evaluadores', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function form(Schema $schema): Schema
    {
        // Re-use the exact same form from the Empresa panel
        return AutoevaluacionForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutoevaluacions::route('/'),
            'view' => Pages\ViewAutoevaluacion::route('/{record}'),
        ];
    }
}
