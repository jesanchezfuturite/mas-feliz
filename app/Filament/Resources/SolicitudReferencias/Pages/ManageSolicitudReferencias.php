<?php

namespace App\Filament\Resources\SolicitudReferencias\Pages;

use App\Filament\Resources\SolicitudReferencias\SolicitudReferenciaResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSolicitudReferencias extends ManageRecords
{
    protected static string $resource = SolicitudReferenciaResource::class;

    public function getTitle(): string
    {
        return 'Referencias a Secretaría de Salud';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
