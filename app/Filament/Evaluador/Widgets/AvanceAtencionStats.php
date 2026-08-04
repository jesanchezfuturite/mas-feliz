<?php

namespace App\Filament\Evaluador\Widgets;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mismo tablero de avance que ve la administración, pero acotado a las
 * empresas que el evaluador tiene asignadas.
 */
class AvanceAtencionStats extends \App\Filament\Widgets\AvanceAtencionStats
{
    protected ?string $description = 'Avance de las empresas que tienes asignadas. No incluye datos personales de los colaboradores.';

    protected static ?int $sort = 2;

    protected function empresaIds(): ?array
    {
        return Empresa::whereHas('evaluadores', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->pluck('id')->all();
    }
}
