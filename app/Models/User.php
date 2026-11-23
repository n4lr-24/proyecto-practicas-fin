<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
            'rol' => RolUsuario::class,
        ];
    }

    public function codigosOtp(): HasMany
    {
        return $this->hasMany(CodigoOtp::class);
    }

    public function solicitudesReserva(): HasMany
    {
        return $this->hasMany(SolicitudReserva::class);
    }

    public function salasComoX(): HasMany
    {
        return $this->hasMany(SalaJuego::class, 'retador_x_id');
    }

    public function salasComoO(): HasMany
    {
        return $this->hasMany(SalaJuego::class, 'retador_o_id');
    }

    public function jugadas(): HasMany
    {
        return $this->hasMany(Jugada::class);
    }

    public function esRol(RolUsuario|string ...$roles): bool
    {
        $valores = array_map(
            fn (RolUsuario|string $rol) => $rol instanceof RolUsuario ? $rol->value : $rol,
            $roles
        );

        return in_array($this->rol->value, $valores, true);
    }
}
