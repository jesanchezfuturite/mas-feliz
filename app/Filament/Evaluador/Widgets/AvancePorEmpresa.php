<?php

namespace App\Filament\Evaluador\Widgets;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;

/**
 * Avance por organización acotado a las empresas asignadas al evaluador.
 */
class AvancePorEmpresa extends \App\Filament\Widgets\AvancePorEmpresa
{
    protected static ?int $sort = 4;

    protected function empresaIds(): ?array
    {
        return Empresa::whereHas('evaluadores', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->pluck('id')->all();
    }
}
