<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * Trabajador social que da seguimiento a las gestiones de canalización.
 * Igual que Evaluador, vive sobre la tabla `users` acotado por `role`.
 */
class Gestor extends User
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('role_gestor', function (Builder $builder) {
            $builder->where('role', 'gestor');
        });

        static::creating(function ($model) {
            $model->role = 'gestor';
        });
    }

    public function getNombresAttribute()
    {
        return $this->name;
    }

    public function setNombresAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getCorreoAttribute()
    {
        return $this->email;
    }

    public function setCorreoAttribute($value)
    {
        $this->attributes['email'] = $value;
    }
}
