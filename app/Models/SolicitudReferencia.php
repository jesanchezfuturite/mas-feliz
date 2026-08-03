<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudReferencia extends Model
{
    protected $table = 'solicitudes_referencia';

    protected $guarded = [];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_cita' => 'datetime',
    ];

    /**
     * Genera el folio correlativo del año en curso. Mismo criterio que
     * Empresa::booted(): se bloquea el último registro para evitar folios
     * duplicados si dos empresas solicitan al mismo tiempo.
     */
    protected static function booted(): void
    {
        static::creating(function (SolicitudReferencia $solicitud) {
            if ($solicitud->folio) {
                return;
            }

            $prefijo = 'REF-' . now()->year . '-';

            $ultimo = static::query()
                ->where('folio', 'like', $prefijo . '%')
                ->orderBy('folio', 'desc')
                ->lockForUpdate()
                ->first();

            $siguiente = $ultimo ? ((int) substr($ultimo->folio, strlen($prefijo))) + 1 : 1;

            $solicitud->folio = $prefijo . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
        });
    }

    public function casoSeguimiento()
    {
        return $this->belongsTo(CasoSeguimiento::class, 'caso_seguimiento_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function asignadaPor()
    {
        return $this->belongsTo(User::class, 'asignada_por');
    }

    /**
     * Una solicitud está agendada cuando Salud ya definió fecha y lugar.
     */
    public function getEstaAgendadaAttribute(): bool
    {
        return $this->fecha_cita !== null;
    }

    /**
     * Unidad de atención mostrable, resolviendo el caso "Otro".
     */
    public function getUnidadAtencionCompletaAttribute(): ?string
    {
        if ($this->unidad_atencion === 'Otro') {
            return $this->unidad_atencion_otra;
        }

        return $this->unidad_atencion;
    }
}
