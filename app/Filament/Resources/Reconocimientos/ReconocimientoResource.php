<?php

namespace App\Filament\Resources\Reconocimientos;

use App\Filament\Resources\Reconocimientos\Pages\ManageReconocimientos;
use App\Filament\Resources\Reconocimientos\Tables\ReconocimientosTable;
use App\Models\Empresa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * La tabla que Angélica armaba a mano antes de cada entrega (31/10/2026):
 * las organizaciones que obtuvieron el distintivo, con el nombre que va en
 * el reconocimiento, su logotipo, su nivel y los contactos para RP.
 *
 * Es otra vista de las mismas empresas, no un modelo nuevo: solo entran las
 * aprobadas, que son a las que se les entrega reconocimiento.
 */
class ReconocimientoResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $modelLabel = 'Reconocimiento';

    protected static ?string $pluralModelLabel = 'Reconocimientos';

    protected static ?string $navigationLabel = 'Reconocimientos';

    protected static ?string $slug = 'reconocimientos';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->aprobadas();
    }

    public static function table(Table $table): Table
    {
        return ReconocimientosTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReconocimientos::route('/'),
        ];
    }
}
