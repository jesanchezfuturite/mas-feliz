<?php

namespace App\Filament\Gestor\Resources\SolicitudReferencias;

use App\Filament\Gestor\Resources\SolicitudReferencias\Pages\ManageSolicitudReferencias;
use App\Filament\Gestor\Resources\SolicitudReferencias\Tables\SolicitudReferenciasTable;
use App\Models\SolicitudReferencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SolicitudReferenciaResource extends Resource
{
    protected static ?string $model = SolicitudReferencia::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Referencia';
    protected static ?string $pluralModelLabel = 'Referencias a Salud';
    protected static ?string $navigationLabel = 'Referencias a Salud';
    protected static ?string $slug = 'referencias';

    public static function table(Table $table): Table
    {
        return SolicitudReferenciasTable::configure($table);
    }

    /**
     * El Gestor no captura solicitudes: las crea la empresa desde su caso de
     * seguimiento. Aquí solo se consultan y se les asigna cita.
     */
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
