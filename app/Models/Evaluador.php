<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Evaluador extends User
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('role_evaluador', function (Builder $builder) {
            $builder->where('role', 'evaluador');
        });

        static::creating(function ($model) {
            $model->role = 'evaluador';
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
