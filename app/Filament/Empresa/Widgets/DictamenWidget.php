<?php

namespace App\Filament\Empresa\Widgets;

use App\Filament\Empresa\Pages\DatosReconocimiento;
use App\Models\Empresa;
use Filament\Widgets\Widget;

/**
 * El resultado de la dictaminación, en el escritorio de la empresa.
 *
 * Angélica (29/10/2026): el correo de resultados invita a «ingresar a la
 * plataforma y consultar el nivel de madurez alcanzado», pero la empresa no
 * veía por ningún lado su estatus, su nivel ni el comentario del dictamen.
 * Va en la principal porque es lo que se les pidió venir a ver.
 *
 * Desde el 31/10/2026 la empresa aprobada pasa primero por un candado: no ve
 * su nivel, su comentario ni su distintivo hasta confirmar los datos de su
 * reconocimiento ({@see DatosReconocimiento}). A la no aprobada no se le pide
 * nada: no hay reconocimiento que elaborar.
 */
class DictamenWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.dictamen-widget';

    protected int|string|array $columnSpan = 'full';

    // Es justo lo que la empresa viene a leer: se dibuja con la página.
    protected static bool $isLazy = false;

    /**
     * Mientras Gobierno no dictamine no hay nada que enseñar: la Ruta
     * Crítica ya dice en qué fase va la empresa.
     */
    public static function canView(): bool
    {
        $empresa = auth()->user();

        return $empresa instanceof Empresa && $empresa->tieneDictamen();
    }

    protected function getViewData(): array
    {
        $empresa = auth()->user();

        if ($empresa->reconocimientoPendiente()) {
            return [
                'candado' => true,
                'urlReconocimiento' => DatosReconocimiento::getUrl(),
            ];
        }

        return [
            'candado' => false,
            'urlReconocimiento' => $empresa->estaAprobada() ? DatosReconocimiento::getUrl() : null,
            'nombreReconocimiento' => $empresa->estaAprobada() ? $empresa->nombre_para_reconocimiento : null,
            'estatus' => Empresa::etiquetaEstatusDistintivo($empresa->estatus_distintivo),
            'nivel' => $empresa->nivel_madurez_asignado,
            'fecha' => $empresa->fecha_dictamen,
            'comentario' => $empresa->retroalimentacion_gobierno,
            'rutaPdf' => $empresa->ruta_pdf,
        ];
    }
}
