<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'password', 'apellidos', 'telefono', 'estatus', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estatus' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin' && $this->estatus === true;
        }

        if ($panel->getId() === 'evaluador') {
            return $this->role === 'evaluador' && $this->estatus === true;
        }

        return $this->estatus === true;
    }

    public function empresas(): BelongsToMany
    {
        // Pivote fijado explícitamente para que la relación funcione igual
        // desde User y desde la subclase Evaluador (que de lo contrario
        // infiere 'empresa_evaluador'/'evaluador_id' en vez de 'empresa_user'/'user_id').
        return $this->belongsToMany(Empresa::class, 'empresa_user', 'user_id', 'empresa_id');
    }
}
