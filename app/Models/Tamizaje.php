<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tamizaje extends Model
{
    protected $guarded = [];

    /**
     * Get the company that owns the screening.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
