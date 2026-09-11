<?php

namespace App\Filament\Empresa\Resources\Tamizajes\Pages;

use App\Filament\Actions\ExportarTamizajesAction;
use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use App\Models\Empresa;
use Filament\Resources\Pages\ManageRecords;

class ManageTamizajes extends ManageRecords
{
    protected static string $resource = TamizajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Angélica lo pidió el 03/09/2026: las empresas quieren llevarse el
            // listado de personas y sus resultados en una hoja de cálculo. No
            // necesita su propio permiso: la página completa ya está detrás del
            // interruptor de herramientas y del de resultados visibles
            // (TamizajeResource::canAccess).
            ExportarTamizajesAction::make()
                ->color('primary')
                // La consulta de la tabla, no el modelo pelado: así el archivo
                // sale con lo que la empresa tiene en pantalla (su propio
                // listado, con la búsqueda y el orden puestos).
                ->consulta(fn () => $this->getFilteredSortedTableQuery() ?? TamizajeResource::getEloquentQuery())
                ->empresa(fn () => auth()->user() instanceof Empresa ? auth()->user() : null),
        ];
    }
}
