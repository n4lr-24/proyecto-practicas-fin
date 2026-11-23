<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaJuego extends Model
{
    protected $table = 'salas_juego';

    protected $fillable = [
        'retador_x_id',
        'retador_o_id',
        'turno',
        'casillas',
        'estado',
        'ganador_id',
    ];

    protected function casts(): array
    {
        return [
            'casillas' => 'array',
        ];
    }

    public function retadorX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retador_x_id');
    }

    public function retadorO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retador_o_id');
    }

    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }

    public function jugadas(): HasMany
    {
        return $this->hasMany(Jugada::class, 'sala_id');
    }

    public function esParticipante(int $userId): bool
    {
        return $this->retador_x_id === $userId || $this->retador_o_id === $userId;
    }

    public function marcaDeUsuario(int $userId): ?string
    {
        if ($this->retador_x_id === $userId) {
            return 'X';
        }

        if ($this->retador_o_id === $userId) {
            return 'O';
        }

        return null;
    }
}
