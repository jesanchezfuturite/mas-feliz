<?php

namespace App\Filament\Resources\Evaluadors;

use App\Filament\Resources\Evaluadors\Pages\CreateEvaluador;
use App\Filament\Resources\Evaluadors\Pages\EditEvaluador;
use App\Filament\Resources\Evaluadors\Pages\ListEvaluadors;
use App\Filament\Resources\Evaluadors\Schemas\EvaluadorForm;
use App\Filament\Resources\Evaluadors\Tables\EvaluadorsTable;
use App\Models\Evaluador;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvaluadorResource extends Resource
{
    protected static ?string $model = Evaluador::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Evaluador';
    protected static ?string $pluralModelLabel = 'Evaluadores';

    public static function form(Schema $schema): Schema
    {
        return EvaluadorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluadorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluadors::route('/'),
            'create' => CreateEvaluador::route('/create'),
            'edit' => EditEvaluador::route('/{record}/edit'),
        ];
    }
}
