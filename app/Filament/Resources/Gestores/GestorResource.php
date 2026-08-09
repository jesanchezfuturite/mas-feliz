<?php

namespace App\Filament\Resources\Gestores;

use App\Filament\Resources\Gestores\Pages\CreateGestor;
use App\Filament\Resources\Gestores\Pages\EditGestor;
use App\Filament\Resources\Gestores\Pages\ListGestores;
use App\Filament\Resources\Gestores\Schemas\GestorForm;
use App\Filament\Resources\Gestores\Tables\GestoresTable;
use App\Models\Gestor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class GestorResource extends Resource
{
    protected static ?string $model = Gestor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Gestor';
    protected static ?string $pluralModelLabel = 'Gestores';
    protected static ?string $slug = 'gestores';

    public static function form(Schema $schema): Schema
    {
        return GestorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GestoresTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGestores::route('/'),
            'create' => CreateGestor::route('/create'),
            'edit' => EditGestor::route('/{record}/edit'),
        ];
    }
}
