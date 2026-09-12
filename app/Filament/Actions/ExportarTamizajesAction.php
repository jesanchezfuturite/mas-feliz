<?php

namespace App\Filament\Actions;

use App\Models\Empresa;
use App\Support\ExportacionTamizajes;
use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

/**
 * Botón de descarga del listado de tamizajes en Excel.
 *
 * Vive en una clase propia porque lo usan tres pantallas —el listado de la
 * empresa, el historial de tamizajes dentro de cada empresa en el panel de
 * gobierno y el listado de empresas del admin, que exporta todas de un jalón—
 * y el proyecto ya tiene bloques duplicados que duelen al tocarlos.
 *
 * Quien lo monta solo dice de dónde salen los renglones (`consulta()`) y de
 * qué organización es el archivo (`empresa()`; `null` cuando cruza varias, y
 * entonces la hoja trae la columna "Organización").
 */
class ExportarTamizajesAction extends Action
{
    protected ?Closure $consulta = null;

    protected ?Closure $empresa = null;

    public static function getDefaultName(): ?string
    {
        return 'exportarTamizajes';
    }

    /** @param  Closure():(Builder|null)  $consulta */
    public function consulta(Closure $consulta): static
    {
        $this->consulta = $consulta;

        return $this;
    }

    /** @param  Closure():(Empresa|null)  $empresa */
    public function empresa(Closure $empresa): static
    {
        $this->empresa = $empresa;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Exportar a Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->tooltip('Descarga el listado de personas con sus resultados')
            ->action(function () {
                $consulta = $this->evaluate($this->consulta);

                if (! $consulta) {
                    return null;
                }

                $empresa = $this->evaluate($this->empresa);
                $empresa = $empresa instanceof Empresa ? $empresa : null;

                $ruta = tempnam(sys_get_temp_dir(), 'mf-tamizajes-');

                $total = ExportacionTamizajes::escribir($consulta, $empresa, $ruta);

                if ($total === 0) {
                    @unlink($ruta);

                    Notification::make()
                        ->title('No hay registros para exportar')
                        ->body('Aún no hay diagnósticos aplicados con los filtros actuales.')
                        ->warning()
                        ->send();

                    return null;
                }

                // El tipo va explícito: sin él la descarga de Livewire llega al
                // navegador sin MIME y algunos la guardan como archivo suelto
                // en vez de hoja de cálculo.
                return response()->streamDownload(function () use ($ruta) {
                    readfile($ruta);
                    @unlink($ruta);
                }, ExportacionTamizajes::nombreArchivo($empresa), [
                    'Content-Type' => ExportacionTamizajes::TIPO_MIME,
                ]);
            });
    }
}
