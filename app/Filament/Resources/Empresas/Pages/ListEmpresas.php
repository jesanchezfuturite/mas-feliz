<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Actions\ExportarTamizajesAction;
use App\Filament\Resources\Empresas\EmpresaResource;
use App\Filament\Resources\Empresas\Widgets\EmpresaStats;
use App\Models\Tamizaje;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes(['class' => 'btn-crear-empresa']),

            // La versión de gobierno del export de la empresa: los tamizajes
            // de todas las organizaciones que están en el listado (con los
            // filtros puestos), en un solo archivo con columna "Organización".
            ExportarTamizajesAction::make()
                ->label('Exportar tamizajes')
                ->color('gray')
                ->tooltip('Descarga en Excel los tamizajes de las organizaciones listadas')
                ->requiresConfirmation()
                ->modalHeading('Exportar tamizajes a Excel')
                ->modalDescription('Se genera una hoja con las personas y sus resultados de todas las organizaciones del listado. Con muchas organizaciones puede tardar unos segundos.')
                ->modalSubmitActionLabel('Descargar')
                ->consulta(function () {
                    $empresas = $this->getFilteredSortedTableQuery() ?? EmpresaResource::getEloquentQuery();

                    // `reorder()` porque el orden del listado de empresas no
                    // tiene nada que hacer dentro de la subconsulta.
                    return Tamizaje::query()
                        ->whereIn('empresa_id', $empresas->clone()->reorder()->select('empresas.id'))
                        ->orderBy('empresa_id')
                        ->orderBy('id');
                })
                // Sin empresa: el archivo cruza organizaciones y la hoja lo dice.
                ->empresa(fn () => null),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EmpresaStats::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            EmpresaStats::class,
        ];
    }
}
