<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaJogoParticipante extends Model
{
    protected $fillable = [
        'pelada_jogo_id',
        'user_id',
        'pelada_membro_id',
        'tipo',
        'status',
        'posicao_fila',
        'confirmado_em',
        'cancelado_em',
    ];

    protected $casts = [
        'confirmado_em' => 'datetime',
        'cancelado_em' => 'datetime',
    ];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
