<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialApoyo extends Model
{
    protected $fillable = [
        'titulo',
        'tipo',
        'archivo_path',
        'enlace_url',
        'seccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
