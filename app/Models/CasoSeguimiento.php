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

    private ?Tamizaje $tamizajeCache = null;

    private bool $tamizajeResuelto = false;

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
     * Tamizaje del que proviene el caso, si la persona contestó el cuestionario.
     * El vínculo es por nombre porque el tamizaje no guarda una llave foránea.
     *
     * Se cachea por instancia: la tabla lo consulta desde una decena de columnas
     * y antes provocaba una consulta por celda.
     */
    public function getTamizajeAttribute(): ?Tamizaje
    {
        if (! $this->tamizajeResuelto) {
            $this->tamizajeCache = Tamizaje::where('empresa_id', $this->empresa_id)
                ->where('nombre_completo', $this->identificador_empleado)
                ->first();

            $this->tamizajeResuelto = true;
        }

        return $this->tamizajeCache;
    }

    /**
     * Un caso es "En línea" cuando nació de un tamizaje, y "Manual" cuando lo
     * capturó la empresa a mano.
     */
    public function getEsDeTamizajeAttribute(): bool
    {
        return $this->tamizaje !== null;
    }

    /**
     * Dato de identificación del colaborador (edad, sexo, funciones, tiempo).
     *
     * Angélica lo confirmó el 06/08/2026: "los datos que ya registró el
     * trabajador se arrastran tal cual, como los contestó en el tamizaje, no se
     * llenan aparte". Por eso la respuesta del cuestionario manda sobre
     * cualquier copia guardada en el caso; el valor propio solo aplica a los
     * casos capturados a mano, que no tienen tamizaje.
     */
    public function datoIdentificacion(string $campo): ?string
    {
        return $this->tamizaje?->{$campo} ?: $this->{$campo};
    }

    /**
     * El documento de Salud pide Medicina, Psicología, Psiquiatría y Otro como
     * casillas independientes, pero se guardan en un solo arreglo porque una
     * persona puede requerir varios. Estos accesores exponen cada casilla como
     * si fuera una columna propia, para poder editarlas en línea en la tabla.
     */
    public function getServicioMedicinaAttribute(): bool
    {
        return $this->requiereServicio('Medicina');
    }

    public function setServicioMedicinaAttribute($valor): void
    {
        $this->alternarServicio('Medicina', $valor);
    }

    public function getServicioPsicologiaAttribute(): bool
    {
        return $this->requiereServicio('Psicología');
    }

    public function setServicioPsicologiaAttribute($valor): void
    {
        $this->alternarServicio('Psicología', $valor);
    }

    public function getServicioPsiquiatriaAttribute(): bool
    {
        return $this->requiereServicio('Psiquiatría');
    }

    public function setServicioPsiquiatriaAttribute($valor): void
    {
        $this->alternarServicio('Psiquiatría', $valor);
    }

    public function getServicioOtroActivoAttribute(): bool
    {
        return $this->requiereServicio('Otro');
    }

    public function setServicioOtroActivoAttribute($valor): void
    {
        $this->alternarServicio('Otro', $valor);
    }

    private function requiereServicio(string $servicio): bool
    {
        return in_array($servicio, $this->servicios ?? [], true);
    }

    private function alternarServicio(string $servicio, $activo): void
    {
        $servicios = collect($this->servicios ?? []);

        $servicios = filter_var($activo, FILTER_VALIDATE_BOOLEAN)
            ? $servicios->push($servicio)->unique()
            : $servicios->reject(fn ($s) => $s === $servicio);

        $this->attributes['servicios'] = json_encode($servicios->values()->all());
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
