<?php

namespace App\Filament\Evaluador\Resources\AutoevaluacionResource\Pages;

use App\Filament\Evaluador\Resources\AutoevaluacionResource;
use App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion as AdminViewAutoevaluacion;

class ViewAutoevaluacion extends AdminViewAutoevaluacion
{
    protected static string $resource = AutoevaluacionResource::class;

    /**
     * Override breadcrumbs to point to the evaluator panel.
     */
    public function getBreadcrumbs(): array
    {
        return [
            \App\Filament\Evaluador\Resources\EmpresaResource::getUrl('index') => 'Empresas',
            '#' => $this->record->empresa->nombre_empresa ?? 'Empresa',
            '' => 'Autoevaluación',
        ];
    }
}
