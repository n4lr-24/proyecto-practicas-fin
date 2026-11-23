<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jugada extends Model
{
    protected $table = 'jugadas';

    protected $fillable = [
        'sala_id',
        'user_id',
        'marca',
        'posicion',
    ];

    protected function casts(): array
    {
        return [
            'posicion' => 'integer',
        ];
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(SalaJuego::class, 'sala_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
