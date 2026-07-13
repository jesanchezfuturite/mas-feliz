<?php

namespace App\Filament\Empresa\Widgets;

use Filament\Widgets\Widget;

class EmpresaInfoWidget extends Widget
{
    protected string $view = 'filament.empresa.widgets.empresa-info-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'empresa' => auth()->user(),
        ];
    }
}
