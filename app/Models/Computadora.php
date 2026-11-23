<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Computadora extends Model
{
    protected $table = 'computadoras';

    protected $fillable = [
        'clave_equipo',
        'etiqueta',
        'sala',
        'caracteristicas',
        'estado',
    ];

    public function solicitudesReserva(): HasMany
    {
        return $this->hasMany(SolicitudReserva::class);
    }
}
