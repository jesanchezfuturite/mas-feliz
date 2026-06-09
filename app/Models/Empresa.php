<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Empresa extends Model implements Authenticatable, HasName
{
    use AuthenticatableTrait, Notifiable;

    protected $fillable = [
        'folio',
        'token_tamizaje',
        'nombre_empresa',
        'municipio',
        'dias_horario_servicio',
        'nombre_director',
        'nombre_responsable',
        'correo',
        'password',
        'telefono',
        'rubro',
        'numero_trabajadores',
        'estatus_distintivo',
        'nivel_madurez_asignado',
        'retroalimentacion_gobierno',
        'fecha_dictamen',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Empresa $empresa) {
            $latest = static::query()
                ->where('folio', 'like', 'MF-2026-%')
                ->orderBy('folio', 'desc')
                ->lockForUpdate() // Lock to prevent race conditions during concurrent creation
                ->first();

            if ($latest) {
                $parts = explode('-', $latest->folio);
                $lastNumber = isset($parts[2]) ? (int) $parts[2] : 0;
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $empresa->folio = 'MF-2026-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $empresa->token_tamizaje = \Illuminate\Support\Str::random(32);
        });
    }

    /**
     * Get the self-evaluations for the company.
     */
    public function autoevaluaciones()
    {
        return $this->hasMany(Autoevaluacion::class);
    }

    /**
     * Get the screening results for the company.
     */
    public function tamizajes()
    {
        return $this->hasMany(Tamizaje::class);
    }

    /**
     * Get the clinical tracking cases for the company.
     */
    public function casosSeguimiento()
    {
        return $this->hasMany(CasoSeguimiento::class);
    }

    /**
     * Get the name of the user for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->nombre_empresa;
    }
}
