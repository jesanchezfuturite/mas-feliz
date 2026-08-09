<?php

namespace App\Filament\Resources\CasosCanalizados\Pages;

use App\Filament\Resources\CasosCanalizados\CasoCanalizadoResource;
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
