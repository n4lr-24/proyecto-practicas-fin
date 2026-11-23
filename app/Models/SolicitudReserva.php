<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudReserva extends Model
{
    protected $table = 'solicitudes_reserva';

    protected $fillable = [
        'user_id',
        'computadora_id',
        'fecha',
        'inicio',
        'fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function computadora(): BelongsTo
    {
        return $this->belongsTo(Computadora::class, 'computadora_id');
    }
}
