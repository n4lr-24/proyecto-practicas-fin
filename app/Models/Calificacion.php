<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    public const UMBRAL_SA = 8.0;

    public const UMBRAL_DE = 9.0;

    public const UMBRAL_AU = 9.5;

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
            $score < self::UMBRAL_SA => 'NA',
            $score < self::UMBRAL_DE => 'SA',
            $score < self::UMBRAL_AU => 'DE',
            default => 'AU',
        };
    }
}
