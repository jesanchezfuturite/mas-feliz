<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Empresa extends Model implements Authenticatable, HasName, CanResetPassword, FilamentUser
{
    use AuthenticatableTrait, Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'folio',
        'token_tamizaje',
        'nombre_empresa',
        'rfc',
        'ambito',
        'domicilio',
        'municipio',
        'dias_horario_servicio',
        'nombre_director',
        'nombre_responsable',
        'cargo_enlace',
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
     * Get the email address where password reset links are sent.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotificationEs($token));
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                if ($column === 'email') {
                    $column = 'correo';
                }
                return parent::where($column, $operator, $value, $boolean);
            }
        };
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        return $this->correo;
    }

    /**
     * Get the name of the user for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->nombre_empresa;
    }

    /**
     * Get the relative path to the generated PDF Distinctive.
     */
    public function getRutaPdfAttribute(): ?string
    {
        $latest = $this->autoevaluaciones()->latest()->first();
        return $latest ? ($latest->respuestas['pdf_distintivo'] ?? null) : null;
    }

    /**
     * Get the evaluation status compatible with Filament's Dictaminado status expectation.
     */
    public function getEstatusAttribute(): ?string
    {
        if (in_array($this->estatus_distintivo, ['Validado', 'Aprobado'])) {
            return 'Dictaminado';
        }
        return $this->estatus_distintivo;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'empresa';
    }
}
