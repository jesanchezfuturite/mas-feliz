<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoSeguimiento extends Model
{
    protected $guarded = [];

    protected $casts = [
        'servicios' => 'array',
        'consentimiento' => 'boolean',
        'referencia_secretaria_salud' => 'boolean',
    ];

    /**
     * Get the company that owns this case.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * El formato de referencia a Secretaría de Salud. Es único por caso:
     * el folio se define como "único de cada caso".
     */
    public function solicitudReferencia()
    {
        return $this->hasOne(SolicitudReferencia::class);
    }

    /**
     * Servicios requeridos en texto, resolviendo la opción "Otro".
     */
    public function getServiciosTextoAttribute(): string
    {
        $servicios = collect($this->servicios ?? [])
            ->map(fn ($s) => $s === 'Otro' ? ($this->servicio_otro ?: 'Otro') : $s)
            ->filter();

        return $servicios->isEmpty() ? 'N/A' : $servicios->implode(', ');
    }
}
