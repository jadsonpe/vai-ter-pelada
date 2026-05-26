<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEsportePerfil extends Model
{
    protected $table = 'user_esporte_perfis';

    protected $fillable = [
        'user_id',
        'esporte_id',
        'posicao',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }
}
