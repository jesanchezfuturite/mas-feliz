<?php

namespace App\Filament\Resources\SolicitudReferencias;

use App\Filament\Gestor\Resources\SolicitudReferencias\Tables\SolicitudReferenciasTable;
use App\Filament\Resources\SolicitudReferencias\Pages\ManageSolicitudReferencias;
use App\Models\SolicitudReferencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

/**
 * Espejo del recurso del Gestor en el panel de administración: Angélica pidió
 * que el admin también pueda asignar citas, no solo el Gestor.
 */
class SolicitudReferenciaResource extends Resource
{
    protected static ?string $model = SolicitudReferencia::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Referencia';
    protected static ?string $pluralModelLabel = 'Referencias a Salud';
    protected static ?string $navigationLabel = 'Referencias a Salud';
    protected static ?string $slug = 'referencias';
    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return SolicitudReferenciasTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSolicitudReferencias::route('/'),
        ];
    }
}
