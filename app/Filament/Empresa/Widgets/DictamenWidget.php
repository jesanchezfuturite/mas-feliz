<?php

namespace App\Filament\Empresa\Widgets;

use App\Models\Empresa;
use Filament\Widgets\Widget;

/**
 * El resultado de la dictaminación, en el escritorio de la empresa.
 *
 * Angélica (29/10/2026): el correo de resultados invita a «ingresar a la
 * plataforma y consultar el nivel de madurez alcanzado», pero la empresa no
 * veía por ningún lado su estatus, su nivel ni el comentario del dictamen.
 * Va en la principal porque es lo que se les pidió venir a ver.
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

        return [
            'estatus' => Empresa::etiquetaEstatusDistintivo($empresa->estatus_distintivo),
            'nivel' => $empresa->nivel_madurez_asignado,
            'fecha' => $empresa->fecha_dictamen,
            'comentario' => $empresa->retroalimentacion_gobierno,
            'rutaPdf' => $empresa->ruta_pdf,
        ];
    }
}
