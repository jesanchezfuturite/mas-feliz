<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'herramientas_empresa_activas', 'resultados_tamizaje_visibles'];

    protected $casts = [
        'herramientas_empresa_activas' => 'boolean',
        'resultados_tamizaje_visibles' => 'boolean',
    ];

    /**
     * ¿La empresa puede ver los resultados del tamizaje por individuo?
     *
     * Se resuelve aquí y no repitiendo la consulta en cada recurso para que el
     * valor por omisión sea uno solo: si todavía no existe la fila de
     * `global_config`, se muestran, que es como se comportó siempre.
     *
     * El interruptor global manda; el de la empresa (columna
     * `empresas.resultados_tamizaje_visibles`, apagable desde el listado de
     * Empresas del admin) solo puede restringir. Es para las instituciones que
     * pidieron no tener visibilidad de sus resultados aunque el resto sí la tenga.
     */
    public static function resultadosTamizajeVisibles(?Empresa $empresa = null): bool
    {
        $global = (bool) (static::where('key', 'global_config')->first()?->resultados_tamizaje_visibles ?? true);

        return $global && ($empresa?->resultados_tamizaje_visibles ?? true);
    }
}
