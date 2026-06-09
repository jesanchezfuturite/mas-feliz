<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoSeguimiento extends Model
{
    protected $guarded = [];

    /**
     * Get the company that owns this case.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
