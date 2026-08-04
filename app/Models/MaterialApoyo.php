<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialApoyo extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'archivo_path',
        'enlace_url',
        'fecha_evento',
        'seccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_evento' => 'datetime',
    ];

    /**
     * Un aviso no lleva archivo ni enlace: comunica una fecha de capacitación.
     */
    public function getEsAvisoAttribute(): bool
    {
        return $this->tipo === 'aviso';
    }
}
