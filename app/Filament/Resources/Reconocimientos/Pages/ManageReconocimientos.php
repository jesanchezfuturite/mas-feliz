<?php

namespace App\Filament\Resources\Reconocimientos\Pages;

use App\Filament\Resources\Reconocimientos\ReconocimientoResource;
use App\Support\ExportacionReconocimientos;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageReconocimientos extends ManageRecords
{
    protected static string $resource = ReconocimientoResource::class;

    public function getTitle(): string
    {
        return 'Reconocimientos';
    }

    protected function getHeaderActions(): array
    {
        return [
            // La hoja para RP y para el proveedor: una organización por
            // renglón, con lo que esté filtrado en el listado.
            Action::make('exportarExcel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-table-cells')
                ->tooltip('Organizaciones, nivel y contactos de lo que está en el listado')
                ->action(function () {
                    $ruta = tempnam(sys_get_temp_dir(), 'mf-reconocimientos-');

                    $total = ExportacionReconocimientos::escribirExcel($this->consultaDelListado(), $ruta);

                    if ($total === 0) {
                        @unlink($ruta);

                        return $this->avisar('No hay organizaciones para exportar', 'Ninguna organización aprobada coincide con los filtros actuales.');
                    }

                    return response()->streamDownload(function () use ($ruta) {
                        readfile($ruta);
                        @unlink($ruta);
                    }, ExportacionReconocimientos::nombreArchivo('Reconocimientos', 'xlsx', $this->regionFiltrada()), [
                        'Content-Type' => ExportacionReconocimientos::TIPO_MIME_EXCEL,
                    ]);
                }),

            // Los archivos originales, tal como los subió cada empresa, con
            // el nombre de la organización: es lo que se le manda al proveedor.
            Action::make('descargarLogos')
                ->label('Descargar logotipos')
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->tooltip('ZIP con los logotipos en alta resolución de lo que está en el listado')
                ->action(function () {
                    $ruta = tempnam(sys_get_temp_dir(), 'mf-logos-');

                    $total = ExportacionReconocimientos::escribirZipLogos($this->consultaDelListado(), $ruta);

                    if ($total === 0) {
                        @unlink($ruta);

                        return $this->avisar('Todavía no hay logotipos', 'Ninguna organización del listado ha subido su logotipo.');
                    }

                    return response()->streamDownload(function () use ($ruta) {
                        readfile($ruta);
                        @unlink($ruta);
                    }, ExportacionReconocimientos::nombreArchivo('Logotipos', 'zip', $this->regionFiltrada()), [
                        'Content-Type' => ExportacionReconocimientos::TIPO_MIME_ZIP,
                    ]);
                }),
        ];
    }

    private function consultaDelListado(): Builder
    {
        return $this->getFilteredTableQuery() ?? ReconocimientoResource::getEloquentQuery();
    }

    /** La región del filtro, para nombrar el archivo con ella. */
    private function regionFiltrada(): ?string
    {
        return $this->getTableFilterState('region')['value'] ?? null;
    }

    private function avisar(string $titulo, string $cuerpo): null
    {
        Notification::make()->title($titulo)->body($cuerpo)->warning()->send();

        return null;
    }
}
