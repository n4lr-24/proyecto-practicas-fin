<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_user_id',
        'estudiante_nombre',
        'unidad',
        'inasistencias',
        'calificacion',
        'equivalencia',
    ];

    protected function casts(): array
    {
        return [
            'unidad' => 'integer',
            'inasistencias' => 'integer',
            'calificacion' => 'decimal:2',
        ];
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alumno_user_id');
    }

    public static function obtenerEquivalencia(float $score): string
    {
        return match (true) {
            $score < 8.0 => 'NA',
            $score < 9.0 => 'SA',
            $score < 9.5 => 'DE',
            default => 'AU',
        };
    }
}
