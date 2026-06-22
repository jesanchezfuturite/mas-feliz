<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'herramientas_empresa_activas'];

    protected $casts = [
        'herramientas_empresa_activas' => 'boolean',
    ];
}
