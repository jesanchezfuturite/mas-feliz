<?php

namespace App\Filament\Gestor\Resources\CasosCanalizados\Pages;

use App\Filament\Gestor\Resources\CasosCanalizados\CasoCanalizadoResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCasosCanalizados extends ManageRecords
{
    protected static string $resource = CasoCanalizadoResource::class;

    public function getTitle(): string
    {
        return 'Casos canalizados';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
