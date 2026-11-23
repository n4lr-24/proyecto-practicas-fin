<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoOtp extends Model
{
    protected $table = 'codigos_otp';

    protected $fillable = [
        'user_id',
        'codigo',
        'intentos',
        'expira_en',
    ];

    protected function casts(): array
    {
        return [
            'intentos' => 'integer',
            'expira_en' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estaExpirado(): bool
    {
        return $this->expira_en->isPast();
    }
}
