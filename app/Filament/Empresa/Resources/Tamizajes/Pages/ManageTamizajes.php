<?php

namespace App\Filament\Empresa\Resources\Tamizajes\Pages;

use App\Filament\Empresa\Resources\Tamizajes\TamizajeResource;
use App\Models\Empresa;
use App\Support\ExportacionTamizajes;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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
            Action::make('exportarExcel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->tooltip('Descarga el listado de personas con sus resultados')
                ->action(function () {
                    $empresa = auth()->user();
                    $empresa = $empresa instanceof Empresa ? $empresa : null;

                    // La consulta de la tabla, no el modelo pelado: así el
                    // archivo sale con lo que la empresa tiene en pantalla
                    // (su propio listado, con la búsqueda y el orden puestos).
                    $consulta = $this->getFilteredSortedTableQuery() ?? TamizajeResource::getEloquentQuery();

                    $ruta = tempnam(sys_get_temp_dir(), 'mf-tamizajes-');

                    $total = ExportacionTamizajes::escribir($consulta, $empresa, $ruta);

                    if ($total === 0) {
                        @unlink($ruta);

                        Notification::make()
                            ->title('No hay registros para exportar')
                            ->body('Aún no se han aplicado diagnósticos con los filtros actuales.')
                            ->warning()
                            ->send();

                        return null;
                    }

                    // El tipo va explícito: sin él la descarga de Livewire
                    // llega al navegador sin MIME y algunos la guardan como
                    // archivo suelto en vez de hoja de cálculo.
                    return response()->streamDownload(function () use ($ruta) {
                        readfile($ruta);
                        @unlink($ruta);
                    }, ExportacionTamizajes::nombreArchivo($empresa), [
                        'Content-Type' => ExportacionTamizajes::TIPO_MIME,
                    ]);
                }),
        ];
    }
}
