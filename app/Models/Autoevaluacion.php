<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autoevaluacion extends Model
{
    protected $table = 'autoevaluacions';

    protected $guarded = [];

    protected $casts = [
        'respuestas' => 'array',
    ];

    /**
     * Get the company that owns the self-evaluation.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
